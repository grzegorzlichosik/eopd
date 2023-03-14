<?php

namespace Tests\Unit\Models;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Place;
use App\Models\Organisation;
use App\Models\PlaceType;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    public function test_place(): void
    {
        $place = Place::factory()->create();
        $this->assertModelExists($place);

        $place = Place::factory()->create();
        $place->delete();
        $this->assertModelMissing($place);
    }

    public function test_place_organisation_relation(): void
    {
        $place = Place::factory()->create();
        $this->assertModelExists($place);
        $this->assertInstanceOf(Organisation::class, $place->organisation);
        $this->assertEquals($place->organisations_id, $place->organisation->id);
    }

    public function test_place_location_relation(): void
    {
        $place = Place::factory()->create();
        $this->assertModelExists($place);
        $this->assertInstanceOf(Location::class, $place->location);
        $this->assertEquals($place->locations_id, $place->location->id);
    }

    public function test_place_place_type_relation(): void
    {
        $place = Place::factory()->create();
        $this->assertModelExists($place);
        $this->assertInstanceOf(PlaceType::class, $place->place_type);
        $this->assertEquals($place->place_types_id, $place->place_type->id);
    }

    public function test_place_encounters_relation(): void
    {

        $place = Place::factory()->create();

        Encounter::factory(10)->create(
            [
                'places_id' => $place->id,
            ]
        )
            ->each(function ($encounter) use ($place) {
                $this->assertDatabaseHas(
                    'encounters',
                    [
                        'places_id' => $place->id,
                    ]
                );
            });

        $this->assertEquals(10, $place->encounters->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $place->encounters);
        $this->assertInstanceOf(Encounter::class, $place->encounters->first());
    }

    public function test_place_channels_relation(): void
    {

        $place = Place::factory()->create();
        $channels = Channel::factory(10)->create();

        $place->channels()->sync(
            $channels->map(fn($item) => $item->id)->toArray()
        );

        $this->assertEquals(10, $place->channels->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $place->channels);
        $this->assertInstanceOf(Channel::class, $place->channels->first());
    }
}

