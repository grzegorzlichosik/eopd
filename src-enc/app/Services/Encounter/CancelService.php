<?php

namespace App\Services\Encounter;

use App\Models\Encounter;

class CancelService extends AbstractEncounterService
{
    /**
     * @codeCoverageIgnore
     */
    public function cancelBooking(Encounter $encounter): Encounter
    {
        $authToken = $encounter->organisation()->first()->master_calendar_nylas_access_token;

        $event = $this->getEvent($encounter->external_id, $authToken);

        /**
         * Notify Attendees
         */
        $encounter->attendees->each(function ($attendee) use ($encounter, $event) {
            $this->notifyCancellation($encounter, $event, [$attendee]);
        });

        /**
         * Notify Agent
         */
        $agent = $encounter->agent()->first();
        $this->notifyCancellation(
            $encounter,
            $event,
            [
                [
                    'name'     => $agent->name,
                    'email'    => $agent->office_365_email_id,
                    'timezone' => $agent->timezone,
                ]
            ]
        );

        /**
         * Notify Place
         */
        if ($encounter->places_id) {
            $this->notifyCancellation($encounter, $event, [
                    [
                        'name'     => $encounter->place->name,
                        'email'    => $encounter->place->email,
                        'timezone' => $encounter->place?->location?->timezone,
                    ]
                ]
            );
        }

        $encounter->transit('encounter_original_attendee_cancel');

        /**
         * Cancel Event in master Calendar
         */
        $this->deleteEvent($encounter->external_id, $authToken);

        return $encounter->refresh();
    }
}
