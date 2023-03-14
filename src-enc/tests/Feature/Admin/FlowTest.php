<?php

namespace Tests\Feature\Admin;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FlowTest extends TestCase
{
    private User $admin;
    private User $agent;
    private Pool $pool;
    private Organisation $organisation;
    private Flow $flow;
    private Channel $channel;
    private Encounter $encounter;
    private Place $places;
    private Location $location;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_admin_can_view_flows(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.flows.index'));
        $response->assertStatus(200);

    }

    public function test_can_view_flows()
    {
        $this->withoutMiddleware()->actingAs($this->admin)->get(route('admin.flows.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Flows')

            );
    }

    public function test_can_view_flows_ascending()
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('admin.flows.index', [
            'sortField' => 'name',
            'sortOrder' => '1'
        ]));
        $response->assertStatus(200);

    }

    public function test_can_view_flows_descending()
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('admin.flows.index', [
            'sortField' => 'name',
            'sortOrder' => '-1'
        ]));
        $response->assertStatus(200);
    }

    public function test_can_filter_flows(): void
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('admin.flows.index', [
            'search' => Str::random()
        ]));

        $response->assertStatus(200);
    }

    public function test_can_collect_flows(): void
    {
        $response = $this->withoutMiddleware()->actingAs($this->admin)->get(route('admin.flows.index'));

        $response->assertStatus(200);
    }

    public function test_can_update_flows()
    {
        $flow = Flow::factory()->create();
        $channel = Channel::factory()->create([
            'flows_id' => $flow->id,
        ]);

        $channelType = ChannelType::find(ChannelType::F2F);

        $response = $this->withoutMiddleware()->actingAs($this->admin)->put(route('admin.flows.update', [
            'uuid'      => $this->flow->uuid,
            'name'      => $this->flow->name,
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => 2,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]
        ]));

        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.flow_edited', ['flow' => $this->flow->name]));
        $response->assertStatus(302);
        $response->assertLocation(route('admin.flows.agents', $this->flow->uuid));

    }

    public function test_cannot_update_flows()
    {
        $wrongInput = 'wrong input';
        $flow = Flow::factory()->create();
        $channel = Channel::factory()->create([
            'flows_id' => $flow->id,
        ]);

        $channelType = ChannelType::find(ChannelType::F2F);

        $response = $this->withoutMiddleware()->actingAs($this->admin)->put(route('admin.flows.update', [
            'uuid'      => $this->flow->uuid,
            'name'      => $this->flow->name,
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => $wrongInput,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]
        ]));
        $response->assertSessionHas('toaster');
        $this->assertStringContainsString($wrongInput, session('toaster')['message']);
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(302);
        $response->assertLocation(route('admin.flows.agents', $this->flow->uuid));
    }

    public function test_can_remove_flows_channel()
    {
        $flow = Flow::factory()->create();
        $channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::F2F,
            'flows_id'         => $flow->id,
        ]);
        Encounter::factory(10)->create([
            'flows_id'    => $flow->id,
            'channels_id' => $channel->id
        ]);

        $location = Location::factory()->create([
            'organisations_id' => $this->admin->organisations_id
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $this->admin->organisations_id,
            'locations_id'     => $location->id
        ]);

        $channel->places()->attach($place->id);
        $channelType = ChannelType::find(ChannelType::F2F);

        $response = $this->withoutMiddleware()->actingAs($this->admin)->put(route('admin.flows.update', [
            'uuid'      => $this->flow->uuid,
            'name'      => Str::random(4),
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => 0,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]

        ]));

        $response->assertStatus(302);

    }

    public function test_can_update_channel_participants_flows()
    {
        $flow = Flow::factory()->create();
        $channel = Channel::factory()->create([
            'flows_id' => $flow->id,
        ]);

        $channelType = ChannelType::find(1);

        $response = $this->withoutMiddleware()->actingAs($this->admin)->put(route('admin.flows.update', [
            'uuid'      => $this->flow->uuid,
            'name'      => $this->flow->name,
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => 10,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]
        ]));

        $response->assertStatus(302);

    }

    public function test_can_create_flows()
    {
        $name = Str::random();
        $channelType = ChannelType::find(1);
        $response = $this->withoutMiddleware()->actingAs($this->admin)->post(route('admin.flows.create', [
            'name'      => $name,
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => 2,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]

        ]));

        $createdFlow = Flow::where('name', $name)->first();

        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.flow_created', ['flow' => $name]));
        $response->assertStatus(302);
        $response->assertLocation(route('admin.flows.agents', $createdFlow->uuid));

    }

    public function test_cannot_create_flows()
    {
        $wrongInput = 'wrong input';
        $channelType = ChannelType::find(1);
        $response = $this->withoutMiddleware()->actingAs($this->admin)->post(route('admin.flows.create', [
            'name'      => Str::random(),
            'objective' => Str::random(50),
            'channels'  => [
                [
                    'channelTypeUuid'  => $channelType->uuid,
                    'max_participants' => $wrongInput,
                    'is_auto_confirm'  => false,
                    'is_default'       => false,
                ]
            ]

        ]));

        $response->assertSessionHas('toaster');
        $this->assertStringContainsString($wrongInput, session('toaster')['message']);
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(302);
        $response->assertLocation(route('admin.flows.index'));

    }

    private function prepare_test_data(): void
    {
        $organisation = Organisation::factory()->create();
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create([
            'organisations_id' => $organisation->id,
        ]);
        $this->agent = User::factory()->twoFactorConfirmed()->agent()->create([
            'organisations_id' => $organisation->id,
        ]);
        $flows = Flow::factory(4)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->flow = Flow::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->location = Location::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->channel = Channel::factory()->create([
            'channel_types_id' => ChannelType::WEB,
            'flows_id'         => $this->flow->id,
            'organisations_id' => $organisation->id,
        ]);

        $this->pool = Pool::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->pool->users()->attach($this->agent->id);

        $this->flow->users()->attach($this->agent->id, [
            'pools_id' => $this->pool->id
        ]);
        $this->flow->channelUsers()->attach($this->agent->id, [
            'channels_id' => $this->channel->id
        ]);

        $this->places = Place::factory()->create([
            'organisations_id' => $organisation->id,
            'locations_id'     => $this->location->id,
        ]);
        $this->encounter = Encounter::factory()->create([
            'agent_id'         => $this->agent->id,
            'flows_id'         => $this->channel->flows_id,
            'organisations_id' => $organisation->id,
            'places_id'        => $this->places->id,
        ]);
        $this->channel->places()->attach($this->places->id);
    }

}
