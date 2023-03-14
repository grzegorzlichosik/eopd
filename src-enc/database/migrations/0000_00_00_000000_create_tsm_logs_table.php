<?php

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
        Schema::create('tsm_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->nullable();
            $table->bigInteger('stateable_id');
            $table->text('stateable_class');
            $table->json('stateable_data');
            $table->string('old_state');
            $table->string('new_state');
            $table->string('transition');
            $table->boolean('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsm_logs');
    }
};
