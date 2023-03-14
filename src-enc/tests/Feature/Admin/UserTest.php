<?php

namespace Tests\Feature\Admin;

use App\Models\Organisation;
use App\Models\User;
use App\Notifications\Reset2FAUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Notifications\NewUserInvite;

class UserTest extends TestCase
{
    use WithFaker;

    protected const NAME = "name";
    protected const EMAIL = "name@email.com";
    private const TOKEN = "740c150368bd214ae0d5815cd8e4791583751b8d5cc920fb43988a3da924613f";

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function test_super_admin_can_view_users(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->actingAs($user)->get(route('admin.users'));
        $response->assertStatus(302);

    }

    public function test_super_admin_can_filter(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search'            => 'nnnnn',
            'role'              => trans('roles.super_administrator'),
            'email_verified_at' => 1
        ]));
        $response->assertStatus(200);

    }

    public function test_super_admin_can_ascending_order(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search'            => 'nnnnn',
            'role'              => trans('roles.super_administrator'),
            'email_verified_at' => 1,
            'sortField'         => 'email_verified_at',
            'sortOrder'         => '1'
        ]));
        $response->assertStatus(200);

    }
    public function test_super_admin_can_filter_descending_order(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search'            => 'nnnnn',
            'role'              => trans('roles.super_administrator'),
            'email_verified_at' => 1,
            'sortField'         => 'email_verified_at',
            'sortOrder'         => '-1'
        ]));
        $response->assertStatus(200);

    }

    public function test_super_admin_can_filter_administrator(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search' => 'nnnnn',
            'role'   => trans('roles.administrator'),
        ]));
        $response->assertStatus(200);

    }

    public function test_super_admin_can_filter_agent(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search' => 'nnnnn',
            'role'   => trans('roles.agent'),
        ]));
        $response->assertStatus(200);

    }

    public function test_can_view_users()
    {
        $user = User::factory()->superAdmin()->create();
        $this->withoutMiddleware()->actingAs($user)->get(route('admin.users'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Users')
                    ->has('users.data', 1)
                    ->where('users.data.0.active_user', true)
            );
    }

    public function test_admin_cannot_see_is_super_admin_flag(): void
    {
        $user = User::factory()->admin()->create();
        $this->withoutMiddleware()->actingAs($user)->get(route('admin.users'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/Users')
                    ->has('roles', 3)
            );
    }

    public function test_super_admin_can_filter_developer(): void
    {
        $user = User::factory()->superAdmin()->create();
        $response = $this->withoutMiddleware()->actingAs($user)->get(route('admin.users', [
            'search' => 'nnnnn',
            'role'   => trans('roles.developer'),
        ]));
        $response->assertStatus(200);
    }

    public function test_request_should_fail_when_no_email_is_provided(): void
    {
        $response = $this->withoutMiddleware()->postJson(route('admin.users.create'), [
            "name"     => self::NAME,
            'roles_id' => [trans('roles.super'), trans('roles.admin'), trans('roles.agent'), trans('roles.developer')],
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('email');
    }

    public function test_request_should_fail_when_no_name_is_provided(): void
    {
        $response = $this->withoutMiddleware()->postJson(route('admin.users.create'), [
            "email"    => self::EMAIL,
            'roles_id' => [trans('roles.super'), trans('roles.admin'), trans('roles.agent'), trans('roles.developer')],
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('name');
    }

    public function test_request_should_fail_when_no_roles_is_provided(): void
    {
        $response = $this->withoutMiddleware()->postJson(route('admin.users.create'), [
            "name"  => self::NAME,
            "email" => self::EMAIL,
        ]);

        $response->assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $response->assertJsonValidationErrors('roles');
    }

    public function test_create_user(): void
    {
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create();
        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.users.create'), [
                'name'  => self::NAME,
                'email' => self::EMAIL,
                'roles' => [
                    'is_agent',
                    'is_developer'
                ],
            ]);

        $this->assertDatabaseHas(
            'users',
            [
                'name'         => self::NAME,
                'email'        => self::EMAIL,
                'is_agent'     => 1,
                'is_developer' => 1,
            ]
        );

        $newUser = User::where('email', request('email'))->first();
        Notification::fake();
        Notification::assertNothingSent();
        Notification::send(
            $newUser,
            new NewUserInvite(self::TOKEN)
        );

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.user_created', ['user' => $newUser->name]));
        $response->assertStatus(302);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.users.create'), [
                'name'  => Str::random(2),
                'email' => Str::random(),
                'roles' => [
                    Str::random()
                ],
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);
        $response->assertStatus(302);

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.users.create'), [
                'name'  => Str::random(2),
                'email' => Str::random(),
                'roles' => [
                    Str::random()
                ],
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);
        $response->assertStatus(302);
    }

    public function test_update_user(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->developer()->unverified()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), [
                'name'  => self::NAME,
                'email' => self::EMAIL,
                'roles' => [
                    'is_admin'
                ],
            ]);

        $this->assertDatabaseHas(
            'users',
            [
                'name'     => self::NAME,
                'email'    => self::EMAIL,
                'is_admin' => 1,
            ]
        );

        $response->assertSessionHas('toaster');
        $this->assertEquals(session('toaster')['message'], trans('modals.user_updated_with_invite_email', ['user' => self::NAME]));
        $response->assertStatus(303);

        $user = $user->refresh();

        Notification::fake();
        Notification::assertNothingSent();
        Notification::send(
            $user,
            new NewUserInvite(self::TOKEN)
        );

        Notification::assertSentTo($user, NewUserInvite::class);

        $user = User::factory()->agent()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );

        $this->assertDatabaseHas(
            'users',
            [
                'name'     => $user->name,
                'email'    => $user->email,
                'is_agent' => 1,
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), [
                'name'  => self::NAME,
                'email' => self::EMAIL,
            ]);

        $response->assertSessionHasErrors(['email', 'roles']);
        $response->assertStatus(302);

        $user = User::factory()->agent()->unverified()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );

        $this->assertDatabaseHas(
            'users',
            [
                'name'     => $user->name,
                'email'    => $user->email,
                'is_agent' => 1,
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), [
                'name'  => Str::random(),
                'email' => Str::random(),
            ]);

        $response->assertSessionHasErrors(['email', 'roles']);
        $response->assertStatus(302);

    }

    public function test_can_delete_user(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->twoFactorConfirmed()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );
        $admin = User::factory()->superAdmin()->twoFactorConfirmed()->create(
            [
                'organisations_id' => $organisation->id
            ]
        );

        $response = $this->withoutMiddleware()
            ->actingAs($admin)
            ->delete(route('admin.users.delete', ['uuid' => $user->uuid]));

        $this->assertNull(User::find($user->id));
        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('toaster');
        $message = session('toaster')['message'];
        $this->assertEquals($message, trans('modals.user_deleted', ['user' => $user->name]));
        $response->assertStatus(302);

    }

}
