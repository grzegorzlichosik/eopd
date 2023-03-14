<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{

    public function test_confirm_password_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user/confirm-password');

        $response->assertStatus(200);
    }

}
