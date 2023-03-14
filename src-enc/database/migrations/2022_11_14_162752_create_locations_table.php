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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->string('name');
            $table->string('short_name', 40);
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city_town')->nullable();
            $table->string('phone')->nullable();
            $table->string('location_lat')->nullable();
            $table->string('location_lon')->nullable();
            $table->string('timezone')->nullable()->default('UTC');
            $table->timestamps();

            $table->unique(['organisations_id', 'name']);

            $table->foreign('organisations_id')->references('id')->on('organisations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
