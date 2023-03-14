<?php

namespace App\Models;

use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use function App\Notifications\trans;
use function App\Notifications\url;

class ResetPassword extends Notification
{
    public function __construct(
        public readonly string $token
    )
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {

        $link = url("/reset-password/" . $this->token . "?email=" . urlencode($notifiable['email']));

        return (new MailMessage)
            ->subject(trans('mail.reset_password_subject'))
            ->line(trans('mail.reset_password_line_1'))
            ->action(trans('mail.reset_password_cta'), $link)
            ->line(trans('mail.reset_password_line_2', ['count' => config('auth.passwords.users.expire')]))
            ->line(trans('mail.reset_password_line_3'))
            ->salutation(self::getSalutation());

    }

}
