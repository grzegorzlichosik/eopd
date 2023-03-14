<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;


class AuthenticationTest extends TestCase
{
    const URL = '/login';

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get(self::URL);

        $response->assertStatus(200);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post(self::URL, [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_as_password_is_too_old()
    {
        $user = User::factory()->requiresPasswordUpdate()->create();

        $this->post(self::URL, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_as_max_failed_logins_reached()
    {
        $user = User::factory()->maxFailedLogins()->create();

        $this->post(self::URL, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }
}
