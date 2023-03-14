<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\TestTwoFactorAuthenticationProvider;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;

class ConfirmableTwoFactorAuthenticationTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        app()->singleton(TwoFactorAuthenticationProviderContract::class, function () {
            return new TestTwoFactorAuthenticationProvider();
        });
    }

    public function test_confirm_show_modal()
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $response = $this->actingAs($user)->get('user/confirm-two-factor-authentication-status');
        $response->assertStatus(200);
        $this->assertFalse($response->json('confirmed'));
    }

    public function test_show_success_with_correct_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($user)->postJson('user/confirm-two-factor-authentication', [
            'code' => '123456',
            'recovery_code' => ''
        ]);

        $response->assertStatus(201);
    }

    public function test_show_success_with_correct_recovery_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($user)->postJson('user/confirm-two-factor-authentication', [
            'code' => '',
            'recovery_code' => '123456'
        ]);

        $response->assertStatus(201);
    }

    public function test_show_error_with_incorrect_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($user)->postJson('user/confirm-two-factor-authentication', [
            'code' => '12345',
        ]);

        $response->assertStatus(422);
        $this->assertEquals($response->json('message'), 'The provided two factor authentication code was invalid.');
    }

    public function test_show_error_with_recovery_code()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($user)->postJson('user/confirm-two-factor-authentication', [
            'recovery_code' => '12345'
        ]);

        $response->assertStatus(422);
        $this->assertEquals($response->json('message'), 'The provided two factor recovery code was invalid.');
    }
}
