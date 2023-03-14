<?php

namespace Tests\Unit\Models;

use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Channel;
use App\Models\Place;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    public function test_channel(): void
    {
        Channel::factory(10)->create()->each(function ($channel) {
            $this->assertDatabaseHas(
                'channels',
                [
                    'uuid'     => $channel->uuid,
                    'flows_id' => $channel->flows_id
                ]
            );
        });

        $channel = Channel::factory()->create();
        $this->assertModelExists($channel);

        $channel = Channel::factory()->create();
        $channel->delete();
        $this->assertModelMissing($channel);
    }

    public function test_channel_face2face(): void
    {
        $channel = Channel::factory()->face2face()->create();
        $this->assertModelExists($channel);
        $this->assertEquals(ChannelType::F2F, $channel->channel_types_id);
    }

    public function test_channel_web(): void
    {
        $channel = Channel::factory()->web()->create();
        $this->assertModelExists($channel);
        $this->assertEquals(ChannelType::WEB, $channel->channel_types_id);
    }

    public function test_channel_phone(): void
    {
        $channel = Channel::factory()->phone()->create();
        $this->assertModelExists($channel);
        $this->assertEquals(ChannelType::PHONE, $channel->channel_types_id);
    }

    public function test_channel_organisation_relation(): void
    {
        $channel = Channel::factory()->create();
        $this->assertModelExists($channel);
        $this->assertInstanceOf(Organisation::class, $channel->organisation);
        $this->assertEquals($channel->organisations_id, $channel->organisation->id);
    }

    public function test_channel_flow_relation(): void
    {
        $channel = Channel::factory()->create();
        $this->assertModelExists($channel);
        $this->assertInstanceOf(Flow::class, $channel->flow);
        $this->assertEquals($channel->flows_id, $channel->flow->id);
    }

    public function test_channel_type_relation(): void
    {
        $channel = Channel::factory()->create();
        $this->assertModelExists($channel);
        $this->assertInstanceOf(ChannelType::class, $channel->type);
        $this->assertEquals($channel->channel_types_id, $channel->type->id);
    }

    public function test_place_channels_relation(): void
    {
        $channel = Channel::factory()->create();
        $places = Place::factory(10)->create();

        $channel->places()->sync(
            $places->map(fn($item) => $item->id)->toArray()
        );

        $this->assertEquals(10, $channel->places->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $channel->places);
        $this->assertInstanceOf(Place::class, $channel->places->first());
    }

    public function test_channel_encounters_relation(): void
    {
        $channel = Channel::factory()->create();
        $this->assertModelExists($channel);

        $encounters = Encounter::factory(10)->create([
            'channels_id' => $channel->id,
        ]);

        $this->assertEquals(10, $channel->encounters->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $channel->encounters);
        $this->assertInstanceOf(Encounter::class, $channel->encounters->first());
        $this->assertTrue($channel->encounters->contains($encounters->first()));
    }
}
