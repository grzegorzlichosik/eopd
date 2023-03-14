<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\TsmState;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    public const STATE_SCHEDULED = 'Scheduled';


    public function test_admin_can_view_schedules(): void
    {
        $user = User::factory()->admin()->create();
        Encounter::factory(10)->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' =>
                Encounter::STATE_PENDING_AUTO_RE_SCHEDULING
        ]);
        $response = $this->actingAs($user)->get(route('admin.encounters.upcoming'));
        $response->assertStatus(302);

    }

    public function test_can_view_upcoming_schedules()
    {
        $user = User::factory()->admin()->create();
        Encounter::factory(10)->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' =>
                Encounter::STATE_SCHEDULED
        ]);

        $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.upcoming'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/EncountersScheduled')

            );
    }

    public function test_can_view_all_schedules()
    {
        $user = User::factory()->admin()->create();
        Encounter::factory(10)->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' =>
                Encounter::STATE_INITIAL
        ]);

        $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.all'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Encounters')

            );
    }

    public function test_can_view_completed_schedules()
    {
        $user = User::factory()->admin()->create();
        Encounter::factory(10)->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' =>
                Encounter::STATE_FINISHED
        ]);

        $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.completed'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/EncountersCompleted')

            );
    }

    public function test_can_view_cancelled_schedules()
    {
        $user = User::factory()->admin()->create();
        Encounter::factory(10)->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' =>
                Encounter::STATE_CANCELLED
        ]);

        $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.cancelled'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/EncountersCancelled')

            );
    }

    public function test_can_view_encounter_ascending()
    {
        $user = User::factory()->admin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.upcoming', [
            'sortField' => 'uuid',
            'sortOrder' => '1'
        ]));
        $response->assertStatus(200);

    }

    public function test_can_view_encounter_descending()
    {
        $user = User::factory()->admin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.upcoming', [
            'sortField' => 'uuid',
            'sortOrder' => '-1'
        ]));
        $response->assertStatus(200);
    }

    public function test_can_filter_encounter(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->admin()->create([
            'organisations_id' => $organisation->id
        ]);
        $location = Location::factory()->create([
            'organisations_id' => $organisation->id
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $organisation->id,
            'locations_id'     => $location->id,
        ]);

        $flows = Flow::factory()->create([
            'organisations_id' => $organisation->id
        ]);
        $channelType = ChannelType::find(1);
        $channels = Channel::factory()->create([
            'organisations_id' => $organisation->id,
            'flows_id' => $flows->id,
            'channel_types_id' => $channelType->id
        ]);
        $agent = User::factory()->agent()->create([
            'organisations_id' => $organisation->id
        ]);
        $encounter = Encounter::factory(10)->create([
            'agent_id' => $agent->id,
            'flows_id' => $channels->flows_id,
            'organisations_id' => $organisation->id,
            'places_id' => $place->id,
        ]);

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.upcoming', [
            'search' => Str::random(2),
            'place'  => $place->uuid,
            'flows'  => $flows->uuid,
            'agent'  => $agent->uuid,
            'location' => $location->uuid,
            'channel'  => $channelType->uuid,
            'currentState' => self::STATE_SCHEDULED,
        ]));


        $response->assertStatus(200);
    }

    public function test_can_view_schedules_detail()
    {
        $user = User::factory()->admin()->create();
        $encounter = Encounter::factory()->create([
            'organisations_id'  => $user->organisations_id,
            'tsm_current_state' => Encounter::STATE_SCHEDULED
        ]);
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.encounters.show',[
            'uuid' => $encounter->uuid
        ]));
        $response->assertStatus(200);
    }

}
