<?php

namespace Tests\Unit\Helpers;

use App\Models\Encounter;
use Tests\TestCase;

class ChangeEncounterStatusTest extends TestCase
{

    public function test_command_success(): void
    {
        $endAt = now()->startOfMinute()->subHour()->subMinute();

        $encounters = Encounter::factory(10)->create(
            [
                'scheduled_at' => $endAt->clone()->subMinutes(30),
                'ends_at'      => $endAt,
            ]
        );

        $this->artisan('whencounter:change_encounter_status')
            ->assertSuccessful();

        $encounters->each(function ($encounter) {
            $encounter->refresh();
            $this->assertEquals(Encounter::STATE_FINISHED, $encounter->tsm_current_state);
        });
    }
}
