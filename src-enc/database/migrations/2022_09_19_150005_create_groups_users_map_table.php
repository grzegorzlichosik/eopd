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
        Schema::create('groups_users_map', function (Blueprint $table) {
            $table->unsignedBigInteger('groups_id');
            $table->unsignedBigInteger('users_id');
            $table->timestamps();

            $table->primary(['groups_id', 'users_id']);

            $table->foreign('groups_id')->references('id')->on('groups');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups_users_map');
    }
};
