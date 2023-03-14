<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Rules\MustBeValidEmail;

class MustBeValidEmailTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function test_rule_success()
    {

        $rules = [
            'email' => new MustBeValidEmail,
        ];

        $data = [
            'email' => $this->faker->email,
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    public function test_rule_fail()
    {
        $rules = [
            'email' => new MustBeValidEmail,
        ];

        $data = [
            'email' => $this->faker->name,
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertFalse($v->passes());
    }

}
