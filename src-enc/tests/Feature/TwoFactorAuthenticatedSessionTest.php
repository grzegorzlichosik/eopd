<?php

namespace Tests\Feature;

use App\Models\AuditLogEvent;
use App\Models\User;
use App\Notifications\Notification as BaseNotification;
use App\Notifications\RecoveryCode;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Tests\TestCase;
use Tests\TestTwoFactorAuthenticationProvider;


class TwoFactorAuthenticatedSessionTest extends TestCase
{
    private const VALID_CODE = "123456";
    private const INVALID_CODE = "654321";

    public function setup(): void
    {
        parent::setUp();
        app()->singleton(TwoFactorAuthenticationProviderContract::class, function () {
            return new TestTwoFactorAuthenticationProvider();
        });
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_2fa_counter_not_exceeded_with_valid_code()
    {
        Event::fake();

        $user = User::factory()
            ->twoFactorConfirmed()
            ->create([
                'failed_2fa_counter' => 2,
            ]);

        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'code'          => self::VALID_CODE,
                'recovery_code' => '',
            ]);

        $user->refresh();

        $this->assertEquals(0, $user->failed_2fa_counter);
        $this->assertDatabaseHas('users', ['failed_2fa_counter' => 0]);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
        $response->assertStatus(302);
    }

    public function test_2fa_counter_not_exceeded_with_valid_recovery_code()
    {
        Event::fake();

        $user = User::factory()
            ->twoFactorConfirmed()
            ->create([
                'failed_2fa_counter' => 2,
            ]);

        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'recovery_code'   => self::VALID_CODE,
                'is_recovery_mode' => 1,
            ]);

        $user->refresh();

        $this->assertEquals(0, $user->failed_2fa_counter);
        $this->assertDatabaseHas('users', ['failed_2fa_counter' => 0]);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
        $response->assertStatus(302);
    }

    public function test_2fa_counter_not_exceeded_with_invalid_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create(['failed_2fa_counter' => 2]);

        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'code' => self::INVALID_CODE,
            ]);

        $user->refresh();
        $this->assertEquals(3, $user->failed_2fa_counter);
        $response->assertSessionHasErrors('code');
        $response->assertStatus(302);
    }

    public function test_2fa_counter_not_exceeded_with_invalid_recovery_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create(['failed_2fa_counter' => 2]);
        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'recovery_code' => self::INVALID_CODE,
                'is_recovery_mode' => 1
            ]);

        $user->refresh();
        $this->assertEquals(3, $user->failed_2fa_counter);
        $response->assertSessionHasErrors('recovery_code');
        $response->assertStatus(302);
    }

    public function test_2fa_counter_exceeded_with_invalid_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create(['failed_2fa_counter' => 6]);
        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'code'          => self::INVALID_CODE,
                'recovery_code' => '',
            ]);

        $user->refresh();
        $this->assertEquals(6, $user->failed_2fa_counter);
        $response->assertSessionHasErrors('account_locked');
        $response->assertStatus(302);
    }

    public function test_2fa_counter_exceeded_with_invalid_recovery_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create(['failed_2fa_counter' => 6]);
        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'code'          => '',
                'recovery_code' => self::INVALID_CODE,
                'is_recovery_mode' => 1
            ]);

        $user->refresh();
        $this->assertEquals(6, $user->failed_2fa_counter);
        $response->assertSessionHasErrors('account_locked');
        $response->assertStatus(302);
    }

    public function test_email_recovery_code()
    {
        $user = User::factory()->create(['failed_2fa_counter' => 0]);
        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->get(route('mail.recovery.codes',[
                'uuid' => $user->uuid,
                'code' =>  Str::random(12)
                ]
            ));
        $response->assertStatus(200);

        Notification::fake();
        Notification::assertNothingSent();
        Notification::send(
            $user,
            new RecoveryCode(request('code'))
        );
        $subject = trans('mail.recovery_code_subject');
        $line1   = trans('mail.recovery_code_line_1');
        $salutation = BaseNotification::getSalutation();
        $testData = [
            'subject'    => $subject,
            'line1'      => $line1,
            'salutation' => $salutation

        ];

        Notification::assertSentTo(
            $user,
            RecoveryCode::class,
            function ($notification, $channels, $notifiable) use ($testData) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($testData['subject'], $mailData['subject']);
                $this->assertEquals($testData['line1'], $mailData['introLines'][0]);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);
                return true;
            });

    }
}
