<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_calendar_given_dates_invalid_nylas_access_token()
    {
        $authUser = User::factory()->superAdmin()->create([
            'email'              => 'testuser1@tf4cg.onmicrosoft.com',
            'nylas_access_token' => Str::random()
        ]);
        $response = $this->withoutMiddleware()->actingAs($authUser)->get(route('calendar.index', [
            'start' => '2022-10-30T00:00:00Z',
            'end'   => '2022-11-06T00:00:00Z',

        ]));
        $response->assertStatus(400);
    }

    public function test_calendar_no_given_dates()
    {
        $authUser = User::factory()->superAdmin()->create([
            'email'              => 'testuser1@tf4cg.onmicrosoft.com',
            'nylas_access_token' => 'SCdDfLbJcu71bp1P27ZHDhU8R6ES7h'
        ]);
        $response = $this->withoutMiddleware()->actingAs($authUser)->get(route('calendar.index', [
            'start' => '',
            'end'   => '',

        ]));
        $response->assertStatus(400);
    }

}
