<?php

namespace Tests\Browser;

use App\Models\Location;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\LocationsPage;
use Tests\Browser\Pages\Admin\PlacesPage;
use Tests\DuskTestCase;

class PlacesTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Collection $places;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_places(): void
    {
        $place = $this->places->random();

        $this->browse(function (Browser $browser) use ($place) {
            $browser->loginAs($this->admin)
                ->visit(new PlacesPage())
                ->assertSee($place->name);

        });
    }

    public function test_show_place_details(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new PlacesPage())
                ->click('#show_place_details')
                ->pause(1000)
                ->assertSee('Name');

        });
    }

    public function test_create_place(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new PlacesPage())
                ->click('#create_new_place')
                ->pause(1000)
                ->assertSee('Create Place')
                ->typeSlowly('#description', Str::random(2))
                ->pause(1000)
                ->click('@create_place')
                ->pause(1000)
                ->assertSee(trans('validation.description_min', ['min' => 3]));

        });
    }

    public function test_edit_place(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->loginAs($this->admin)
                ->visit(new PlacesPage())
                ->click('@edit_place')
                ->pause(1000)
                ->assertSee('Edit Place')
                ->typeSlowly('@edit_place_description', Str::random(2))
                ->pause(1000)
                ->click('@submit_edit_place')
                ->pause(1000)
                ->assertSee(trans('validation.description_min', ['min' => 3]));

        });
    }

    private function prepare_test_data(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->admin()->create();
        $locations = Location::factory()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);
        $this->places = Place::where('organisations_id', $this->admin->organisations_id)->get();
        if ($this->places->isEmpty()) {
            for ($i = 1; $i <= 5; $i++) {
                $this->places->push(
                    Place::factory()->create(
                        [
                            'organisations_id' => $this->admin->organisations_id,
                            'name'             => 'Place ' . $i,
                            'locations_id'     => $locations->id,
                        ]
                    )
                );
            }
        } else {
            $this->places->each(function($place){
                $place->is_active = 1;
                $place->save();
            });
        }

    }
}
