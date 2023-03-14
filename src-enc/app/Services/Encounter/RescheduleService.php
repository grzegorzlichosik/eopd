<?php

namespace App\Services\Encounter;

use App\Models\Encounter;
use App\Models\Place;
use App\Models\User;
use App\Notifications\EncounterReschedulePlaceIcsFile;
use App\Notifications\EncounterSendRescheduledIcs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class RescheduleService extends AbstractEncounterService
{
    /**
     * @codeCoverageIgnore
     */
    public function rescheduleBooking(Encounter $encounter, array $data): Encounter
    {
        $organisation = $encounter->organisation()->first();
        $authToken = $organisation->master_calendar_nylas_access_token;
        $event = $this->getEvent($encounter->external_id, $authToken);
        $originalEncounter = $encounter->replicate();

        $place = null;
        if (!empty($data['date_time']['rooms'])) {
            $place = Place::whereUuidIn($data['date_time']['rooms'])
                ->where('organisations_id', $organisation->id)
                ->with('location')
                ->get()
                ->random();
        }

        $agent = null;
        if (!empty($data['date_time']['users'])) {
            $agent = User::whereUuidIn($data['date_time']['users'])
                ->where('organisations_id', $organisation->id)
                ->get()
                ->random();
        }

        $requestor = $encounter->attendees->where('is_original', 1)->first();

        $startDate = Carbon::parse($data['date_time']['start']);
        $endDate = Carbon::parse($data['date_time']['end']);
        $eventTitle = 'Meeting ' . $organisation->name . ': ' . $encounter->flow->name;

        $params = [
            'title'        => $eventTitle,
            'location'     => $place ? $place->name : $encounter->channel->type->name,
            'busy'         => true,
            'read_only'    => true,
            'when'         => [
                'start_time' => $startDate->unix(),
                'end_time'   => $endDate->unix(),
            ],
            'calendar_id'  => $organisation->master_calendar_nylas_primary_calendar_id,
            'participants' => [
                [
                    'name'  => $organisation->name,
                    'email' => $organisation->master_calendar_email,
                ],
                [
                    'name'  => $agent->name,
                    'email' => $agent->office_365_email_id,
                ],
                [
                    'name'  => $requestor->name,
                    'email' => $requestor->email,
                ],
            ]
        ];

        if ($place) {
            $params['participants'][] = [
                'name'  => $place->name,
                'email' => $place->email,
            ];

            if ($place->id !== $encounter->places_id) {
                /**
                 * Send cancellation to old place
                 */
                $this->notifyCancellation(
                    $encounter,
                    $event,
                    [
                        [
                            'name'     => $encounter->place->name,
                            'email'    => $encounter->place->email,
                            'timezone' => $encounter->place?->location?->timezone,
                        ]
                    ]
                );
            }
        }

        if ($agent->id !== $encounter->agent_id) {
            /**
             * Send cancellation to old agent
             */
            $this->notifyCancellation(
                $encounter,
                $event,
                [
                    [
                        'name'     => $encounter->agent->name,
                        'email'    => $encounter->agent->office_365_email_id,
                        'timezone' => $encounter->agent->timezone,
                    ]
                ]
            );
        }

        /**
         * Update Encounter with new data
         */
        $encounter->agent_id = $agent->id;
        $encounter->places_id = $place->id ?? null;
        $encounter->scheduled_at = $startDate;
        $encounter->ends_at = $endDate;
        $encounter->save();

        if ($place && $place->id !== $originalEncounter->places_id) {
            /**
             * Send invite to new place
             */
            $participants = [
                [
                    'name'  => $place->name,
                    'email' => $place->email,
                ]
            ];

            $this->notifyNewBookingPlace($encounter, $agent, $participants);
        } else {
            $participants = [
                [
                    'name'  => $place->name,
                    'email' => $place->email,
                ]
            ];

            $this->notifyReschedulePlace($encounter, $agent, $participants);
        }

        if ($agent->id !== $originalEncounter->agent_id) {
            /**
             * Send invite to new agent
             */
            $participants = [
                [
                    'name'  => $agent->name,
                    'email' => $agent->office_365_email_id,
                ],
                [
                    'name'  => $requestor->name,
                    'email' => $requestor->email,
                ],
            ];
            $this->notifyNewBookingAgent($encounter, $agent, $participants);
        } else {
            /**
             * Send rescheduled agent
             */
            $this->notifyReschedule(
                $encounter,
                $event,
                [
                    [
                        'name'     => $encounter->agent->name,
                        'email'    => $encounter->agent->office_365_email_id,
                        'timezone' => $encounter->agent->timezone,
                    ]
                ]
            );
        }

        $encounter->attendees->each(function ($attendee) use ($encounter, $event) {
            $this->notifyReschedule($encounter, $event, [$attendee]);
        });

        /**
         * Update Event in master Calendar
         */
        $this->updateEvent($encounter->external_id, $params, $authToken);

        /**
         * Finally transit encounter to new state
         */
        $encounter->transit('encounter_re_schedule');

        return $encounter->refresh();
    }

    /**
     * @codeCoverageIgnore
     */
    public function addAttendees(Encounter $encounter, array $data): Encounter
    {

        foreach ($data['participants'] as $participant) {
            $encounter->attendees()->create(
                array_merge(
                    [
                        'is_original' => 0,
                        'is_accepted' => 0
                    ],
                    $participant
                )
            );

            $participants = [
                $participant
            ];

            $this->notifyNewBookingAttendee($encounter, $participants);
        }

        return $encounter;
    }

    /**
     * @codeCoverageIgnore
     */
    public function notifyReschedule(Encounter $encounter, array $event, array $participants): void
    {
        Notification::route('mail', $participants[0]['email'])
            ->notify(
                (
                new EncounterSendRescheduledIcs(
                    $encounter,
                    $event,
                    $participants
                )
                )
                    ->delay(now()->addSeconds(self::DELAY))
            );
    }

    /**
     * @codeCoverageIgnore
     */
    public function notifyReschedulePlace(Encounter $encounter, User $agent, array $participants): void
    {
        Notification::route('mail', $participants[0]['email'])
            ->notify(
                (
                new EncounterReschedulePlaceIcsFile(
                    $encounter,
                    $participants,
                    $agent
                )
                )
                    ->delay(now()->addSeconds(self::DELAY))
            );
    }
}
