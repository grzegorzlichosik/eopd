<?php

namespace App\Services\Encounter;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingService extends AbstractEncounterService
{

    /**
     * @codeCoverageIgnore
     */
    public function makeBooking(Flow $flow, Channel $channel, array $data): Encounter
    {
        $organisation = Organisation::find($flow->organisations_id);
        if (!$organisation->master_calendar_nylas_primary_calendar_id) {
            $this->setPrimaryCalendar($organisation);
        }

        $place = null;
        if (!empty($data['date_time']['rooms'])) {
            $place = Place::whereUuidIn($data['date_time']['rooms'])
                ->where('organisations_id', $organisation->id)
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

        $startDate = Carbon::parse($data['date_time']['start']);
        $endDate = Carbon::parse($data['date_time']['end']);
        $eventTitle = 'Meeting ' . $organisation->name . ': ' . $flow->name;

        $params = [
            'title'        => $eventTitle,
            'location'     => $place ? $place->name : $channel->type->name,
            'busy'         => true,
            'read_only'    => true,
            'when'         => [
                'start_time' => $startDate->unix(),
                'end_time'   => $endDate->unix(),
            ],
            'metadata'     => [
                'uuid'    => Str::uuid()->toString(),
                'channel' => $channel->uuid,
                'type'    => $channel->type->name,
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
                    'name'  => $data['attendee_name'],
                    'email' => $data['attendee_email'],
                ],
            ]
        ];

        if ($place) {
            $params['participants'][] = [
                'name'  => $place->name,
                'email' => $place->email,
            ];
        }

        /**
         * Book master calendar event
         */
        $event = $this->createEvent($params, $organisation->master_calendar_nylas_access_token);

        /**
         * Create encounter
         */
        $encounter = Encounter::create(
            [
                'organisations_id' => $flow->organisations_id,
                'flows_id'         => $flow->id,
                'channels_id'      => $channel->id,
                'channel_types_id' => $channel->channel_types_id,
                'agent_id'         => $agent->id,
                'places_id'        => $place->id ?? null,
                'external_id'      => $event->id,
                'scheduled_at'     => $startDate,
                'ends_at'          => $endDate,
            ]
        );

        $encounter->transit('encounter_create');

        $encounter->attendees()->create(
            [
                'name'         => $data['attendee_name'],
                'email'        => $data['attendee_email'],
                'phone_number' => $data['phone_number'] ? $data['dial_code'] . ' ' . $data['phone_number'] : null,
                'is_original'  => 1,
                'is_accepted'  => 0,
            ]
        );

        /**
         * Notify Agent
         */
        $participants = [
            [
                'name'  => $agent->name,
                'email' => $agent->office_365_email_id,
            ],
            [
                'name'  => $data['attendee_name'],
                'email' => $data['attendee_email'],
            ],
        ];
        $this->notifyNewBookingAgent($encounter, $agent, $participants);


        /**
         * Notify Attendee
         */
        $participants = [
            [
                'name'  => $data['attendee_name'],
                'email' => $data['attendee_email'],
            ],
        ];
        $this->notifyNewBookingAttendee($encounter, $participants);

        /**
         * Notify Place
         */
        if ($place) {
            $participants = [
                [
                    'name'  => $place->name,
                    'email' => $place->email,
                ]
            ];

            $this->notifyNewBookingPlace($encounter, $agent, $participants);
        }

        return $encounter;
    }



}
