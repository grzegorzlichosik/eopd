<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UsersLatestLogin;
use Tests\TestCase;

class UserLatestLoginTest extends TestCase
{
    public function test_latest_logins(): void
    {
        $userLatestLogin = UsersLatestLogin::factory()->create();
        $this->assertModelExists($userLatestLogin);
    }

    public function test_users_latest_login_relation(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $usersLatestLogin = UsersLatestLogin::factory()->create([
            'users_id' => $user->id,
        ]);

        $this->assertDatabaseHas(
            'users_latest_logins',
            [
                'users_id' => $user->id,
            ]
        );

        $this->assertInstanceOf(User::class, $usersLatestLogin->user);
        $this->assertEquals($usersLatestLogin->users_id, $user->id);
    }

}
