<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class UserIsOriginalAttendee extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return true;
    }
}
