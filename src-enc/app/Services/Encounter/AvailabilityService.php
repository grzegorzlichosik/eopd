<?php

namespace App\Services\Encounter;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Flow;
use App\Services\OAuth\OAuthNylasService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService extends AbstractEncounterService
{
    /**
     * @codeCoverageIgnore
     */
    public function getAvailability(Flow $flow, Channel $channel, Carbon $date): Collection
    {
        list($startDate, $endDate) = $this->getStartEndDates($date);

        $usersEmails = $this->getAgentsEmails($flow);
        $placesEmails = $this->getPlacesEmails($channel);

        $params = [
            'start_time'       => $startDate->unix(),
            'end_time'         => $endDate->unix(),
            'duration_minutes' => 30,
            'tentative_busy'   => true,
            'buffer'           => 0,
            'interval_minutes' => 30,
            'round_robin'      => 'max-availability',
            'emails'           => array_merge(
                $placesEmails->pluck('email')->toArray(),
                $usersEmails->pluck('email')->toArray()
            ),
        ];

        $response = new \stdClass;
        if ($usersEmails->isNotEmpty()) {
            $response = $this->oAuthService
                ->getAuthResponse(
                    '/calendars/availability',
                    'post',
                    $params,
                    $usersEmails->first()['access_token']
                );
        }

        $availabilitySlots = $this->getAvailabilitySlots($response);

        $placesEmailsUuids = $placesEmails->pluck('uuid', 'email')->toArray();
        $usersEmailsUuids = $usersEmails->pluck('uuid', 'email')->toArray();

        return $availabilitySlots->groupBy('start')
            ->map(function ($item) {
                $result = [];
                foreach ($item->groupBy('end') as $key => $end) {
                    $emails = [];
                    foreach ($end as $slot) {
                        $emails = array_merge($emails, $slot->emails);
                        $result[$key] = $slot;
                    }
                    $result[$key]->emails = array_unique($emails);
                }
                return $result;
            })->flatten(1)
            ->values()
            ->where('status', 'free')
            ->map(function ($timeSlot) use (
                $placesEmails,
                $usersEmails,
                $placesEmailsUuids,
                $usersEmailsUuids
            ) {

                $rooms = [];
                $users = [];
                list($rooms, $users) = $this->getAvailabilityEntities(
                    $timeSlot,
                    $placesEmails,
                    $placesEmailsUuids,
                    $rooms,
                    $usersEmails,
                    $usersEmailsUuids,
                    $users
                );

                return [
                    'rooms'     => $rooms,
                    'users'     => $users,
                    'start'     => Carbon::parse($timeSlot->start_time)->toDateTimeLocalString(),
                    'end'       => Carbon::parse($timeSlot->end_time)->toDateTimeLocalString(),
                    'startTime' => Carbon::parse($timeSlot->start_time)->toTimeString('minute'),
                    'endTime'   => Carbon::parse($timeSlot->end_time)->toTimeString('minute'),
                ];
            })->reject(function ($item) use ($channel) {
                return ($channel->channel_types_id === ChannelType::F2F && empty($item['rooms']))
                    ||
                    empty($item['users']);
            })->values();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getAvailabilitySlots(object $response): ?\Illuminate\Support\Collection
    {
        $availabilitySlots = collect([]);
        if (isset($response->time_slots)) {
            foreach ($response->time_slots as $timeSlot) {
                $availabilitySlots->push($timeSlot);
            }
        }

        return $availabilitySlots;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getAvailabilityEntities(
        $timeSlot,
        ?\Illuminate\Support\Collection $placesEmails,
        array $placesEmailsUuids,
        array $rooms,
        ?\Illuminate\Support\Collection $usersEmails,
        array $usersEmailsUuids,
        array $users
    ): array
    {
        foreach ($timeSlot->emails as $email) {

            if (in_array($email, $placesEmails->pluck('email')->toArray())) {
                $rooms[] = $placesEmailsUuids[$email];
            }

            if (in_array($email, $usersEmails->pluck('email')->toArray())) {
                $users[] = $usersEmailsUuids[$email];
            }
        }
        return array($rooms, $users);
    }

}
