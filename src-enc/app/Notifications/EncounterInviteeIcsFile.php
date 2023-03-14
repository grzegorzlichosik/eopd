<?php

namespace App\Notifications;

use App\Models\Encounter;
use App\Notifications\Traits\GenerateEmailContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Notifications\Traits\GenerateIcsFile;
use App\Notifications\Traits\EncounterDetails;

class EncounterInviteeIcsFile extends Notification implements ShouldQueue
{
    use Queueable, GenerateIcsFile, EncounterDetails, GenerateEmailContent;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        private readonly Encounter $encounter,
        private readonly array     $participants
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
        $encounter = self::encounterDetails($this->encounter->uuid);

        //TODO get timezone from organisation when added
        $timezone = $encounter->place?->location?->timezone ?: 'UTC';

        $event = self::refetchNylasEvent(
            $this->encounter->external_id,
            $encounter->organisation->master_calendar_nylas_access_token
        );

        $ics = $this->generateIcsFile(json_decode(json_encode($event), true));

        $scheduledAt = $encounter->scheduled_at->tz($timezone);
        $date = $scheduledAt->clone()->toDateString();
        $start = $scheduledAt->clone()->format('H:i');
        $end = $encounter->ends_at->tz($timezone)->format('H:i');

        return (new MailMessage())
            ->subject($event->title)
            ->line(
                trans(
                    'mail.encounter_agent_line_1',
                    [
                        'title' => $event->title,
                        'date'  => $date,
                        'start' => $start,
                        'end'   => $end,
                    ]
                )
            )
            ->line(new HtmlString('<u>' . trans('mail.event_details') . '</u>'))
            ->line($this->getEventDetails($encounter, $date, $start, $end))
            ->line(new HtmlString('<u>' . trans('mail.requestor_details') . '</u>'))
            ->line($this->getRequestorDetails($encounter))
            ->line($this->getLinks($encounter))
            ->salutation(self::getSalutation())
            ->attachData($ics, 'invite.ics', ['mime' => 'text/calendar;charset=UTF-8;method=REQUEST']);
    }
}
