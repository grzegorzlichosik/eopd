<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;

class QueueNotification extends StateMachineBaseAction
{
    public function execute()
    {
        return false;
    }
}
