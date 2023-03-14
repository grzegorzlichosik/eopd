<?php

namespace Tests\Unit\Http\Middleware;

use App\Models\User;
use Tests\TestCase;

class RedirectIfAuthenticatedTest extends TestCase
{
    public function test_redirects_if_authenticated()
    {
        $user = User::factory()->twoFactorConfirmed()->create();

        $response = $this->actingAs($user)->get(route('login'));
        $response->assertStatus(302);
    }
}
