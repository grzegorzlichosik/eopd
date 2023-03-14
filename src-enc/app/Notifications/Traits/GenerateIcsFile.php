<?php

namespace App\Notifications\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar as SpatieCalendar;
use Spatie\IcalendarGenerator\Components\Event as SpatieEvent;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\TextProperty;

trait GenerateIcsFile
{

    private static string $url = "/bookings/manage/";

    /**
     * @codeCoverageIgnore
     */
    public function generateIcsFile(array $event, string $method = 'REQUEST', int $sequence = 0): string
    {

        $event['ical_uid'] = $event['ical_uid'] ?? Str::uuid()->toString();

        $spatieEvent = SpatieEvent::create()
            ->organizer($event['organizer_email'], $event['organizer_name'])
            ->name('Invitation: ' . $event['title'])
            ->uniqueIdentifier($event['ical_uid'])
            ->startsAt(Carbon::createFromTimestamp($event['when']['start_time']))
            ->endsAt(Carbon::createFromTimestamp($event['when']['end_time']))
            ->appendProperty(TextProperty::create('TRANSP', 'OPAQUE'))
            ->appendProperty(TextProperty::create('SEQUENCE', $sequence));

        if ($method === 'CANCEL') {
            $spatieEvent->status(EventStatus::cancelled());
        }

        foreach ($this->participants as $participant) {
            $spatieEvent = $spatieEvent->attendee(
                $participant['email'],
                $participant['name'],
                ParticipationStatus::accepted(),
                requiresResponse: true
            );
        }

        return SpatieCalendar::create()
            ->productIdentifier('-//Google Inc//Google Calendar 70.9054//EN')
            ->withoutAutoTimezoneComponents()
            ->appendProperty(TextProperty::create('CALSCALE', 'GREGORIAN'))
            ->appendProperty(TextProperty::create('METHOD', $method))
            ->event($spatieEvent)
            ->get();
    }

}
