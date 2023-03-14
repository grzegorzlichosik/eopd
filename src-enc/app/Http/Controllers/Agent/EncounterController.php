<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Traits\JsonResponse;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\TsmState;
use App\Services\CalendarService;
use App\Services\EncounterDetailsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EncounterController extends Controller
{
    use JsonResponse;

    public function __construct(
        protected CalendarService $service,
        protected EncounterDetailsService $encounterDetailsService
    )
    {
    }

    public function allEncounters(): \Inertia\Response
    {
        return Inertia::render('Agent/Encounters', [
            'encounters'   => $this->encounterDetailsService->encounters(
                '',
                null,
                1,
                auth()->id()
            ),
            'flows'        => Flow::getFlows(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters()
        ]);
    }

    public function calendar(): \Inertia\Response
    {
        $events = ($this->encounterDetailsService->encounters('', null, 0, Auth::user()->id))
            ->get();

        $encounters =  collect($events)->map(function ($event) {

            return [
                'event_id' => $event->uuid,
                'title'    => $event->flow_name,
                'start'    => Carbon::parse($event->scheduled_at)->toDateTimeLocalString(),
                'end'      => Carbon::parse($event->ends_at)->toDateTimeLocalString(),
            ];
        });

        return Inertia::render('Agent/Calendars', [
            'encounters'   => $encounters,
            'flows'        => Flow::getFlows(),
            'places'       => Place::getPlaces(),
            'locations'    => Location::getLocations(),
            'channelTypes' => ChannelType::channelTypes(),
            'status'       => TsmState::encounterStatus(),
            'searchFilter' => $this->searchFilters()
        ]);
    }

    public function show(string $uuid): Encounter
    {
        return  $this->encounterDetailsService->encounterCalendarDetails($uuid);

    }

    public function searchFilters(): array
    {
        return [
            'searchValue'     => request()->input('search'),

            'searchFlows'     => request()->input('flows') ?
                Flow::where('uuid', request()->input('flows'))->first() : '',

            'searchPlaces'    => request()->input('place') ?
                Place::where('uuid', request()->input('place'))->first() : '',

            'searchLocations' => request()->input('location') ?
                Location::where('uuid', request()->input('location'))->first() : '',

            'searchChannels'  => Channel::getChannels(),

            'searchStatus'    => request()->input('currentState') ? TsmState::where('class', 'Encounter')
                ->where('name', request()->input('currentState'))->first() : TsmState::where('class', 'Encounter')
                ->where('name', Encounter::STATE_SCHEDULED)->first(),
        ];
    }
}
