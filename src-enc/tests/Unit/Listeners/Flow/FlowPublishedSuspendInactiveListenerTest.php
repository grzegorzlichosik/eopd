<?php

namespace tests\Unit\Listeners\Flow;

use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowPublishedSuspendInactiveListener;
use App\Events\Flow\FlowPublishedSuspendInactive as FlowPublishedSuspendInactiveEvent;
use App\Notifications\Flow\FlowPublishedSuspendInactive as FlowPublishedSuspendInactiveNotification;

class FlowPublishedSuspendInactiveTest extends TestCase
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

        $listener = app()->make(FlowPublishedSuspendInactiveListener::class);
        $event = new  FlowPublishedSuspendInactiveEvent($flow, $user);
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

        $listener = app()->make(FlowPublishedSuspendInactiveListener::class);
        $event = new FlowPublishedSuspendInactiveEvent($flow, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            FlowPublishedSuspendInactiveNotification::class,
            function ($notification, $channels) use ($flow) {
                return $notification->flow->id === $flow->id;
            }
        );
    }
}
