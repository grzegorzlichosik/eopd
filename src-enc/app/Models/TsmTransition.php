<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Threadable\StateMachine\Components\StateableModelInterface;
use Illuminate\Database\Eloquent\Model;

class TsmTransition extends Model
{
    use SoftDeletes;

    protected $table = 'tsm_transitions';

    public function transition_config()
    {
        return $this->belongsTo(TsmTransitionConfig::class, 'tsm_transition_configs_id', 'id');
    }

    public function getConfig(StateableModelInterface $stateableModelInterface): TsmTransitionConfig
    {
        return $this->transition_config;
    }

    public function from_state()
    {
        return $this->belongsTo(TsmState::class, 'from_states_id', 'id');
    }

    public function to_state()
    {
        return $this->belongsTo(TsmState::class, 'to_states_id', 'id');
    }
}
