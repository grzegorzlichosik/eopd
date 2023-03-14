<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\PlaceType;
use App\Models\Pool;
use App\Models\TsmState;
use App\Models\User;
use App\Services\NylasService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Validators\AddAgentValidator;
use App\Services\EncounterDetailsService;

class FlowDetailsController extends Controller
{

    public function __construct(
        protected EncounterDetailsService $encounterDetailsService
    )
    {
    }

    public function agents(string $uuid): \Inertia\Response
    {
        $poolsById = Pool::where('organisations_id', auth()->user()->organisations_id)
            ->pluck('name', 'id');

        $flow = $this->getFlowDetails($uuid)->first();

        $flowChannelTypes = $flow?->channels?->pluck('channel_types_id')->toArray();

        $agents = [];
        foreach ($flow->users as $user) {
            $userChannels = $user->channels()
                ->where('channels_users_map.flows_id', $flow->id)
                ->pluck('channel_types_id')
                ->toArray();

            $agents[] = [
                'uuid'         => $user['uuid'],
                'flowUuid'     => $flow->uuid,
                'name'         => $user['name'],
                'email'        => $user['email'],
                'pool_name'    => $poolsById[$user->pivot->pools_id],
                'face_to_face' => in_array(ChannelType::F2F, $userChannels),
                'web'          => in_array(ChannelType::WEB, $userChannels),
                'phone'        => in_array(ChannelType::PHONE, $userChannels),
            ];
        }

        $flow->f2f = in_array(ChannelType::F2F, $flowChannelTypes);
        $flow->web = in_array(ChannelType::WEB, $flowChannelTypes);
        $flow->phone = in_array(ChannelType::PHONE, $flowChannelTypes);

        collect($flow->channels)->map(function ($channel) {
            unset($channel->places);
            return $channel;
        });

        $flow->unsetRelation('users');
        $flow->unsetRelation('channelUsers');

        return Inertia::render('Admin/Partials/FlowDetail', [
            'flow'         => $flow,
            'agents'       => $agents,
            'channelTypes' => $this->channelTypes(),
        ]);
    }

    public function resources(string $uuid): \Inertia\Response
    {
        $flow = $this->getFlowDetails($uuid)->first();
        $placesTypesById = PlaceType::pluck('label', 'id');
        $f2fChannel = $this->getResources($flow);

        $resources = $f2fChannel?->places->map(function ($item) use ($placesTypesById, $f2fChannel) {
            return [
                'uuid'        => $item->uuid,
                'name'        => $item->name,
                'type'        => trans('place_types.' . $placesTypesById[$item->place_types_id]),
                'location'    => $item?->location?->name,
                'is_active'   => $item->is_active,
                'channelUuid' => $f2fChannel->uuid,
            ];
        })
            ->sortBy('name');

        $flow->unsetRelation('users');
        $flow->unsetRelation('channelUsers');

        collect($flow->channels)->map(function ($channel) {
            unset($channel->places);
            return $channel;
        });

        return Inertia::render('Admin/Partials/FlowResources', [
            'flow'         => $flow,
            'resources'    => $resources,
            'channelTypes' => $this->channelTypes(),
        ]);

    }

    public function encounters(string $uuid, Request $request): \Inertia\Response
    {
        $flow = $this->getFlowDetails($uuid)->first();
        $flowChannelTypes = $flow->channels->pluck('channel_types_id')->toArray();

        $channels = request()->input('channel') ?
            ChannelType::where('uuid', request()->input('channel'))->first() : '';
        if ($channels) {
            $channels->name = trans('channels.' . $channels->label);
        }

        $flow->f2f = in_array(ChannelType::F2F, $flowChannelTypes);

        return Inertia::render('Admin/Partials/FlowEncounters', [
            'flow'         => $flow,
            'encounters'   => $this->encounterDetailsService->encounters('', $flow->id, 1, null),
            'agents'       => User::getAgents(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => [
                'searchValue'     => request()->input('search'),
                'searchAgents'    => request()->input('agent') ?
                    User::where('uuid', request()->input('agent'))->first() : '',
                'searchPlaces'    => request()->input('place') ?
                    Place::where('uuid', request()->input('place'))->first() : '',
                'searchLocations' => request()->input('location') ?
                    Location::where('uuid', request()->input('location'))->first() : '',
                'searchChannels'  => $channels,
                'searchStatus'    => request()->input('currentState') ?
                    TsmState::where('class', 'Encounter')
                        ->where('name', request()->input('currentState'))->first() : '',
            ],
            'channelTypes' => $this->channelTypes(),
        ]);
    }

    private function getResources(Flow $flow): ?Channel
    {
        return $flow->channels->where('channel_types_id', ChannelType::F2F)->first();
    }

    public function channelTypes(): Collection
    {
        return ChannelType::get()
            ->map(function ($item) {
                return
                    [
                        'name' => trans('channels.' . $item['label']),
                        'uuid' => $item['uuid']
                    ];
            });
    }

    public function enableResource(Flow $flow, int $channelType): bool
    {
        $enableResources = 0;
        foreach ($flow->channels as $item) {
            if ($item->channel_types_id === $channelType) {
                $enableResources = 1;
            }
        }
        return $enableResources;
    }

    private function getFlowDetails(string $uuid): Builder
    {
        return Flow::where('organisations_id', auth()->user()->organisations_id)
            ->where('uuid', $uuid)
            ->with(
                [
                    'channels.places',
                    'users',
                    'channelUsers',
                    'channels' => function ($query) {
                        $query->with(
                            [
                                'places',
                                'type:id,uuid'
                            ]
                        );
                    },
                    'channels.type'
                ]
            );
    }

}
