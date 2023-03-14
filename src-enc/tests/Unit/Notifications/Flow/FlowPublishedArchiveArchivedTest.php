<?php

namespace tests\Unit\Notifications\Flow;

use App\Models\User;
use App\Models\Flow;
use App\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\Flow\FlowPublishedArchiveArchived;

class FlowPublishedArchiveArchivedTest extends TestCase
{
    public function test_notification(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        $user = User::factory()->create();
        $flow = Flow::factory()->create(
            [
                'organisations_id' => $user->organisations_id
            ]
        );

        Notification::send(
            $user,
            new FlowPublishedArchiveArchived($flow)
        );

        $testData = [
            'salutation' => BaseNotification::getSalutation(),
            'line1'      => 'Flow name: ' . $flow->name,
        ];

        Notification::assertSentTo(
            $user,
            FlowPublishedArchiveArchived::class,
            function ($notification, $channels, $notifiable) use ($testData, $flow) {

                $mailData = $notification->toMail($notifiable)->toArray();

                $this->assertEquals($notification->getStateMachineNotificationSubject(), $mailData['subject']);
                $this->assertEquals('Hello ' . $notifiable['name'] . ',', $mailData['greeting']);
                $this->assertEquals($testData['line1'], $mailData['introLines'][0]);
                $this->assertEquals($testData['salutation'], $mailData['salutation']);

                return true;
            });
    }
}
