<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateProfileNameTest extends TestCase
{

    public function test_profile_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->withoutMiddleware()->put('user/profile-details', [
            'name' => Str::random(10),
            'timezone' => 'America/Adak',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users',
            [
                'id' => $user->id,
                'timezone' => request('timezone')
            ]);
    }

    public function test_get_timezone()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->withoutMiddleware()->get('user/timezones');
        $response->assertStatus(200);
    }


}
