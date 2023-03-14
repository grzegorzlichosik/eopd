<?php

namespace tests\Unit\Listeners\Flow;

use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowInactiveEditInactiveListener;
use App\Events\Flow\FlowInactiveEditInactive as FlowInactiveEditInactiveEvent;
use App\Notifications\Flow\FlowInactiveEditInactive as FlowInactiveEditInactiveNotification;

class FlowInactiveEditInactiveTest extends TestCase
{
    public function test_listener(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $flow = Flow::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $listener = app()->make(FlowInactiveEditInactiveListener::class);
        $event = new  FlowInactiveEditInactiveEvent($flow, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($flow->id, $event->flow->id);
    }

    public function test_listener_notification(): void
    {
        Event::fake();
        Notification::fake();
        $user = User::factory()->admin()->create();
        $flow = Flow::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $listener = app()->make(FlowInactiveEditInactiveListener::class);
        $event = new FlowInactiveEditInactiveEvent($flow, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            FlowInactiveEditInactiveNotification::class,
            function ($notification, $channels) use ($flow) {
                return $notification->flow->id === $flow->id;
            }
        );
    }
}
