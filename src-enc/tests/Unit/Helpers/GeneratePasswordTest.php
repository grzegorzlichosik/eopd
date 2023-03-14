<?php

namespace Tests\Unit\Helpers;

include_once __DIR__ . '/../../../app/Helpers/helpers.php';

use Tests\TestCase;

class GeneratePasswordTest extends TestCase
{

    public function test_generate_passwords_helper()
    {
        $password = generatePassword();
        $this->assertIsString($password);
        $this->assertEquals(32, strlen($password));

        $password = generatePassword(16);
        $this->assertIsString($password);
        $this->assertEquals(16, strlen($password));
    }
}
