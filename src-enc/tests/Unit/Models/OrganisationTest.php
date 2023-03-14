<?php

namespace Tests\Unit\Models;

use App\Models\Channel;
use App\Models\Flow;
use App\Models\Pool;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\User;
use Tests\TestCase;

class OrganisationTest extends TestCase
{
    public function test_organisation(): void
    {
        Organisation::factory(10)->create()->each(function($organisation){
            $this->assertDatabaseHas(
                'organisations',
                [
                    'uuid' => $organisation->uuid,
                    'name' => $organisation->name
                ]
            );
        });

        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $organisation = Organisation::factory()->create();
        $organisation->delete();
        $this->assertModelMissing($organisation);
    }

    public function test_platform_organisation(): void
    {
        $organisation = Organisation::where('is_platform', 1)->first();
        $this->assertModelExists($organisation);
    }

    public function test_organisation_users_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $users = User::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->users->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->users);
        $this->assertInstanceOf(User::class, $organisation->users->first());
        $this->assertTrue($organisation->users->contains($users->first()));
    }

    public function test_organisation_pools_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $pools = Pool::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->pools->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->pools);
        $this->assertInstanceOf(Pool::class, $organisation->pools->first());
        $this->assertTrue($organisation->pools->contains($pools->first()));
    }

    public function test_organisation_created_by_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $user = User::factory()->create([
            'organisations_id' => $organisation->id,
        ]);

        $organisation->created_by = $user->id;
        $organisation->save();
        $organisation = $organisation->refresh();

        $this->assertInstanceOf(User::class, $organisation->created_by()->first());
        $this->assertSame($user->id, $organisation->created_by()->first()->id);
    }

    public function test_organisation_locations_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $locations = Location::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->locations->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->locations);
        $this->assertInstanceOf(Location::class, $organisation->locations->first());
        $this->assertTrue($organisation->locations->contains($locations->first()));
    }

    public function test_organisation_places_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $places = Place::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->places->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->places);
        $this->assertInstanceOf(Place::class, $organisation->places->first());
        $this->assertTrue($organisation->places->contains($places->first()));
    }

    public function test_organisation_flows_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $flows = Flow::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->flows->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->flows);
        $this->assertInstanceOf(Flow::class, $organisation->flows->first());
        $this->assertTrue($organisation->flows->contains($flows->first()));
    }

    public function test_organisation_channels_relation(): void
    {
        $organisation = Organisation::factory()->create();
        $this->assertModelExists($organisation);

        $channels = Channel::factory(10)->create([
            'organisations_id' => $organisation->id,
        ]);

        $this->assertEquals(10, $organisation->channels->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $organisation->channels);
        $this->assertInstanceOf(Channel::class, $organisation->channels->first());
        $this->assertTrue($organisation->channels->contains($channels->first()));
    }
}
