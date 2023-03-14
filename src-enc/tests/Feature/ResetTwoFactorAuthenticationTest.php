<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;


class ResetTwoFactorAuthenticationTest extends TestCase
{

    public function test_can_reset_two_factor_authentication()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($user)
            ->withSession(['login.id' => $user->id])
            ->post('/reset-two-factor-authentication');
        $response->assertStatus(302);
        $user = $user->fresh();
        $this->assertNotNull($user->two_factor_reset_request_at);
    }
}
