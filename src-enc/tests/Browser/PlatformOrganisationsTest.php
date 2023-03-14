<?php

namespace Tests\Browser;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Platform\OrganisationsPage;
use Tests\DuskTestCase;

class PlatformOrganisationsTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;
    private Collection $organisations;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_organisations(): void
    {
        $this->browse(function (Browser $browser){
            $organisation = $this->organisations->random();
            $browser->loginAs($this->admin)
                ->visit(new OrganisationsPage())
                ->assertSee('Platform - Organisations');
        });
    }

    public function test_filter_organisations(): void
    {
        $organisation = $this->organisations->random();

        $this->browse(function (Browser $browser) use($organisation) {
            $browser->loginAs($this->admin)
                ->visit(new OrganisationsPage())
                ->typeSlowly('@global-search', $organisation->name)
                ->click('@search')
                ->pause(1000)
                ->assertSee($organisation->name);
        });
    }

    private function prepare_test_data(): void
    {
        $platformOrganisation = Organisation::where('is_platform', 1)->first();

        $this->admin = User::factory()->twoFactorConfirmed()
            ->create(['organisations_id' => $platformOrganisation->id]);

        $this->organisations = Organisation::factory(20)->create();
        foreach ($this->organisations as $organisation){
            $admins = User::factory(5)->admin()->unverified()->create(['organisations_id' => $organisation->id]);
            $organisation->created_by = $admins->first()->id;
            $organisation->save();
        }
    }
}
