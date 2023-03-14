<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createEncountersTable();
        $this->createEncounterAttendeesTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encounter_attendees');
        Schema::dropIfExists('encounters');
    }

    private function createEncountersTable(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->unsignedBigInteger('flows_id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('places_id')->nullable();
            $table->string('tsm_current_state');
            $table->json('metadata')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamps();

            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('flows_id')->references('id')->on('flows');
            $table->foreign('agent_id')->references('id')->on('users');
            $table->foreign('places_id')->references('id')->on('places');
        });
    }

    private function createEncounterAttendeesTable(): void
    {
        Schema::create('encounter_attendees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('encounters_id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->boolean('is_original');
            $table->boolean('is_accepted');
            $table->timestamps();

            $table->foreign('encounters_id')->references('id')->on('encounters');
        });
    }
};
