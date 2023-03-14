<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class EncounterDataRequired extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return true;
    }
}
