<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Rules\MustBeValidPhone;

class MustBeValidPhoneTest extends TestCase
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
            'phone' => new MustBeValidPhone,
        ];

        $data = [
            'phone' => $this->faker->e164PhoneNumber,
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    public function test_rule_fail()
    {
        $rules = [
            'phone' => new MustBeValidPhone,
        ];

        $data = [
            'phone' => $this->faker->text(12),
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertFalse($v->passes());
    }

}
