<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class FlowDataIsRequired extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return $this->stateableModel !== null;
    }

}
