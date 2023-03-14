<?php

namespace App\Notifications\Traits;

use App\Models\Encounter;
use App\Services\OAuth\OAuthNylasService;

trait EncounterDetails
{
    /**
     * @codeCoverageIgnore
     */
    public static function encounterDetails(string $uuid): Encounter
    {
        return Encounter::where('uuid', $uuid)
            ->with(
                [
                    'channel',
                    'channel.type',
                    'attendees',
                    'place:id,name',
                    'place.location',
                    'organisation:id,name,phone_number,default_date_format,master_calendar_nylas_access_token',
                    'flow:id,name,objective,uuid',
                ]
            )
            ->withCount('attendees')
            ->firstorFail();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function refetchNylasEvent(string $eventId, string $authToken): ?object
    {
        return (new OAuthNylasService())->getAuthResponse(
            '/events/' . $eventId,
            'get',
            [],
            $authToken
        );
    }
}
