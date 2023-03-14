<?php


namespace tests\Unit\Actions\Flow;

use App\Actions\FlowInactiveEditInactive as FlowInactiveEditInactiveAction;
use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowInactiveEditInactiveListener;
use App\Events\Flow\FlowInactiveEditInactive;

class FlowInactiveEditInactiveTest extends TestCase
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

        $action = new FlowInactiveEditInactiveAction($flow, $user);
        $action->execute();

        $listener = app()->make(FlowInactiveEditInactiveListener::class);
        $event = new FlowInactiveEditInactive($flow, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($flow->id, $event->flow->id);
    }

}
