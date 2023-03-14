<?php

namespace Tests\Feature\Calendar;

use App\Exceptions\OAuthConnectionException;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    protected const EMAIL = 'testuser1@tf4cg.onmicrosoft.com';

    public function test_callback()
    {
        $authUser = User::factory()->superAdmin()->create([
            'email' => self::EMAIL,
        ]);

        $response = $this->withoutMiddleware()->actingAs($authUser)->get(route('calendar.oauth.callback', [
            'code' => Str::random(),
        ]));

        $response->assertStatus(200);
    }

    public function test_get_callback_with_code_link_my_calendar()
    {
        $authUser = User::factory()->superAdmin()->create([
            'email' => self::EMAIL
        ]);

        $this->withoutMiddleware()->actingAs($authUser)->get(route('calendar.oauth.callback', [
            'code' => ''
        ]));

        $this->actingAs($authUser)->get(route('calendar.retry'));
        $this->assertDatabaseHas('users', [
            'email'                     => self::EMAIL,
            'nylas_access_token'        => null,
            'nylas_account_id'          => null,
            'nylas_provider'            => null,
            'nylas_primary_calendar_id' => null,
            'microsoft_refresh_token'   => null
        ]);

        $response = $this->actingAs($authUser)->get(route('calendar.init'));
        $response->assertStatus(302);
    }

}
