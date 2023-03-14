<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\JsonResponse;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\TsmState;
use App\Models\User;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\EncounterDetailsService;

class ScheduleManagementController extends Controller
{

    use JsonResponse;

    public function __construct(
        protected CalendarService         $service,
        protected EncounterDetailsService $encounterDetailsService
    )
    {
    }

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/EncountersScheduled', [
            'encounters'   => $this->encounterDetailsService->encounters(Encounter::STATE_SCHEDULED, null, 1, null),
            'flows'        => Flow::getFlows(),
            'agents'       => User::getAgents(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters(),
        ]);
    }

    public function completed(): \Inertia\Response
    {
        return Inertia::render('Admin/EncountersCompleted', [
            'encounters'   => $this->encounterDetailsService->encounters(Encounter::STATE_FINISHED, null, 1, null),
            'flows'        => Flow::getFlows(),
            'agents'       => User::getAgents(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters()
        ]);
    }

    public function allEncounters(): \Inertia\Response
    {
        return Inertia::render('Admin/Encounters', [
            'encounters'   => $this->encounterDetailsService->encounters('', null, 1, null),
            'flows'        => Flow::getFlows(),
            'agents'       => User::getAgents(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters()
        ]);
    }

    public function cancelled(): \Inertia\Response
    {
        return Inertia::render('Admin/EncountersCancelled', [
            'encounters'   => $this->encounterDetailsService->encounters(Encounter::STATE_CANCELLED, null, 1, null),
            'flows'        => Flow::getFlows(),
            'agents'       => User::getAgents(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters()
        ]);
    }

    public function show(string $uuid): \Inertia\Response
    {
        $encounter = Encounter::where('uuid', $uuid)
            ->where('organisations_id', Auth::user()->organisations_id)
            ->with(['flow', 'agent', 'place', 'attendees'])
            ->withCount('attendees')
            ->first();

        $location = null;
        if ($encounter->place) {
            $location = Location::where('id', $encounter->place->locations_id)
                ->first();
        }
        if ($encounter->tsm_current_state === Encounter::STATE_CANCELLED) {
            $encounter->tsm_current_state = trans('tsmstates.cancelled');

        } elseif ($encounter->tsm_current_state === Encounter::STATE_FINISHED) {
            $encounter->tsm_current_state = trans('tsmstates.finished');

        } elseif (in_array($encounter->tsm_current_state, [
                Encounter::STATE_PENDING_MANUAL_RE_SCHEDULING,
                Encounter::STATE_PENDING_AUTO_RE_SCHEDULING
            ]
        )) {
            $encounter->tsm_current_state = trans('tsmstates.attention');

        } elseif ($encounter->tsm_current_state === Encounter::STATE_SCHEDULED) {
            $confirmed = $encounter->attendees->where('is_accepted', 1)->count();
            if ($confirmed >= 1) {
                $encounter->tsm_current_state = trans('tsmstates.confirmed');
            } else {
                $encounter->tsm_current_state = trans('tsmstates.awaiting');
            }
        }

        return Inertia::render('Admin/Partials/ScheduleManagementDetail', [
            'encounter' => $encounter,
            'location'  => $location,
        ]);

    }

    public function searchFilters(): array
    {
        return [
            'searchValue' => request()->input('search'),

            'searchFlows' => request()->input('flows') ?
                Flow::where('uuid', request()->input('flows'))->first() : '',

            'searchAgents' => request()->input('agent') ?
                User::where('uuid', request()->input('agent'))->first() : '',

            'searchPlaces' => request()->input('place') ?
                Place::where('uuid', request()->input('place'))->first() : '',

            'searchLocations' => request()->input('location') ?
                Location::where('uuid', request()->input('location'))->first() : '',

            'searchChannels' => Channel::getChannels(),

            'searchStatus' => request()->input('currentState') ? TsmState::where('class', 'Encounter')
                ->where('name', request()->input('currentState'))->first() : '',
        ];
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function calendar(string $uuid, Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->respond(function () use ($request, $uuid) {
            return $this->service->getAllEventsCollection($uuid, $request->input());
        });
    }
}
