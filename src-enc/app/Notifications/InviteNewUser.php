<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class InviteNewUser extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $token,
        public readonly string $admin,
        public readonly string $organisation
    )
    {
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url("/reset-password/" . $this->token . "?email=" . urlencode($notifiable['email']));

        return (new MailMessage)
                    ->subject(trans('mail.invite_new_user_subject', ['admin' => $this->admin]))
                    ->greeting('Hi '. $notifiable['name'] .',')
                    ->line(trans('mail.invite_new_user_line_1', ['organisation' => $this->organisation]))
                    ->line(trans('mail.invite_new_user_line_2', ['organisation' => $this->organisation]))
                    ->action(trans('mail.invite_new_user_cta'), $url)
                    ->line(trans('mail.invite_new_user_line_3', ['count' => config('auth.passwords.users.expire')]))
                    ->salutation(self::getSalutation());
    }
}
