<?php

namespace Tests\Feature\Admin;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Flow;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FlowAgentTest extends TestCase
{
    public function test_can_store_agents(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $agent = User::factory()->agent()->twoFactorConfirmed()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.flows.agents.store', [
            'uuid' => $flow->uuid,
            'selected_agents' => [[
                'name' => $agent->name . '(' . $pool->name . ')',
                'uuid' => $agent->uuid . ':' . $pool->uuid
            ]]
        ]));

        $response->assertRedirect(route('admin.flows.agents', [$flow->uuid]));
        $response->assertStatus(302);
    }

    public function test_can_delete_agent(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $agent = User::factory()->agent()->twoFactorConfirmed()->create([
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
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $flow->channelUsers()->attach($agent->id, ['channels_id' => $channel->id]);
        $response = $this->withoutMiddleware()->actingAs($admin)->delete(route('admin.flows.agent.delete', [
            'flowUuid' => $flow->uuid,
            'uuid' => $agent->uuid,
            'pool' => $pool->name
        ]));

        $this->assertDatabaseMissing('flows_users_map', [
            'flows_id' => $flow->id,
            'users_id' => $agent->id,
            'pools_id' => $pool->id
        ]);

        $response->assertStatus(302);
    }


    public function test_cannot_store_agents(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $agent = User::factory()->agent()->twoFactorConfirmed()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);

        $flow->channelUsers()->attach($agent->id, ['channels_id' => $channel->id]);
        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.flows.agents.store', [
            'uuid' => $flow->uuid,
            'selected_agents' => [[
                'name' => $agent->name . '(' . $pool->name . ')',
                'uuid' => $agent->uuid,
                'poolUuid' => $pool->uuid
            ]]
        ]));

        $response->assertRedirect(route('admin.flows.agents', [$flow->uuid]));
        $response->assertStatus(302);
    }
//
    public function test_agent_add_channel(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $agent = User::factory()->agent()->twoFactorConfirmed()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);

        $flow->channelUsers()->attach($agent->id, ['channels_id' => $channel->id]);

        $response = $this->withoutMiddleware()->actingAs($admin)->put(route('admin.flows.agent.addChannel',
            [
            'uuid'          => $flow->uuid,
            'email'         => $agent->email,
            'checkedStatus' => true,
            'type'          => 'face_to_face'
            ]
        ));
        $response->assertStatus(302);
    }

    public function test_agent_remove_channel(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $agent = User::factory()->agent()->twoFactorConfirmed()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id' => $flow->id,
            'organisations_id' => $admin->organisations_id
        ]);

        $flow->channelUsers()->attach($agent->id, ['channels_id' => $channel->id]);

        $response = $this->withoutMiddleware()->actingAs($admin)->put(route(
            'admin.flows.agent.removeChannel',
            [
            'uuid'          => $flow->uuid,
            'email'         => $agent->email,
            'checkedStatus' => false,
            'type'          => 'face_to_face'
            ]
        ));
        $response->assertStatus(302);
    }


    public function test_can_search_users()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $orgUser = User::factory()->agent()->create([
            'name' => Str::random(),
            'nylas_account_id' =>Str::random(),
            'organisations_id' => $admin->organisations_id
        ]);
        $flow = Flow::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool = Pool::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $pool->users()->attach($orgUser->id);

        $response = $this->withoutMiddleware()->actingAs($admin)->getJson(route('admin.flows.agent.search',
            [
            'uuid'     => $flow->uuid,
            'search'   => $orgUser->name,
            'selected' => [$orgUser->uuid]
        ]));

        $response->assertStatus(200);

    }
}
