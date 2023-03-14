<?php

namespace Tests\Unit\Http\Middleware;


use App\Http\Middleware\IsPlatformOrganisation;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class IsPlatformOrganisationTest extends TestCase
{
    public function test_non_platform_organisation_users_are_redirected()
    {
        $user =User::factory()->admin()->create();

        $this->actingAs($user);

        $request = Request::create(route('platform.organisations.index'), 'GET');

        $middleware = new IsPlatformOrganisation();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response->getStatusCode(), 302);
    }


    public function test_platform_organisation_users_are_not_redirected()
    {
        $user = Organisation::where('is_platform', 1)->with('users')->first()->users()->first();

        $this->actingAs($user);

        $request = Request::create(route('platform.organisations.index'), 'GET');

        $middleware = new IsPlatformOrganisation();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response, null);
    }
}
