<?php

namespace Tests\Feature\Admin;

use App\Models\File;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use WithFaker;


    public function test_admin_can_view_locations(): void
    {
        $user = User::factory()->admin()->create();
        Location::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.resources.locations.index'));
        $response->assertStatus(302);

    }

    public function test_can_view_locations()
    {
        $user = User::factory()->admin()->create();
        Location::factory()->create();
        $this->withoutMiddleware()->actingAs($user)->get(route('admin.resources.locations.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Locations')

            );
    }

    public function test_can_view_locations_ascending()
    {
        $user = User::factory()->admin()->create();
        Location::factory()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.resources.locations.index', [
            'sortField' => 'name',
            'sortOrder' => '1'
        ]));
        $response->assertStatus(200);

    }

    public function test_can_view_descending()
    {
        $user = User::factory()->admin()->create();
        Location::factory()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.resources.locations.index', [
            'sortField' => 'name',
            'sortOrder' => '-1'
        ]));
        $response->assertStatus(200);
    }

    public function test_can_create_location()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();

        $name = Str::random(10);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.resources.locations.create'), [
                "name"         => $name,
                "short_name"   => Str::random(10),
                "country_code" => "IE",
                "phone"        => "019010430",
                "dial_code"    => "353",
                "address"      => Str::random(10),
                "postcode"     => mt_rand(),
                "city_town"    => Str::random(8),
                "timezone"     => "America/Adak",
                "location_lon" => -74.051089505341,
                "location_lat" => 40.689181918273,
                "file"         => UploadedFile::fake()->create('sample-pdf.pdf'),

            ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas(
            'locations',
            [
                'name' => $name,
            ]
        );

        $location = Location::where('name', request('name'))->first();

        if (request('file')) {
            $this->assertDatabaseHas(
                'files',
                [
                    'id' => $location->files_id
                ]
            );
        }

        $response->assertRedirect(route('admin.resources.locations.index'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.location_added', ['location' => request('name')]));
        $response->assertStatus(302);

        $file = File::find($location->files_id)->first();
        $response = $this->actingAs($admin)->get(route('admin.resources.locations.instructions', [$file->uuid]));
        $response->assertStatus(200);
    }

    public function test_cannot_create_location()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();

        $name = Str::random(10);
        $wrongInput =Str::random(300);
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.resources.locations.create'), [
                "name"         => $name,
                "short_name"   => Str::random(10),
                "country_code" => "IE",
                "phone"        => "019010430",
                "dial_code"    => "353",
                "address"      => Str::random(10),
                "postcode"     => mt_rand(),
                "city_town"    => Str::random(8),
                "timezone"     => "America/Adak",
                "location_lon" => $wrongInput,
                "location_lat" => 40.689181918273,
                "file"         => UploadedFile::fake()->create('sample-pdf.pdf'),

            ]);

        $response->assertRedirect(route('admin.resources.locations.index'));
        $response->assertSessionHas('toaster');
        $this->assertStringContainsString($wrongInput, session('toaster')['message']);
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(302);

    }

    public function test_can_update_file(): void
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();
        $location = Location::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.resources.locations.upload', [ 'uuid' => $location->uuid]), [
                "file"    => UploadedFile::fake()->create('sample-pdf.pdf'),

            ]);
        $response->assertStatus(302);

        if (request('file')) {
            $this->assertDatabaseHas(
                'files',
                [
                    'id' => $location->files_id
                ]
            );
        }

        $response->assertRedirect(route('admin.resources.locations.index'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.location_file_edited', ['location' => request('name')]));
        $response->assertStatus(302);
    }

    public function test_can_update_location()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();

        $name = Str::random(10);

        $location = Location::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.resources.locations.update', [ 'uuid' => $location->uuid]), [
                "name"         => $name,
                "short_name"   => Str::random(10),
                "country_code" => "IE",
                "phone"        => "019010430",
                "dial_code"    => "353",
                "address"      => Str::random(10),
                "postcode"     => mt_rand(),
                "city_town"    => Str::random(8),
                "timezone"     => "America/Adak",
                "location_lon" => -74.051089505341,
                "location_lat" => 40.689181918273,

            ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas(
            'locations',
            [
                'name' => $name,
            ]
        );

        $response->assertRedirect(route('admin.resources.locations.index'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.location_edited', ['location' => request('name')]));
        $response->assertStatus(302);

    }

    public function test_cannot_update_location()
    {
        $admin = User::factory()->admin()->twoFactorConfirmed()->create();

        $name = Str::random(10);

        $location = Location::factory()->create([
            'organisations_id' => $admin->organisations_id
        ]);
        $wrongInput = Str::random('300');
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.resources.locations.update', [ 'uuid' => $location->uuid]), [
                "name"         => $name,
                "short_name"   => Str::random(10),
                "country_code" => "IE",
                "phone"        => "019010430",
                "dial_code"    => "353",
                "address"      => Str::random(10),
                "postcode"     => mt_rand(),
                "city_town"    => Str::random(8),
                "timezone"     => "America/Adak",
                "location_lon" => $wrongInput,
                "location_lat" => 40.689181918273
            ]);
        $response->assertRedirect(route('admin.resources.locations.index'));
        $response->assertSessionHas('toaster');
        $this->assertStringContainsString($wrongInput, session('toaster')['message']);
        $this->assertEquals('error', session('toaster')['type']);
        $response->assertStatus(302);

    }

    public function test_can_filter(): void
    {
        $user = User::factory()->admin()->create();
        $location = Location::factory()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.resources.locations.index', [
            'search' => $location->name,
        ]));
        $response->assertStatus(200);
    }

}
