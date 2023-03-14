<?php

namespace Tests\Browser;

use App\Models\Pool;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\PoolsPage;
use Tests\DuskTestCase;

class AdminPoolTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Collection $pools;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_pools(): void
    {
        $pool = $this->pools->random();

        $this->browse(function (Browser $browser) use ($pool) {
            $browser->loginAs($this->admin)
                ->visit(new PoolsPage())
                ->assertSee($pool->name);

        });
    }

    public function test_create_pool(): void
    {
        $this->browse(function (Browser $browser) {
            $name = Str::random(2);

            $browser->loginAs($this->admin)
                ->visit(new PoolsPage())
                ->click('#create_new_pool')
                ->pause(1000)
                ->assertSee('Create Pool')
                ->pause(1000)
                ->typeSlowly('#name', $name)
                ->pause(1000)
                ->click('@create_pool')
                ->pause(1000)
                ->assertSee(trans('validation.name_min', ['min' => 3]));
        });
    }

    private function prepare_test_data(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->superAdmin()->create();

        $this->pools = Pool::factory(mt_rand(1, 4))->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);

    }
}
