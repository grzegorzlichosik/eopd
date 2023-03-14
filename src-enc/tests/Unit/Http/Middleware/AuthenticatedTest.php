<?php

namespace Tests\Unit\Http\Middleware;

use App\Models\User;
use Tests\TestCase;

class AuthenticatedTest extends TestCase
{
    public function test_authenticated()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
    }
}
