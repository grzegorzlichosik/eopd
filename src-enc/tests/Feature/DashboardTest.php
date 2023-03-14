<?php

namespace Tests\Feature;

use App\Models\Holding\Procedure;
use App\Models\User;
use App\Models\UsersFacility;
use App\Models\UsersLatestLogin;
use Carbon\Carbon;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class DashboardTest extends TestCase
{
    public function setup(): void
    {
        parent::setUp();
    }

    public function test_can_view_dashboard()
    {
        $user = User::factory()->twoFactorConfirmed()->create();
        UsersLatestLogin::factory(5)->create([
            'users_id' => $user->id
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Dashboard')
                    ->has('user')
            );
    }

}
