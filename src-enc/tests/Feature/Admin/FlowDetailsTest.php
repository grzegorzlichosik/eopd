<?php

namespace Tests\Feature\Admin;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FlowDetailsTest extends TestCase
{

    public function test_can_show_agents_flow_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create([
            'nylas_account_id' => Str::random(),
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channelFace = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);
        $channelWeb = Channel::factory()->create([
            'channel_types_id' => ChannelType::WEB,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);
        $channelPhone =  Channel::factory()->create([
            'channel_types_id' => ChannelType::PHONE,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);
        $pools = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pools->users()->attach($agent->id);
        $flow->users()->attach($agent->id, ['pools_id' => $pools->id]);
        $flow->channelUsers()->attach($agent->id, ['channels_id' => $channelPhone->id]);
        $response = $this->withoutMiddleware()->actingAs($admin)->get(route('admin.flows.agents', [
            'uuid' => $flow->uuid,
        ]));

        $response->assertStatus(200);
    }

    public function test_can_show_resources_flow_detail(): void
    {
        $admin = User::factory()->admin()->create();

        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $admin->organisations_id,
            'locations_id' => $location->id
        ]);

        $channel->places()->attach($place->id);
        $response = $this->withoutMiddleware()->actingAs($admin)->get(route('admin.flows.resources', [
            'uuid' => $flow->uuid,
        ]));

        $response->assertStatus(200);
    }

    public function test_can_show_encounters_flow_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create([
            'nylas_account_id' => Str::random(),
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $admin->organisations_id,
            'locations_id' => $location->id
        ]);
        Encounter::factory()->create([
            'agent_id'         => $agent->id,
            'channels_id'      => $channel->id,
            'flows_id'         => $channel->flows_id,
            'organisations_id' => $admin->organisations_id,
            'places_id'        => $place->id,
        ]);
        $response = $this->withoutMiddleware()->actingAs($admin)->get(route('admin.flows.encounters', [
            'uuid' => $flow->uuid,
        ]));

        $response->assertStatus(200);
    }
}
