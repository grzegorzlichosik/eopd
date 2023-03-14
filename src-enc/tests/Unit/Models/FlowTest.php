<?php

namespace Tests\Unit\Models;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\FlowType;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Tests\TestCase;

class FlowTest extends TestCase
{
    public function test_flow(): void
    {
        Flow::factory(10)->create()->each(function ($flow) {
            $this->assertDatabaseHas(
                'flows',
                [
                    'uuid'             => $flow->uuid,
                    'organisations_id' => $flow->organisations_id
                ]
            );
        });

        $flow = Flow::factory()->create();
        $this->assertModelExists($flow);

        $flow = Flow::factory()->create();
        $flow->delete();
        $this->assertModelMissing($flow);
    }

    public function test_flow_organisation_relation(): void
    {
        $flow = Flow::factory()->create();
        $this->assertModelExists($flow);
        $this->assertInstanceOf(Organisation::class, $flow->organisation);
        $this->assertEquals($flow->organisations_id, $flow->organisation->id);
    }

    public function test_flow_channel_relation(): void
    {
        $flow = Flow::factory()->create();
        $this->assertModelExists($flow);
        $channel = Channel::factory()->create(
            [
                'flows_id' => $flow->id,
            ]
        );

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $flow->channels);
        $this->assertInstanceOf(Channel::class, $flow->channels->first());
        $this->assertTrue($flow->channels->contains($channel));
    }

    public function test_flow_users_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $flow = Flow::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $pools = Pool::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $users = User::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $flow->users()->syncWithPivotValues(
            $users->map(fn($item) => $item->id)->toArray(),
            [
                'pools_id' => $pools->random()->id,
            ]
        );

        $this->assertEquals(10, $flow->users->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $flow->users);
        $this->assertInstanceOf(User::class, $flow->users->first());
    }

    public function test_flow_users_channels_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $flow = Flow::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $channel = Channel::factory()->create([
            'flows_id' => $flow->id,
            'organisations_id' => $organisation->id,
        ]);

        $users = User::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $flow->channelUsers()->syncWithPivotValues(
            $users->map(fn($item) => $item->id)->toArray(),
            [
                'channels_id' => $channel->id,
            ]
        );

        $this->assertEquals(10, $flow->channelUsers->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $flow->users);
        $this->assertInstanceOf(User::class, $flow->channelUsers->first());
    }

    public function test_flow_encounters_relation(): void
    {

        $flow = Flow::factory()->create();

        Encounter::factory(10)->create(
            [
                'flows_id' => $flow->id,
            ]
        )
            ->each(function ($encounter)  use ($flow){
                $this->assertDatabaseHas(
                    'encounters',
                    [
                        'flows_id' => $flow->id,
                    ]
                );
            });

        $this->assertEquals(10, $flow->encounters->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $flow->encounters);
        $this->assertInstanceOf(Encounter::class, $flow->encounters->first());
    }

}
