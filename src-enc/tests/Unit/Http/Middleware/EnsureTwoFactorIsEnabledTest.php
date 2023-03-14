<?php

namespace Tests\Unit\Http\Middleware;

use App\Models\User;
use Tests\TestCase;

class EnsureTwoFactorIsEnabledTest extends TestCase
{
    public function test_no_redirect_when_two_factor_enabled()
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_redirect_when_two_factor_disabled()
    {
        $user = User::factory()->twoFactorNotConfirmed()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(302);

        $response = $this->actingAs($user)->getJson(route('dashboard'));
        $response->assertStatus(403);
    }
}
