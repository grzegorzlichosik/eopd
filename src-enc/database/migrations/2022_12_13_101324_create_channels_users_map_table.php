<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels_users_map', function (Blueprint $table) {
            $table->unsignedBigInteger('flows_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('channels_id')->default(null);
            $table->timestamps();

            $table->primary(['flows_id', 'users_id', 'channels_id']);

            $table->foreign('flows_id')->references('id')->on('flows');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('channels_id')->references('id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels_users_map');
    }
};
