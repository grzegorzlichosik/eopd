<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    public function testIsAvailableInTheScheduler()
    {
        /** @var \Illuminate\Console\Scheduling\Schedule $schedule */
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        $events = collect($schedule->events())->filter(function (\Illuminate\Console\Scheduling\Event $event) {
            return stripos($event->command, 'whencounter:get_resources');
        });

        if ($events->count() == 0) {
            $this->fail('No events found');
        }

        $events->each(function (\Illuminate\Console\Scheduling\Event $event) {
            $this->assertEquals('*/2 * * * *', $event->expression);
        });
    }

    public function testEncounterChangeStatusIsAvailableInTheScheduler()
    {
        /** @var \Illuminate\Console\Scheduling\Schedule $schedule */
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        $events = collect($schedule->events())->filter(function (\Illuminate\Console\Scheduling\Event $event) {
            return stripos($event->command, 'whencounter:change_encounter_status');
        });

        if ($events->count() == 0) {
            $this->fail('No events found');
        }

        $events->each(function (\Illuminate\Console\Scheduling\Event $event) {
            $this->assertEquals('* * * * *', $event->expression);
        });
    }
}
