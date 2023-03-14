<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class UserIsAdmin extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return (bool)auth()->user()->is_admin;
    }
}
