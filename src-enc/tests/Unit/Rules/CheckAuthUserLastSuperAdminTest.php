<?php

namespace Tests\Unit\Rules;

use App\Models\Organisation;
use App\Models\User;
use App\Rules\CheckRoles;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CheckAuthUserLastSuperAdminTest extends TestCase
{
    use WithFaker;

    public function setup(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function test_rule_fail()
    {
        $user = User::factory()->superAdmin()->create();

        $payload = [
            'roles' => ['is_admin']
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->put(route('admin.users.update', ['uuid' => $user->uuid]), $payload);

        $response->assertSessionHasErrors();

    }

    public function test_rule_success()
    {
        $organisation = Organisation::factory()->create();
        $users = User::factory(2)->superAdmin()->create(
            [
                'organisations_id' => $organisation->id,
            ]
        );

        $payload = [
            'roles' => ['is_agent']
        ];

        $response = $this->actingAs($users[0])
            ->withoutMiddleware()
            ->put(route('admin.users.update', ['uuid' => $users[0]->uuid]), $payload);

        $response->assertSessionHasNoErrors();

    }

}
