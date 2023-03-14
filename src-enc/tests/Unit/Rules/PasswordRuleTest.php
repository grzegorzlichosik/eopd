<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\Password;

class PasswordRuleTest extends TestCase
{

    public function test_rule_success()
    {
        $rule = (new Password)->length(12)
            ->requireUppercase()
            ->requireNumeric()
            ->requireLowercase();

        $rules = [
            'password1' => $rule,
            'password2' => $rule,
        ];

        $data = [
            'password1' => '44gfQZwertyyy',
            'password2' => '4ghQwertyyy!4Z',
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertTrue($v->passes());
    }

    public function test_rule_fail()
    {
        $rule = (new Password)->length(12)
            ->requireUppercase()
            ->requireNumeric()
            ->requireLowercase();

        $rules = [
            'password1' => $rule,
        ];

        $data = [
            'password1' => 'password',
        ];

        $v = $this->app['validator']->make($data, $rules);
        $this->assertFalse($v->passes());
    }

}
