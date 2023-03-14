<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class Reset2fa extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public readonly string $token
    )
    {

    }
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $link = url("/reset-password/" . $this->token . "?email=" . urlencode($notifiable['email']));

        return (new MailMessage)
                    ->subject(trans('mail.reset_2fa_subject'))
                    ->line(trans('mail.reset_2fa_line1'))
                    ->action(trans('mail.reset_2fa_cta'), $link)
                    ->line(trans('mail.reset_2fa_line_2', ['count' => config('auth.passwords.users.expire')]))
                    ->salutation(self::getSalutation());
    }

}
