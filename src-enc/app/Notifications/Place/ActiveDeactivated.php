<?php

namespace App\Notifications\Place;

use App\Models\Place;
use App\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ActiveDeactivated extends Notification
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
            ? 'PLACE-Active-Deactivate-Inactive-SAMD-E'
            : 'PLACE-Active-Deactivate-Inactive-ADM-E';

        return (new MailMessage)
            ->subject('Active place has been deactivated')
            ->greeting('Hello ' . $notifiable['name'] . ',')
            ->line('Place name: ' . $this->place->name)
            ->line('Event name: ' . $eventName)
            ->line("JSON: " . json_encode($this->place->toArray()))
            ->salutation(self::getSalutation());
    }

}
