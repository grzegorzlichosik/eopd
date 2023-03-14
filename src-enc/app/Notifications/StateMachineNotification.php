<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class StateMachineNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        $eventName = $this->getStateMachineEventName($notifiable);
        $subject = $this->getStateMachineNotificationSubject();

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable['name'] . ',')
            ->line('Flow name: ' . $this->flow->name)
            ->line('Event name: ' . $eventName)
            ->line("JSON: " . json_encode($this->flow->toArray()))
            ->salutation(self::getSalutation());
    }

    public function getStateMachineNotificationSubject(): string
    {
        return implode(' ', $this->getStateMachineClassNameAsArray());
    }

    public function getStateMachineEventName(Model $notifiable): string
    {
        $className = $this->getStateMachineClassNameAsArray();
        $className[0] = strtoupper($className[0]);

        if ($notifiable->is_super_admin) {
            $className[] = 'SAMD';
        }

        if ($notifiable->is_admin) {
            $className[] = 'AMD';
        }

        if (!empty($notifiable->is_coordinator) && $notifiable->is_coordinator) {
            $className[] = 'CO';
        }

        $className[] = 'E';

        return implode('-', $className);

    }

    public function getStateMachineClassNameAsArray(): array
    {
        $className = explode('_', Str::snake(last(explode('\\', get_class($this)))));
        $className = array_map('strtolower', $className);
        $className = array_map('ucfirst', $className);

        return $className;
    }

}
