<?php

namespace App\Services;

use App\Connectors\NylasConnector;
use App\Exceptions\OAuthConnectionException;
use App\Exceptions\UnauthorisedException;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\File;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\TsmState;
use App\Notifications\SendIcsFile;
use App\Services\OAuth\OAuthNylasService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Nylas\Client;
use Carbon\Carbon;
use App\Models\User;
use App\Services\Traits\StartAndEndDates;

class EncounterDetailsService extends NylasService
{
    private const PLACE_LOCATION = 'place.location';
    private const CHANNEL_TYPE = 'channel.type';

    public function encounters(?string $status, ?int $flowId, ?int $paginate, ?int $agentId)
    {
        $organisationId = auth()->user()->organisations_id;
        list($sortField, $sortOrder) = getTableSorting('scheduled_at');

        $searchQueries = self::search($agentId);

        $encounters = Encounter::where('organisations_id', $organisationId)
            ->withAggregate('flow', 'name')
            ->withAggregate('agent', 'name')
            ->withAggregate('agent', 'email')
            ->withAggregate('place', 'name')
            ->withAggregate('place', 'email')
            ->withAggregate('flow', 'objective')
            ->withCount('attendees')
            ->with('channel')
            ->with('channel_type')
            ->with('place')
            ->with(self::CHANNEL_TYPE)
            ->with([
                    'attendees',
                    self::PLACE_LOCATION
                ]
            )
            ->withCount('attendees')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('uuid', 'like', '%' . $search . '%')
                        ->orWhereHas('place', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas(self::PLACE_LOCATION, function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('flow', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('agent', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('attendees', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });

                });
            })
            ->when($searchQueries['flows'], function ($query, $flow) {
                return $query->where('flows_id', $flow->id);
            })
            ->when($searchQueries['agents'], function ($query, $agent) {
                return $query->where('agent_id', $agent->id);
            })
            ->when($searchQueries['places'], function ($query, $place) {
                return $query->where('places_id', $place->id);
            })
            ->when($searchQueries['locations'], function ($query, $location) {
                return $query->whereHas(self::PLACE_LOCATION, function ($query2) use ($location) {
                    return $query2->where('id', $location->id);
                });
            })
            ->when($flowId, function ($query, $id) {
                return $query->where('flows_id', $id);
            })
            ->when($searchQueries['channelTypes'], function ($query, $channelType) {
                    return $query->where('channel_types_id', $channelType->id);

            })
            ->when($searchQueries['currentState'], function ($query, $state) {
                return $query->where('tsm_current_state', $state->name);
            })
            ->when(request()->input('start'), function ($query, $date) {
                return $query->where('scheduled_at', '>=', $date);
            })
            ->when(request()->input('end'), function ($query, $date) {
                return $query->where('scheduled_at', '<=', $date);
            })
            ->when($agentId, function ($query, $agent) {
                return $query->where('agent_id', $agent);
            })
            ->when($status, function ($query, $state) {
                $qry = null;
                switch ($state) {
                    case (Encounter::STATE_FINISHED):
                        $qry = $query->where('tsm_current_state', Encounter::STATE_FINISHED);
                        break;
                    case (Encounter::STATE_CANCELLED):
                        $qry = $query->where('tsm_current_state', Encounter::STATE_CANCELLED);
                        break;
                    case (Encounter::STATE_SCHEDULED):
                        $qry = $query->whereIn('tsm_current_state', [
                            Encounter::STATE_SCHEDULED,
                            Encounter::STATE_PENDING_AUTO_RE_SCHEDULING,
                            Encounter::STATE_PENDING_MANUAL_RE_SCHEDULING
                        ]);
                        break;
                    default:
                }
                return $qry;
            })

            ->when($paginate, function ($query, $page) use ($sortField, $sortOrder) {
                $query = $query->orderBy($sortField, $sortOrder);
                if ($page) {
                    $query = $query->paginate(20)
                        ->appends(cleanupAppends(request()->input()));
                }
                return $query;
            });

        if ($paginate) {
            collect($encounters->items())->map(function ($encounter) {
                $encounter->channel_types_name = trans('channels.' . $encounter->channel_type?->label);
                $encounter->location = $encounter->place?->location?->name;
                if ($encounter->place?->location?->files_id) {
                    $file= File::find($encounter->place?->location?->files_id);
                    $encounter->location_file = $file->uuid;
                }
                $encounter->main_attendee = $encounter->attendees->where('is_original', 1)->first();
                switch ($encounter->tsm_current_state) {

                    case (Encounter::STATE_CANCELLED):
                        $encounter->tsm_current_state = trans('tsmstates.cancelled');
                        break;
                    case (Encounter::STATE_FINISHED):
                        $encounter->tsm_current_state = trans('tsmstates.finished');
                        break;
                    case (Encounter::STATE_SCHEDULED):
                        $confirmed = $encounter->attendees->where('is_accepted', 1)->count();
                        $encounter->tsm_current_state = trans('tsmstates.awaiting');
                        if ($confirmed >= 1) {
                            $encounter->tsm_current_state = trans('tsmstates.confirmed');
                        }
                        break;
                    case (Encounter::STATE_INITIAL):
                        $encounter->tsm_current_state = trans('tsmstates.initial');
                        break;
                    default:
                        $encounter->tsm_current_state = trans('tsmstates.attention');
                }
                $encounter->unsetRelation('place');

                return $encounter;
            });
        }

        return  $encounters;
    }

    public static function search(?int $agentId): array
    {
        $search = [
            'flows'         => request()->input('flows') ?
                Flow::where('uuid', request()->input('flows'))->first() : '',

            'agents'        => request()->input('agent') ?
                User::where('uuid', request()->input('agent'))->first() : '',

            'places'        => request()->input('place') ?
                Place::where('uuid', request()->input('place'))->first() : '',

            'locations'     => request()->input('location') ?
                Location::where('uuid', request()->input('location'))->first() : '',

            'channelTypes'  => request()->input('channel') ?
                ChannelType::where('uuid', request()->input('channel'))->first() : '',

            'currentState'  => request()->input('currentState') ?
                TsmState::where('class', 'Encounter')->where('name', request()->input('currentState'))->first() :
                ''
        ];

        if ($search['currentState'] === '' && $agentId !== null) {
            $search['currentState'] =  TsmState::where('class', 'Encounter')
                ->where('name', Encounter::STATE_SCHEDULED)->first();
        }
        return $search;
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function encounterCalendarDetails(string $uuid): Encounter
    {
        $encounter = Encounter::where('uuid', $uuid)
            ->withAggregate('organisation', 'name')
            ->withAggregate('organisation', 'phone_number')
            ->withAggregate('flow', 'name')
            ->withAggregate('flow', 'objective')
            ->withAggregate('flow', 'uuid')
            ->withAggregate('place', 'name')
            ->with('channel')
            ->with('channel_type')
            ->with(self::CHANNEL_TYPE)
            ->with([
                    'attendees',
                    'place.location'
                ]
            )
            ->withCount('attendees')
            ->firstorFail();
        $encounter->channel_types_name = trans('channels.' . $encounter->channel_type?->label);
        $encounter->location_details = $encounter->place?->location;
        if ($encounter->place?->location?->files_id) {
            $file= File::find($encounter->place?->location?->files_id);
            $encounter->location_file = $file->uuid;
        }
        $encounter->main_attendee = $encounter->attendees->where('is_original', 1)->first();
        return $encounter;
    }

}
