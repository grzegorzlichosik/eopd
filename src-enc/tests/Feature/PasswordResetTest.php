<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Tests\TestCase;
use App\Notifications\Notification as BaseNotification;

class PasswordResetTest extends TestCase
{
    private const SUCCESS_PASSWORD = 'rMch8dstYF5yGqBW41';
    private const FAIL_PASSWORD = 'password';
    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function test_reset_password_link_screen_can_be_rendered()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    /**
     * Testing submitting the password reset page with a password
     * Reset link request.
     */

    public function test_reset_password_link_can_be_requested()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();

        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(302);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Testing submitting the password reset page with a password
     * reset link exists.
     */

    public function test_reset_password_screen_can_be_rendered()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();

        $response = $this->get('/reset-password/' . self::TOKEN, [
            'email' => urlencode($user->email)
        ]);
        $response->assertStatus(200);

    }


    /**
     * Testing submitting the password reset page with a password
     * login link exists after update
     */

    public function test_login_link_screen_can_be_rendered()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_password_reset_mail()
    {
        Notification::fake();
        Notification::assertNothingSent();
        $user = User::factory()->create();
        Notification::send(
            $user,
            new ResetPassword(self::TOKEN)
        );
        $subject = trans('mail.reset_password_subject');
        $actionText = trans('mail.reset_password_cta');
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
            ResetPassword::class,
            function ($notification, $channels, $notifiable) use ($testData) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($testData['subject'], $mailData['subject']);
                $this->assertEquals($testData['actionText'], $mailData['actionText']);
                $this->assertEquals($testData['actionUrl'], $mailData['actionUrl']);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);
                return true;
            });
    }

    public function test_password_can_be_reset_null()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();
        Notification::send(
            $user,
            new ResetPassword(self::TOKEN)
        );
        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => '',
                'password_confirmation' => '',
            ]);
            $errors = session('errors');
            $response->assertSessionHasErrors();
            $this->assertEquals($errors->get('password')[0], 'The password field is required.');
            return true;
        });
    }

    public function test_password_can_be_reset_mismatch()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();
        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => self::SUCCESS_PASSWORD,
                'password_confirmation' => self::FAIL_PASSWORD,
            ]);
            $errors = session('errors');
            $response->assertSessionHasErrors();
            $this->assertEquals($errors->get('password')[0], 'The password confirmation does not match.');
            return true;
        });
    }

    public function test_password_can_be_reset_short()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();
        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => 'pass',
                'password_confirmation' => 'pass',
            ]);
            $errors = session('errors');
            $response->assertSessionHasErrors();
            $this->assertEquals($errors->get('password')[0], trans('validation.password_rule', ['length' => 12]));
            return true;
        });
    }

    public function test_password_can_be_reset_updated()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();
        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => self::SUCCESS_PASSWORD,
                'password_confirmation' => self::SUCCESS_PASSWORD,
            ]);
            $response->assertSessionHasNoErrors();
            return true;
        });
    }

    public function test_password_can_be_reset_valid_token()
    {
        if (!Features::enabled(Features::resetPasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();
        $response = $this->withoutMiddleware()->post('/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => self::SUCCESS_PASSWORD,
                'password_confirmation' => self::SUCCESS_PASSWORD,
            ]);
            $response->assertSessionHasNoErrors();
            return true;
        });
    }


}
