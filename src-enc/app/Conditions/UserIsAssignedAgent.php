<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class UserIsAssignedAgent extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return $this->stateableModel->agent_id === auth()->user()->id;
    }
}
