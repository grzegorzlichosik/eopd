<?php

namespace tests\Unit\Listeners\Flow;

use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowInactivePublishPublishedListener;
use App\Events\Flow\FlowInactivePublishPublished as FlowInactivePublishPublishedEvent;
use App\Notifications\Flow\FlowInactivePublishPublished as FlowInactivePublishPublishedNotification;

class FlowInactivePublishPublishedTest extends TestCase
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

        $listener = app()->make(FlowInactivePublishPublishedListener::class);
        $event = new  FlowInactivePublishPublishedEvent($flow, $user);
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

        $listener = app()->make(FlowInactivePublishPublishedListener::class);
        $event = new FlowInactivePublishPublishedEvent($flow, $user);
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            FlowInactivePublishPublishedNotification::class,
            function ($notification, $channels) use ($flow) {
                return $notification->flow->id === $flow->id;
            }
        );
    }
}