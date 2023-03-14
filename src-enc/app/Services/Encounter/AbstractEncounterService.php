<?php

namespace App\Services\Encounter;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\User;
use App\Notifications\EncounterInviteeIcsFile;
use App\Notifications\EncounterReschedulePlaceIcsFile;
use App\Notifications\EncounterSendAgentIcsFile;
use App\Notifications\EncounterSendCancelledIcs;
use App\Notifications\EncounterSendPlaceIcsFile;
use App\Notifications\EncounterSendRescheduledIcs;
use App\Services\OAuth\OAuthNylasService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class AbstractEncounterService
{
    public const DELAY = 30;

    public function __construct(
        protected OAuthNylasService $oAuthService
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStartEndDates(Carbon $date): array
    {
        $startDate = $date->clone()->isToday() ? now() : $date->clone()->set('hour', '07:00');
        $endDate = $date->clone()->set('hour', '19:00');
        return array($startDate, $endDate);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAgentsEmails(Flow $flow): ?\Illuminate\Support\Collection
    {
        return $flow->users
            ->reject(
                fn($user) => !$user->nylas_access_token
                    ||
                    !$user->is_agent
                    ||
                    !$user->email_verified_at
                    ||
                    !$user->nylas_primary_calendar_id
            )
            ->map(function ($user) {
                return [
                    'account_id'   => $user->nylas_account_id,
                    'calendar_id'  => $user->nylas_primary_calendar_id,
                    'access_token' => $user->nylas_access_token,
                    'email'        => $user->office_365_email_id,
                    'name'         => $user->name,
                    'uuid'         => $user->uuid,
                ];
            });
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPlacesEmails(Channel $channel): ?\Illuminate\Support\Collection
    {
        $placesEmails = collect([]);
        if ($channel->places->isNotEmpty()) {
            $placesEmails = $channel->places
                ->reject(fn($item) => $item->tsm_current_state !== Place::STATE_ACTIVE)
                ->map(
                    fn($item) => [
                        'email' => $item->email,
                        'uuid'  => $item->uuid,
                    ]
                );
        }
        return $placesEmails;
    }

    /**
     * @codeCoverageIgnore
     */
    public function createEvent(array $params, string $token): ?object
    {
        return $this->oAuthService->getAuthResponse(
            '/events?notify_participants=false',
            'post',
            $params,
            $token
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getEvent(string $id, string $token): ?array
    {
        return json_decode(
            json_encode(
                $this->oAuthService->getAuthResponse(
                    '/events/' . $id,
                    'get',
                    [],
                    $token
                )
            ),
            true
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function deleteEvent(string $id, string $token): ?object
    {
        return $this->oAuthService->getAuthResponse(
            '/events/' . $id . '?notify_participants=false',
            'delete',
            [],
            $token
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function updateEvent(string $id, array $params, string $token): ?object
    {
        return $this->oAuthService->getAuthResponse(
            '/events/' . $id . '?notify_participants=false',
            'put',
            $params,
            $token
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPrimaryCalendar(Organisation $organisation): ?string
    {
        if (!$organisation->master_calendar_nylas_primary_calendar_id) {
            $params = [
                'limit'  => 10,
                'offset' => 0,
            ];

            $calendars = $this->oAuthService->getAuthResponse(
                '/calendars',
                'get',
                $params,
                $organisation->master_calendar_nylas_access_token
            );

            if ($calendars) {
                $primary = collect($calendars)
                    ->where('is_primary', true)
                    ->first();
                if ($primary) {
                    $organisation->master_calendar_nylas_primary_calendar_id = $primary->id;
                    $organisation->save();
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }

        return $organisation->master_calendar_nylas_primary_calendar_id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function notifyCancellation(Encounter $encounter, array $event, array $participants): void
    {
        Notification::route('mail', $participants[0]['email'])
            ->notify(new EncounterSendCancelledIcs($encounter, $event, $participants));
    }

    /**
     * @codeCoverageIgnore
     */
    public function notifyNewBookingAgent(Encounter $encounter, User $agent, array $participants): void
    {
        Notification::route('mail', $agent->office_365_email_id)
            ->notify(
                (
                new EncounterSendAgentIcsFile(
                    $encounter,
                    $participants,
                    $agent
                )
                )
                    ->delay(now()->addSeconds(self::DELAY))
            );
    }

    /**
     * @codeCoverageIgnore
     */
    public function notifyNewBookingPlace(Encounter $encounter, User $agent, array $participants): void
    {
        Notification::route('mail', $participants[0]['email'])
            ->notify(
                (
                new EncounterSendPlaceIcsFile(
                    $encounter,
                    $participants,
                    $agent
                )
                )
                    ->delay(now()->addSeconds(self::DELAY))
            );
    }


    /**
     * @codeCoverageIgnore
     */
    public function notifyNewBookingAttendee(Encounter $encounter, array $participants): void
    {
        Notification::route('mail', $participants[0]['email'])
            ->notify(
                (
                new EncounterInviteeIcsFile(
                    $encounter,
                    $participants
                )
                )
                    ->delay(now()->addSeconds(self::DELAY))
            );
    }

}
