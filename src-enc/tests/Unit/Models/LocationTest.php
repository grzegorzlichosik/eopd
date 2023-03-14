<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use Tests\TestCase;

class LocationTest extends TestCase
{
    public function test_location(): void
    {
        $location = Location::factory()->create();
        $this->assertModelExists($location);

        $location = Location::factory()->create();
        $location->delete();
        $this->assertModelMissing($location);
    }

    public function test_location_organisation_relation(): void
    {
        $location = Location::factory()->create();
        $this->assertModelExists($location);
        $this->assertInstanceOf(Organisation::class, $location->organisation);
        $this->assertEquals($location->organisations_id, $location->organisation->id);
    }

    public function test_location_places_relation(): void
    {
        $location = Location::factory()->create();
        $this->assertModelExists($location);

        $places = Place::factory(10)->create([
            'locations_id'     => $location->id,
            'organisations_id' => $location->organisations_id,
        ]);

        $this->assertEquals(10, $location->places->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $location->places);
        $this->assertInstanceOf(Place::class, $location->places->first());
        $this->assertTrue($location->places->contains($places->first()));
    }

    public function test_location_file_relation(): void
    {
        $location = Location::factory()->create();
        $this->assertModelExists($location);
        $this->assertInstanceOf(File::class, $location->file);
        $this->assertEquals($location->files_id, $location->file->id);
    }
}
