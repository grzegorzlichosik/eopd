<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckRoles;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CheckRolesTest extends TestCase
{
    use WithFaker;

    public function test_rule_success()
    {

        $rules = [
            'roles' => new CheckRoles(),
        ];

        $data = [
            'roles' => ['is_admin'],
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    public function test_rule_fail()
    {
        $rules = [
            'email' => new CheckRoles,
        ];

        $data = [
            'email' => [Str::random()],
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertFalse($v->passes());
    }

}
