<?php

namespace Tests\Feature;

use Tests\TestCase;

class IframeTest extends TestCase
{
    public function test_iframe()
    {
        $number = random_int(1000, 9999);

        $this->withoutMiddleware();

        $response = $this->post('/iframe', [
            'number' => $number,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(200);
        $response->assertSee($number);
    }
}
