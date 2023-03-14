<?php

namespace App\Notifications\Flow;

use App\Models\Flow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\StateMachineNotification;

class FlowInactivePublishPublished extends StateMachineNotification
{
    use Queueable;

    public function __construct(
        public Flow $flow,
    )
    {
    }

}
