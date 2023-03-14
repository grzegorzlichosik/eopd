<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


class UpdatePasswordTest extends TestCase
{

    private const NEW_PASSWORD = 'rMc%8ds=YF5*GqBW41';

    public function test_password_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->withoutMiddleware()->put('/user/password', [
            'current_password' => 'password',
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users',
        [
            'id' => $user->id,
        ]);
    }

    public function test_current_password_must_be_correct()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->withoutMiddleware()->put('/user/password', [
            'current_password' => 'wrong-password',
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ]);
        $response->assertSessionHasErrors();
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_passwords_must_match()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->withoutMiddleware()->put('/user/password', [
            'current_password' => 'password',
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors();
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
