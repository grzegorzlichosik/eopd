<?php

namespace Tests\Unit\Http\Middleware;


use App\Http\Middleware\IsSuperAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class IsSuperAdminTest extends TestCase
{
    public function test_non_super_admins_are_redirected()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $request = Request::create('/superAdmin/users', 'GET');

        $middleware = new IsSuperAdmin();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response->getStatusCode(), 302);
    }


    public function test_super_admins_are_not_redirected()
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user);

        $request = Request::create('/superAdmin/users', 'GET');

        $middleware = new IsSuperAdmin();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response, null);
    }
}
