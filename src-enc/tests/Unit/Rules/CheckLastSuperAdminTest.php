<?php

namespace Tests\Unit\Rules;

use App\Models\Organisation;
use App\Models\User;
use App\Rules\CheckRoles;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CheckLastSuperAdminTest extends TestCase
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

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->delete(route('admin.users.delete', ['uuid' => $user->uuid]));

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

        $response = $this->actingAs($users[0])
            ->withoutMiddleware()
            ->delete(route('admin.users.delete', ['uuid' => $users[1]->uuid]));

        $response->assertSessionHasNoErrors();

    }

}
