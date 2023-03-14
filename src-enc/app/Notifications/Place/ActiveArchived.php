<?php

namespace App\Notifications\Place;

use App\Models\Place;
use App\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ActiveArchived extends Notification
{
    use Queueable;

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
            ? 'PLACE-Active-Archive-Archived-SAMD-E'
            : 'PLACE-Active-Archive-Archived-ADM-E';

        return (new MailMessage)
            ->subject('Active place has been archived')
            ->greeting('Hello ' . $notifiable['name'] . ',')
            ->line('Place name: ' . $this->place->name)
            ->line('Event name: ' . $eventName)
            ->line("JSON: " . json_encode($this->place->toArray()))
            ->salutation(self::getSalutation());
    }

}
