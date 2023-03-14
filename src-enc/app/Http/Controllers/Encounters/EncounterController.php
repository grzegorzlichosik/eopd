<?php

namespace App\Http\Controllers\Encounters;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;

class EncounterController extends Controller
{
    /**
     * @codeCoverageIgnore
     */
    public function getFlow(string $uuid): ?Flow
    {
        return Flow::whereUuid($uuid)
            ->with('channels.type:id,label')
            ->first();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getChannel(string $uuid, ?Flow $flow): ?Channel
    {
        return Channel::whereUuid($uuid)
            ->where('flows_id', $flow->id)
            ->with(
                [
                    'type',
                    'places'
                ]
            )
            ->first();
    }

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
                    'place:id,name,email',
                    'place.location:id,name',
                    'organisation:id,name,phone_number,default_date_format',
                    'flow:id,name,objective,uuid',
                    'agent:id,name,email,office_365_email_id'
                ]
            )
            ->withCount('attendees')
            ->firstorFail();
    }

}
