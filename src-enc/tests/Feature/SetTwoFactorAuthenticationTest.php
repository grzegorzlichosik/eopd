<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;


class SetTwoFactorAuthenticationTest extends TestCase
{

    public function test_can_set_two_factor_authentication()
    {
        $user = User::factory()
            ->twoFactorNotConfirmed()
            ->create();

        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->get('/user/set-two-factor-authentication');
        $response->assertStatus(200);
    }

    public function test_can_not_set_two_factor_authentication()
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->get('/user/set-two-factor-authentication');

        $response->assertStatus(302);
    }
}
