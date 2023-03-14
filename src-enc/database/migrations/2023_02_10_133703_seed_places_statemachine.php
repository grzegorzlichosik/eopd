<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\TsmState;
use App\Models\TsmTransitionConfig;
use App\Models\TsmTransition;
use Illuminate\Support\Facades\DB;

class SeedPlacesStatemachine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $statemachine_spec = resource_path('statemachines/Place_1.json');
        $spec = \json_decode(file_get_contents($statemachine_spec));
        DB::beginTransaction();
        try {
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $statemachine_spec = resource_path('statemachines/Place_1.json');
        $spec = \json_decode(file_get_contents($statemachine_spec));
        $class = $spec->configuration->stateable_class;
        $states = implode(',', array_map(function ($val) {
            return $val->id;
        }, DB::select("SELECT id FROM tsm_states WHERE class='$class'")));

        $transition_configs = implode(',', array_map(function ($val) {
            return $val->tsm_transition_configs_id;
        }, DB::select("SELECT tsm_transition_configs_id FROM tsm_transitions WHERE from_states_id IN ($states)")));

        $transitions = implode(',', array_map(function ($val) {
            return $val->id;
        }, DB::select("SELECT id FROM tsm_transitions WHERE from_states_id IN ($states)")));

        DB::delete("DELETE FROM tsm_transitions WHERE id IN ($transitions)");
        DB::delete("DELETE FROM tsm_transition_configs WHERE id IN ($transition_configs)");
        DB::delete("DELETE FROM tsm_states WHERE id IN ($states)");
    }
}
