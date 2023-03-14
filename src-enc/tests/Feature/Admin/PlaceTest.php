<?php

namespace Tests\Feature\Admin;

use App\Models\Location;
use App\Models\Place;
use App\Models\PlaceType;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    use WithFaker;

    public function test_admin_can_view_places(): void
    {
        $user = User::factory()->admin()->create();
        Place::factory()->create();
        $response = $this->actingAs($user)
            ->get(route('admin.resources.places.index'));

        $response->assertStatus(302);
    }

    public function test_can_view_places()
    {
        $user = User::factory()->admin()->create();
        Location::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );
        Place::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );
        $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('admin.resources.places.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Places')
                    ->has('locations', 10)
                    ->has('places.data', 10)
            );
    }

    public function test_can_view_places_ascending()
    {
        $user = User::factory()->admin()->create();
        Place::factory()->create();
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('admin.resources.places.index', [
                'sortField' => 'location_uuid',
                'sortOrder' => '1'
            ]));
        $response->assertStatus(200);
    }

    public function test_can_view_descending()
    {
        $user = User::factory()->admin()->create();
        Place::factory()->create();
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('admin.resources.places.index', [
                'sortField' => 'name',
                'sortOrder' => '-1'
            ]));
        $response->assertStatus(200);
    }

    public function test_can_filter(): void
    {
        $user = User::factory()->admin()->create();
        $place = Place::factory()->create();
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('admin.resources.places.index', [
                'search' => $place->name,
            ]));
        $response->assertStatus(200);
    }

    public function test_can_update(): void
    {
        $user = User::factory()->admin()->create();
        $placeTypes = PlaceType::pluck('uuid');
        $locations = Location::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id,
                'locations_id'     => $locations->first()->id,
            ]
        );

        $name = Str::random(10);
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->put(route('admin.resources.places.update', ['uuid' => $place->uuid]),
                [
                    'location_uuid'   => $locations->last()->uuid->toString(),
                    'place_type_uuid' => $placeTypes->random(),
                    'name'            => $name,
                    'email'           => $this->faker->email,
                    'description'     => Str::random(10),
                    'status'          => Place::STATE_ACTIVE,
                ]
            );
        $response->assertStatus(303);
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.place_updated', ['place' => $name]));
        $response->assertLocation(route('admin.resources.places.index'));

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->put(route('admin.resources.places.update', ['uuid' => $place->uuid]), [
                'location_uuid' => Str::uuid()
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_cannot_update(): void
    {
        $user = User::factory()->admin()->create();
        $placeTypes = PlaceType::pluck('uuid');
        $locations = Location::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id,
                'locations_id'     => $locations->first()->id,
            ]
        );

        $name = Str::random(10);
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->put(route('admin.resources.places.update', ['uuid' => Str::uuid()->toString()]), [
                'location_uuid'   => $locations->last()->uuid->toString(),
                'place_type_uuid' => $placeTypes->random(),
                'name'            => $name,
                'email'           => $this->faker->email,
                'description'     => Str::random(10),
                'status'          => Place::STATE_ACTIVE,
            ]);

        $response->assertStatus(303);
        $response->assertSessionHas('toaster');
        $this->assertStringContainsString('No query results for model [App\Models\Place].', session('toaster')['message']);
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertLocation(route('admin.resources.places.index'));

    }

    public function test_can_create(): void
    {
        $user = User::factory()->admin()->create();
        $placeTypes = PlaceType::pluck('uuid');
        $locations = Location::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );

        $name = Str::random(10);
        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('admin.resources.places.store'),
                [
                    'location_uuid'   => $locations->last()->uuid->toString(),
                    'place_type_uuid' => $placeTypes->random(),
                    'name'            => $name,
                    'email'           => $this->faker->email,
                    'description'     => Str::random(10),
                    'status'          => Place::STATE_ACTIVE,
                ]
            );

        $response->assertStatus(303);
        $this->assertDatabaseHas('places', ['name' => $name]);

        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.place_created', ['place' => $name]));
        $response->assertStatus(303);
        $response->assertLocation(route('admin.resources.places.index'));
    }

    public function test_cannot_create(): void
    {
        $user = User::factory()->admin()->create();
        $placeTypes = PlaceType::pluck('uuid');
        $locations = Location::factory(10)->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('admin.resources.places.store'), [
                'location_uuid'   => $locations->last()->uuid->toString(),
                'place_type_uuid' => $placeTypes->random(),
                'description'     => Str::random(10),
                'status'          => Place::STATE_ACTIVE,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_can_delete(): void
    {
        $user = User::factory()->admin()->create();
        $place = Place::factory()->create(
            [
                'organisations_id' => $user->organisations_id,
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->delete(route('admin.resources.places.delete', ['uuid', $place->uuid]));


        $response->assertStatus(303);
    }
}
