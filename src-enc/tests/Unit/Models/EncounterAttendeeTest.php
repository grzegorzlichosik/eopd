<?php

namespace Tests\Unit\Models;

use App\Models\Channel;
use App\Models\Encounter;
use App\Models\EncounterAttendee;
use App\Models\FlowType;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Tests\TestCase;

class EncounterAttendeeTest extends TestCase
{
    public function test_encounter_attendee(): void
    {
        EncounterAttendee::factory(10)->create()->each(function ($attendee) {
            $this->assertDatabaseHas(
                'encounter_attendees',
                [
                    'id' => $attendee->id,
                ]
            );
        });

        $attendee = EncounterAttendee::factory()->create();
        $this->assertModelExists($attendee);

        $attendee = EncounterAttendee::factory()->create();
        $attendee->delete();
        $this->assertModelMissing($attendee);

        EncounterAttendee::factory(10)->original()->create()->each(function ($attendee) {
            $this->assertDatabaseHas(
                'encounter_attendees',
                [
                    'id'          => $attendee->id,
                    'is_original' => 1,
                ]
            );
        });

        EncounterAttendee::factory(10)->accepted()->create()->each(function ($attendee) {
            $this->assertDatabaseHas(
                'encounter_attendees',
                [
                    'id'          => $attendee->id,
                    'is_accepted' => 1,
                ]
            );
        });
    }

    public function test_encounter_attendee_encounter_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);

        $attendees = EncounterAttendee::factory(10)->create(
            [
                'encounters_id' => $encounter->id,
            ]
        )
            ->each(function ($attendee)  use ($encounter){
                $this->assertDatabaseHas(
                    'encounter_attendees',
                    [
                        'encounters_id' => $encounter->id,
                    ]
                );
            });

        $this->assertEquals(10, $encounter->attendees->count());
        $this->assertInstanceOf(Encounter::class, $attendees->random()->encounter);
    }

}
