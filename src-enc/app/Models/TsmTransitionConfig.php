<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TsmTransitionConfig extends Model
{
    use SoftDeletes;

    protected $table = 'tsm_transition_configs';

    const TRIGGER_AUTO = 'AUTO';
    const TRIGGER_COMMAND = 'COMMAND';
    const TRIGGER_TIMER = 'TIMER';

    const CONDITION_ALWAYS = 'ALWAYS';
    const CONDITION_EXPRESSION = 'EXPRESSION';
    const CONDITION_ACTION = 'ACTION';
    const CONDITION_OTHERWISE = 'OTHERWISE';

    const CONDITION_TYPE_PHP = "PHP";
    const CONDITION_TYPE_LAMBDA = "LAMBDA";


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'condition' => 'array',
        'action' => 'array',
        'validation' => 'array'
    ];
}
