<?php

use App\Models\TsmTransitionConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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

        Schema::create('tsm_states', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->string('name');
            $table->tinyInteger('initial')->default(0);
            $table->tinyInteger('final')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tsm_transition_configs', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->enum('trigger', [TsmTransitionConfig::TRIGGER_AUTO, TsmTransitionConfig::TRIGGER_COMMAND, TsmTransitionConfig::TRIGGER_TIMER])->default(TsmTransitionConfig::TRIGGER_COMMAND);
            $table->enum('condition_type', [TsmTransitionConfig::CONDITION_ALWAYS, TsmTransitionConfig::CONDITION_ACTION, TsmTransitionConfig::CONDITION_OTHERWISE, TsmTransitionConfig::CONDITION_EXPRESSION])->default(TsmTransitionConfig::CONDITION_ALWAYS);
            $table->json('condition')->nullable();
            $table->string('expression')->nullable();
            $table->json('action')->nullable();
            $table->json('validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tsm_transitions', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('name');
            $table->bigInteger('from_states_id')->unsigned()->index('from_states_id_foreign');
            $table->bigInteger('to_states_id')->unsigned()->index('to_states_id_foreign');
            $table->bigInteger('tsm_transition_configs_id')->unsigned()->index('tsm_transition_configs_id_foreign');
            $table->timestamps();
            $table->foreign('from_states_id')->references('id')->on('tsm_states')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('to_states_id')->references('id')->on('tsm_states')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('tsm_transition_configs_id')->references('id')->on('tsm_transition_configs')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->softDeletes();
        });

        Schema::create('tsm_custom_transitions', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->bigInteger('tsm_transitions_id')->unsigned()->index('tsm_transitions_id_foreign');
            $table->bigInteger('references_id')->unsigned()->index('reference_id_foreign');
            $table->bigInteger('tsm_transition_configs_id')->unsigned()->index('tsm_transitions_config_id_foreign');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tsm_transitions_id')->references('id')->on('tsm_transitions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('tsm_transition_configs_id')->references('id')->on('tsm_transition_configs')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsm_custom_transitions');
        Schema::dropIfExists('tsm_transitions');
        Schema::dropIfExists('tsm_transition_configs');
        Schema::dropIfExists('tsm_states');
    }
};
