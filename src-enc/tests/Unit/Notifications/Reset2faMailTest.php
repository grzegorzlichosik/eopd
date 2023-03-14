<?php

namespace tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\Notification as BaseNotification;
use App\Notifications\Reset2fa;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Reset2faMailTest extends TestCase
{
    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function test_reset_2fa_password_mail_user(): void
    {

        Notification::fake();
        Notification::assertNothingSent();
        $user = User::factory()->create();
        Notification::send(
            $user,
            new Reset2fa(self::TOKEN)
        );
        $subject = trans('mail.reset_2fa_subject');
        $actionText = trans('mail.reset_2fa_cta');
        $actionUrl = \url("/reset-password/" . self::TOKEN . "?email=" . urlencode($user->email));
        $salutation = BaseNotification::getSalutation();;
        $line1 = trans('mail.reset_2fa_line1');
        $line2 = trans('mail.reset_2fa_line_2', ['count' => config('auth.passwords.users.expire')]);
        $testData = [
            'subject'    => $subject,
            'actionText' => $actionText,
            'actionUrl'  => $actionUrl,
            'salutation' => $salutation,
            'line1'      => $line1,
            'line2'      => $line2
        ];

        Notification::assertSentTo(
            $user,
            Reset2fa::class,
            function ($notification, $channels, $notifiable) use ($testData) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($testData['subject'], $mailData['subject']);
                $this->assertEquals($testData['line1'], $mailData['introLines'][0]);
                $this->assertEquals($testData['actionText'], $mailData['actionText']);
                $this->assertEquals($testData['actionUrl'], $mailData['actionUrl']);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);
                $this->assertEquals($testData['line2'], $mailData['outroLines'][0]);

                return true;
            });
    }
}
