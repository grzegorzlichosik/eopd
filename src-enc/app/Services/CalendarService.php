<?php

namespace App\Services;

use App\Connectors\NylasConnector;
use App\Exceptions\OAuthConnectionException;
use App\Exceptions\UnauthorisedException;
use App\Models\Encounter;
use App\Models\Organisation;
use App\Models\Place;
use App\Services\OAuth\OAuthNylasService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Nylas\Client;
use Carbon\Carbon;
use App\Models\User;
use App\Services\Traits\StartAndEndDates;

class CalendarService extends NylasService
{
    use StartAndEndDates;

    protected Client $nylasClient;

    protected OAuthNylasService $oAuthService;

    public function __construct(NylasConnector $nylasConnector, OAuthNylasService $oAuthService)
    {
        $this->nylasClient = $nylasConnector->client;
        $this->oAuthService = $oAuthService;
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getAllCalendars(User $user): ?object
    {
        if ($user->nylas_access_token) {
            $params = [
                'limit'  => 10,
                'offset' => 0,
            ];
            return $this->oAuthService->getAuthResponse('/calendars', 'get', $params, $user->nylas_access_token);
        }
        return null;
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function setPrimaryCalendar(User $user): ?string
    {
        if (!$user->nylas_primary_calendar_id) {
            $calendars = $this->getAllCalendars($user);
            if ($calendars) {
                $primary = collect($calendars)
                    ->where('is_primary', true)
                    ->first();
                if ($primary) {
                    $user->nylas_primary_calendar_id = $primary->id;
                    $user->save();
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }

        return $user->nylas_primary_calendar_id;
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getCalendarEvents(User $user, Carbon $startDate, Carbon $endDate): ?object
    {
        if ($user->nylas_access_token) {
            $params = [
                'calendar_id'  => $user->nylas_primary_calendar_id,
                'starts_after' => $startDate->unix(),
                'ends_before'  => $endDate->unix(),
            ];
            return $this->oAuthService->getAuthResponse('/events', 'get', $params, $user->nylas_access_token);
        }
        return null;
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getEventsCollection(array $options, User $user): ?Collection
    {
        $primaryCalendarId = $this->setPrimaryCalendar($user);

        if (!$primaryCalendarId) {
            throw new UnauthorisedException();
        }

        [$startDate, $endDate] = self::getStartAndEndDates($options);
        $events = $this->getCalendarEvents($user, $startDate, $endDate);

        return collect($events)->map(function ($event) {
            return [
                'event_id' => $event->calendar_id,
                'title' => $event->title,
                'start' => Carbon::parse($event->when->start_time)->toDateTimeLocalString(),
                'end' => Carbon::parse($event->when->end_time)->toDateTimeLocalString(),
            ];
        });
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function getAllEventsCollection(string $uuid, array $options): Collection
    {

        $calendar = Encounter::where('uuid', $uuid)
            ->where('organisations_id', auth()->user()->organisations_id)
            ->with(['agent', 'place', 'organisation'])
            ->first();

        [$startDate, $endDate] = self::getStartAndEndDates($options);

        $params = [
            'start_time' => $startDate->unix(),
            'end_time'   => $endDate->unix(),
            'emails'     => [
                $calendar->agent->office_365_email_id,
                $calendar->place->email,
            ]
        ];

        $response = $this->oAuthService->getAuthResponse(
            '/calendars/free-busy',
            'post',
            $params,
            $calendar->agent->nylas_access_token
        );

        return collect($response)->map(function ($calendar) {
            return collect($calendar->time_slots)->map(function ($event) {
                return [
                    'start' => Carbon::parse($event->start_time)->toDateTimeLocalString(),
                    'end'   => Carbon::parse($event->end_time)->toDateTimeLocalString(),
                ];
            });
        })->flatten(1);

    }

}
