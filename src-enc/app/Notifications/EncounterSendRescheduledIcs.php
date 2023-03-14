<?php

namespace App\Notifications;

use App\Models\Encounter;
use App\Notifications\Traits\EncounterDetails;
use App\Notifications\Traits\GenerateEmailContent;
use App\Notifications\Traits\GenerateIcsFile;
use App\Services\OAuth\OAuthService;
use App\Services\OAuth\OAuthNylasService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\IcalendarGenerator\Components\Calendar as SpatieCalendar;
use Spatie\IcalendarGenerator\Components\Event as SpatieEvent;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class EncounterSendRescheduledIcs extends Notification
{
    use Queueable, GenerateIcsFile, EncounterDetails, GenerateEmailContent;
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        private readonly Encounter $encounter,
        private readonly array     $event,
        private readonly array     $participants,
    )
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * @codeCoverageIgnore
     */
    public function toMail($notifiable)
    {
        $ics = $this->generateIcsFile($this->event, 'REQUEST', 1);
        $timezone = $this->participants[0]['timezone'] ?? 'UTC';

        $scheduledAt = $this->encounter->scheduled_at->tz($timezone);
        $date = $scheduledAt->clone()->toDateString();
        $start = $scheduledAt->clone()->format('H:i');
        $end = $this->encounter->ends_at->tz($timezone)->format('H:i');

        return (new MailMessage)
            ->subject($this->event['title'])
            ->greeting('Hello')
            ->line(
                trans(
                    'mail.reschedule_line_1',
                    [
                        'title' =>  $this->event['title'],
                        'date'  => $date,
                        'start' => $start,
                        'end'   => $end,
                    ]
                )
            )
            ->attachData($ics, 'invite.ics', ['mime' => 'text/calendar;charset=UTF-8;method=REQUEST'])
            ->salutation(self::getSalutation());

    }
}
