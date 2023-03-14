<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class PlaceDataRequired extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return true;
    }

}
