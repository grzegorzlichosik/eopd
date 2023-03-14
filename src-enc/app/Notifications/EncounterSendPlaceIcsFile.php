<?php

namespace App\Notifications;

use App\Models\Encounter;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Notification;
use App\Notifications\Traits\EncounterDetails;
use App\Notifications\Traits\GenerateEmailContent;

class EncounterSendPlaceIcsFile extends EncounterSendAgentIcsFile implements ShouldQueue
{
    use Queueable, EncounterDetails, GenerateEmailContent;

    /**
     * @codeCoverageIgnore
     */
    public function generateIcsFile(array $event, string $method = 'REQUEST', int $sequence = 0): string
    {
        $event['organizer_email'] = $this->agent->office_365_email_id;
        $event['organizer_name'] = $this->agent->name;

        return parent::generateIcsFile($event, $method);

    }
}
