<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class UserIsAttendee extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return true;
    }
}
