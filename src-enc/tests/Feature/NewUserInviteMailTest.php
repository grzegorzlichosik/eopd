<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\User;
use App\Notifications\InviteNewUser;
use App\Notifications\NewUserInvite;
use App\Notifications\Notification as BaseNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewUserInviteMailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function test_register_user_invite_mail()
    {
        Notification::fake();
        Notification::assertNothingSent();
        $user = User::factory()->unverified()->create();
        Notification::send(
            $user,
            new NewUserInvite(self::TOKEN)
        );
        $subject = trans('mail.account_activation_subject');
        $actionText = trans('mail.account_activation_cta');
        $actionUrl = \url("/reset-password/" . self::TOKEN . "?email=" . urlencode($user->email));
        $salutation = BaseNotification::getSalutation();
        $testData = [
            'subject'    => $subject,
            'actionText' => $actionText,
            'actionUrl'  => $actionUrl,
            'salutation' => $salutation
        ];

        Notification::assertSentTo(
            $user,
            NewUserInvite::class,
            function ($notification, $channels, $notifiable) use ($testData) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($testData['subject'], $mailData['subject']);
                $this->assertEquals($testData['actionText'], $mailData['actionText']);
                $this->assertEquals($testData['actionUrl'], $mailData['actionUrl']);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);
                $this->assertEquals(env('APP_NAME', 'Laravel'), NewUserInvite::getAppName());
                $this->assertEquals(env('APP_URL', 'https://localhost'), NewUserInvite::getAppUrl());

                return true;
            });
    }

    public function test_invite_new_user_mail()
    {
        Notification::fake();
        Notification::assertNothingSent();
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->twoFactorConfirmed()->create([
            'organisations_id' => $organisation->id
        ]);
        $user = User::factory()->unverified()->create();

        Notification::send(
            $user,
            new InviteNewUser(self::TOKEN, $admin->name, $organisation->name)
        );
        $subject = trans('mail.invite_new_user_subject', ['admin' => $admin->name]);
        $actionText = trans('mail.invite_new_user_cta');
        $actionUrl = \url("/reset-password/" . self::TOKEN . "?email=" . urlencode($user->email));
        $salutation = BaseNotification::getSalutation();
        $testData = [
            'subject' => $subject,
            'actionText' => $actionText,
            'actionUrl' => $actionUrl,
            'salutation' => $salutation
        ];

        Notification::assertSentTo(
            $user,
            InviteNewUser::class,
            function ($notification, $channels, $notifiable) use ($testData) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($testData['subject'], $mailData['subject']);
                $this->assertEquals($testData['actionText'], $mailData['actionText']);
                $this->assertEquals($testData['actionUrl'], $mailData['actionUrl']);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);

                return true;
            });
    }

}
