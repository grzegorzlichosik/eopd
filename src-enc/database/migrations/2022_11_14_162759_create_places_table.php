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
        Schema::create('place_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->unsignedBigInteger('locations_id')->nullable();
            $table->unsignedBigInteger('place_types_id');
            $table->string('name');
            $table->string('external_id');
            $table->string('email');
            $table->boolean('is_active')->default(0);
            $table->json('metadata');
            $table->timestamps();

            $table->unique(['organisations_id', 'name']);
            $table->unique(['organisations_id', 'email']);

            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('locations_id')->references('id')->on('locations');
            $table->foreign('place_types_id')->references('id')->on('place_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
        Schema::dropIfExists('place_types');
    }
};
