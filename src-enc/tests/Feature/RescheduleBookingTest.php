<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\Flow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class RescheduleBookingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_reschedule_booking_page_redirected(): void
    {
        $encounter = Encounter::factory()->create();
        $response = $this->withoutMiddleware()
            ->get(route('encounters.booking.post.reschedule', ['uuid' => $encounter->uuid]));
        $response->assertStatus(200);
    }
}
