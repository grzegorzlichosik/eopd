<?php

namespace App\Conditions;

use Threadable\StateMachine\Components\StateMachineBaseCondition;

class UserIsAdminOrSuperAdminOrFlowCoordinator extends StateMachineBaseCondition
{
    public function execute(): bool
    {
        return
            auth()->user()->is_admin
            ||
            auth()->user()->is_super_admin
            ||
            $this->isUserFlowCoordinator();
    }

   private function isUserFlowCoordinator(): bool
   {
       return true;
   }

}
