<?php

namespace Tests\Feature\Admin;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FlowResourceTest extends TestCase
{
    public function test_can_search_places()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisation->id,
        ]);
        $places = Place::factory(10)->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
        ]);
        $response = $this->withoutMiddleware()->actingAs($admin)->getJson(route('admin.flows.place.search',
            [
                'uuid'     => $flow->uuid,
                'search'   => $place->name,
                'selected' => []
            ]));

        $response->assertStatus(200);

    }

    public function test_can_store_places(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisation->id,
        ]);
        $places = Place::factory()->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.flows.place.store', [
            'uuid'           => $flow->uuid,
            'selected_flows' => [[
                                     'uuid'        => $places->uuid->toString(),
                                     'channelUuid' => $channel->uuid->toString(),
                                     'name'        => $places->name . '(' . $location->name . ')',
                                 ]]
        ]));
        $response->assertStatus(303);
    }

    public function test_cannot_store_places(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisation->id,
        ]);
        $places = Place::factory()->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.flows.place.store', [
            'uuid'           => $flow->uuid,
            'selected_flows' => [[
                                     'name' => $places->name . '(' . $location->name . ')',
                                 ]]
        ]));

        $response->assertRedirect(route('admin.flows.resources', ['uuid' => $flow->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(303);

    }

    public function test_can_delete_place(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisation->id,
        ]);
        $places = Place::factory()->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
        ]);
        $channel->places()->attach($places->id);
        $response = $this->withoutMiddleware()->actingAs($admin)->delete(route('admin.flows.place.delete', [
            'uuid'    => $flow->uuid,
            'channel' => $channel->uuid,
            'place'   => $places->uuid
        ]));

        $response->assertStatus(303);
    }

    public function test_cannot_delete_place(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisation->id,
        ]);
        $places = Place::factory()->create([
            'organisations_id' => $admin->organisation->id,
            'locations_id'     => $location->id,
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
        ]);
        $channel->places()->attach($places->id);
        $wrongValue = Str::uuid();
        $response = $this->withoutMiddleware()->actingAs($admin)->delete(route('admin.flows.place.delete', [
            'uuid'    => $flow->uuid,
            'channel' => $channel->uuid,
            'place'   => $wrongValue
        ]));

        $response->assertRedirect(route('admin.flows.resources', ['uuid' => $flow->uuid]));
        $response->assertSessionHas('toaster');
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(303);

    }
}
