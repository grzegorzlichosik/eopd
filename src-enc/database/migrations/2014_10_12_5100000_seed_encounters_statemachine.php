<?php

use App\Models\TsmState;
use App\Models\TsmTransition;
use App\Models\TsmTransitionConfig;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $statemachine_spec = resource_path("statemachines/Encounter_1.json");
        $spec = \json_decode(file_get_contents($statemachine_spec));

        foreach ($spec->statemachine as $state_name => $val) {
            $final = 1;
            if ($val->transitions != null) {
                $final = count($val->transitions) == 0;
            }
            $state = new TsmState();
            $state->class = $spec->configuration->stateable_class;
            $state->name = $state_name;
            $state->initial = $val->initial;
            $state->final = $final;
            $state->save();
            $states[$state_name] = $state->id;
        }

        foreach ($spec->statemachine as $state_name => $val) {
            foreach ($val->transitions as $transition_val) {

                $transition_config = new TsmTransitionConfig();
                $transition_config->trigger = $transition_val->trigger;
                $transition_config->condition_type = $transition_val->condition_type;
                $transition_config->condition = $transition_val->condition ?? null;
                $transition_config->action = $transition_val->action ?? null;
                $transition_config->validation = $transition_val->validation ?? null;
                $transition_config->save();

                $transition = new TsmTransition();
                $transition->name = $transition_val->name;
                $transition->from_states_id = $states[$state_name];
                $transition->to_states_id = $states[$transition_val->to_state];
                $transition->tsm_transition_configs_id = $transition_config->id;
                $transition->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $statemachine_spec = resource_path("statemachines/Encounter_1.json");
        $spec = \json_decode(file_get_contents($statemachine_spec));
        $class = $spec->configuration->stateable_class;
        $states =  TsmState::where('class', "$class")->pluck('id')->toArray();
        $transition_configs = TsmTransition::whereIn('from_states_id', $states)->pluck('tsm_transition_configs_id')->toArray();
        $transitions = TsmTransition::whereIn('from_states_id', $states)->pluck('id')->toArray();

        TsmTransition::whereIn('id', $transitions)->forceDelete();
        TsmTransitionConfig::whereIn('id', $transition_configs)->forceDelete();
        TsmState::whereIn('id', $states)->forceDelete();
    }
};
