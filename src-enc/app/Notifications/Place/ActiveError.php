<?php

namespace App\Notifications\Place;

use App\Models\Place;
use App\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ActiveError extends Notification
{
    use Queueable;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public readonly Place $place,
    )
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $eventName = $notifiable->is_super_admin
            ? 'PLACE-Active-Nylas-Update Error-Error-SAMD-E'
            : 'PLACE-Active-Nylas-Update Error-Error-ADM-E';

        return (new MailMessage)
            ->subject('Resourced active place has been updated with error')
            ->greeting('Hello ' . $notifiable['name'] . ',')
            ->line('Place name: ' . $this->place->name)
            ->line('Event name: ' . $eventName)
            ->line("JSON: " . json_encode($this->place->toArray()))
            ->salutation(self::getSalutation());
    }

}



