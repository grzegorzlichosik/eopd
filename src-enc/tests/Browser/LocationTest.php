<?php

namespace Tests\Browser;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\LocationsPage;
use Tests\DuskTestCase;

class LocationTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Collection $pools;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_locations(): void
    {
        $location = $this->locations->random();

        $this->browse(function (Browser $browser) use ($location) {
            $browser->loginAs($this->admin)
                ->visit(new LocationsPage())
                ->assertSee($location->name);

        });
    }

    public function test_create_location(): void
    {
        $this->browse(function (Browser $browser) {
            $name = Str::random(2);

            $browser->loginAs($this->admin)
                ->visit(new LocationsPage())
                ->click('#create_location')
                ->pause(1000)
                ->assertSee('Create Location')
                ->pause(1000)
                ->typeSlowly('#short_name', $name)
                ->pause(1000)
                ->click('@create_location')
                ->pause(1000)
                ->assertSee(trans('validation.short_name_min', ['min' => 3]));
        });
    }

    public function test_edit_location(): void
    {
        $this->browse(function (Browser $browser) {
            $name = Str::random(2);

            $browser->loginAs($this->admin)
                ->visit(new LocationsPage())
                ->click('#edit_location')
                ->pause(1000)
                ->assertSee('Edit Location')
                ->pause(1000)
                ->typeSlowly('#short_name', $name)
                ->pause(1000)
                ->click('@create_location')
                ->pause(1000)
                ->assertSee(trans('validation.short_name_min', ['min' => 3]));
        });
    }



    private function prepare_test_data(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create();

        $this->locations = Location::factory(mt_rand(1, 4))->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);

    }
}
