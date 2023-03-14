<?php

namespace Tests\Unit\Helpers;

include_once __DIR__ . '/../../../app/Helpers/helpers.php';

use Illuminate\Support\Str;
use Tests\TestCase;

class CleanAppendsTest extends TestCase
{

    public function test_cleanup_appends_helper()
    {
        $data = [
            'q' => Str::random(32),
            'param' => Str::random(32),
        ];

        $this->assertArrayNotHasKey('q', cleanupAppends($data));
        $this->assertArrayHasKey('param', cleanupAppends($data));
    }

}
