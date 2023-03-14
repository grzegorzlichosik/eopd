<?php


namespace tests\Unit\Actions\Flow;

use App\Actions\FlowInactiveArchiveArchived as FlowInactiveArchiveArchivedAction;
use App\Models\Flow;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Listeners\Flow\FlowInactiveArchiveArchivedListener;
use App\Events\Flow\FlowInactiveArchiveArchived;

class FlowInactiveArchiveArchivedTest extends TestCase
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

        $action = new FlowInactiveArchiveArchivedAction($flow, $user);
        $action->execute();

        $listener = app()->make(FlowInactiveArchiveArchivedListener::class);
        $event = new FlowInactiveArchiveArchived($flow, $user);
        $listener->handle($event);

        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($flow->id, $event->flow->id);
    }

}
