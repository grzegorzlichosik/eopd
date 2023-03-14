<?php

namespace App\Notifications;

use App\Models\Encounter;
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

class BookingCancelled extends Notification
{
    use Queueable;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        private readonly string $eventId,
        private readonly array  $participants,
        private readonly string $authToken
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

        $event = (new OAuthNylasService())->getAuthResponse(
            '/events/' . $this->eventId,
            'get',
            [],
            $this->authToken
        );
        $this->removeCalendarEvent(json_decode(json_encode($event), true));

        $date = date('d/m/Y', $event->when->start_time);
        $start = date('H:i A', $event->when->start_time);
        $end = date('H:i A', $event->when->end_time);
        return (new MailMessage)
            ->subject($event->title)
            ->greeting('Hello')
            ->line("Booking for $event->title scheduled on $date from $start to
                 $end has been cancelled.")
            ->salutation(self::getSalutation());

    }

    /**
     * @codeCoverageIgnore
     */
    private function removeCalendarEvent(array $event): string
    {

      $spatieEvent = Encounter::spatieEventCreate($event);

        foreach ($this->participants as $participant) {
            $spatieEvent = $spatieEvent->attendee(
                $participant['email'],
                $participant['name'],
            );
        }

        return SpatieCalendar::create()
            ->productIdentifier('-//Google Inc//Google Calendar 70.9054//EN')
            ->withoutAutoTimezoneComponents()
            ->appendProperty(TextProperty::create('CALSCALE', 'GREGORIAN'))
            ->appendProperty(TextProperty::create('METHOD', 'CANCEL'))
            ->event($spatieEvent)
            ->get();
    }
}
