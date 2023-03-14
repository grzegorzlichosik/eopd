<?php

namespace Tests\Feature;

use App\Models\Encounter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CancelBookingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cancel_booking(): void
    {
        $encounter = Encounter::factory()->create();
        $response = $this->withoutMiddleware()->get(route('encounters.booking.post.cancel', ['uuid' => $encounter->uuid]));
        $response->assertStatus(200);
    }
}
