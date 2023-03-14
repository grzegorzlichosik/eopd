<?php

namespace Tests\Browser;

use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Agent\EncounterPage;
use Tests\DuskTestCase;

class AgentEncounterTest extends DuskTestCase
{
    use WithFaker;

    private User $agent;

    private Flow $flows;

    private Collection $encounters;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_encounter(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->agent)
                ->visit('/')
                ->assertSee('View My Schedule');

        });
    }

    public function test_view_calendar_encounter(): void
    {
        $encounter = $this->encounters->random();

        $this->browse(function (Browser $browser) use ($encounter) {
            $browser->loginAs($this->agent)
                ->visit('/agent/encounters/calendar')
                ->assertSee('Calendar');

        });
    }

    private function prepare_test_data(): void
    {
        $this->agent = User::factory()->twoFactorConfirmed()->agent()->create([
            'nylas_account_id' => Str::random()
        ]);

        $this->flows = Flow::factory()->create([
            'name' => Str::random(3),
            'organisations_id' => $this->agent->organisations_id,
        ]);
        $place = Place::factory()->create([
            'organisations_id' => $this->agent->organisations_id,
        ]);
        $this->encounters = Encounter::factory(mt_rand(1, 4))->create([
            'organisations_id' => $this->agent->organisations_id,
            'flows_id' => $this->flows->id,
            'places_id' => $place->id,
            'agent_id' => $this->agent->id
        ]);

    }
}
