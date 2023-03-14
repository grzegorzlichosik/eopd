<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class RecoveryCode extends Notification
{
    public function __construct(
        public readonly string $code
    )
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $code = explode(',', $this->code);
        $recoveryCodes = null;
        foreach ($code as $recovery) {
            $recoveryCodes = $recoveryCodes . "\r\n\n" . $recovery;
        }
        return (new MailMessage)
            ->subject(trans('mail.recovery_code_subject'))
            ->line(trans('mail.recovery_code_line_1'))
            ->line(new HtmlString('Recovery Codes:<div><strong>'.$recoveryCodes.'</div></strong>'))
            ->salutation(self::getSalutation());
    }

}
