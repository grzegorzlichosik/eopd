<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\UsersPage;
use Tests\DuskTestCase;

class AdminUsersTest extends DuskTestCase
{
    use WithFaker;

    private User $admin;

    private Collection $users;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_view_users(): void
    {
        $user = $this->users->random();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($this->admin)
                ->visit(new UsersPage())
                ->assertSee($user->email)
                ->assertSee($user->name)
                ->assertSee($this->admin->email)
                ->assertSee($this->admin->name);
        });
    }

    public function test_filter_users(): void
    {
        $user = $this->users->random();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($this->admin)
                ->visit(new UsersPage())
                ->typeSlowly('@global-search', $user->name)
                ->click('@search')
                ->pause(1000)
                ->assertSee($user->email)
                ->assertSee($user->name);
        });
    }

    public function test_delete_user(): void
    {
        $user = $this->users->random();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($this->admin)
                ->visit(new UsersPage())
                ->typeSlowly('@global-search', $user->name)
                ->click('@search')
                ->pause(1000)
                ->click('@delete-user-button')
                ->pause(1000)
                ->assertSee('Are you sure you want to delete user')
                ->pause(1000)
                ->click('@confirm-button')
                ->pause(1000)
                ->assertDontSee($user->email);
        });
    }

    public function test_edit_user(): void
    {
        $user = $this->users->whereNull('email_verified_at')->random();

        $this->browse(function (Browser $browser) use ($user) {
            $newName = $this->faker->name;

            $browser->loginAs($this->admin)
                ->visit(new UsersPage())
                ->typeSlowly('@global-search', $user->name)
                ->click('@search')
                ->pause(1000)
                ->click('@edit-user-button')
                ->pause(1000)
                ->assertSee('Edit User: ' . $user->name)
                ->typeSlowly('#name', $newName)
                ->click('@update_user')
                ->pause(1000)
                ->assertDontSee($user->name)
                ->assertSee($newName);
        });
    }

    public function test_create_user(): void
    {
        $this->browse(function (Browser $browser) {
            $name = $this->faker->name;
            $email = $this->faker->email;

            $browser->loginAs($this->admin)
                ->visit(new UsersPage())
                ->click('#invite_new_user')
                ->pause(1000)
                ->assertSee('Create User')
                ->pause(1000)
                ->typeSlowly('#name', $name)
                ->typeSlowly('#email', $email)
                ->pause(1000)
                ->click('@create_user')
                ->pause(1000)
                ->assertSee(trans('validation.please_select_at_one_roles'));
        });
    }

    private function prepare_test_data(): void
    {
        $this->admin = User::factory()->twoFactorConfirmed()->superAdmin()->create();

        $this->users = User::factory(mt_rand(1, 4))->agent()->create([
            'organisations_id' => $this->admin->organisations_id,
        ]);

        $this->users = $this->users->merge(
            User::factory(mt_rand(1, 4))->admin()->create([
                'organisations_id' => $this->admin->organisations_id,
            ])
        );

        $this->users = $this->users->merge(
            User::factory(mt_rand(1, 4))->developer()->unverified()->create([
                'organisations_id' => $this->admin->organisations_id,
            ])
        );
    }
}
