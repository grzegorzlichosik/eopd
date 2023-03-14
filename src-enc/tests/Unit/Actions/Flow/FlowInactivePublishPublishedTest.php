<?php


namespace tests\Unit\Actions\Flow;

use App\Actions\FlowInactivePublishPublished as FlowInactivePublishPublishedAction;
use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowInactivePublishPublishedListener;
use App\Events\Flow\FlowInactivePublishPublished;

class FlowInactivePublishPublishedTest extends TestCase
{
    public function test_action(): void
    {
        Event::fake();
        Notification::fake();
        $user = User::factory()->create();
        $flow = Flow::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        $action = new FlowInactivePublishPublishedAction($flow, $user);
        $action->execute();

        $listener = app()->make(FlowInactivePublishPublishedListener::class);
        $event = new FlowInactivePublishPublished($flow, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($flow->id, $event->flow->id);
    }

}
