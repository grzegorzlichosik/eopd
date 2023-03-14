<?php

namespace Tests\Unit\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{

    public function test_can_view_two_factor()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $response = $this->actingAs($user)->get('/user/two-factor');
        $response->assertStatus(200);
    }

    public function test_can_view_reset_password()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        $response = $this->actingAs($user)->get('/user/password');
        $response->assertStatus(200);
    }
}
