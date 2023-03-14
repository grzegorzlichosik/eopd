<?php

namespace Tests\Feature\Agent;

use App\Models\Encounter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EncounterTest extends TestCase
{

    public function test_agent_can_view_schedules(): void
    {
        $user = User::factory()->agent()->create();
        $encounter = Encounter::factory(10)->create([
            'organisations_id' => $user->organisations_id,
            'agent_id'         => $user->id
        ]);
        $response = $this->actingAs($user)->get(route('agent.encounters.all'));
        $response->assertStatus(302);

    }

    public function test_can_view_my_schedules()
    {
        $user = User::factory()->agent()->create();
        $this->withoutMiddleware()->actingAs($user)->get(route('agent.encounters.all'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Agent/Encounters')

            );
    }

    public function test_can_view_my_calendar()
    {
        $user = User::factory()->agent()->create();
        $encounter = Encounter::factory(10)->create([
            'organisations_id' => $user->organisations_id,
            'agent_id'         => $user->id
        ]);
        $this->withoutMiddleware()->actingAs($user)->get(route('agent.encounters.calendar'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Agent/Calendars')

            );
    }

    public function test_agent_can_view_calendar_details(): void
    {
        $user = User::factory()->agent()->create();
        $encounter = Encounter::factory()->create([
           'organisations_id' => $user->organisations_id,
           'agent_id'         => $user->id
        ]);
        $response = $this->actingAs($user)->get(route('agent.encounters.show',[
            'uuid' => $encounter->uuid
        ]));
        $response->assertStatus(302);

    }

}
