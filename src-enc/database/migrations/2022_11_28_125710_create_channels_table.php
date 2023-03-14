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
        $this->createChannelTable();
        $this->createChannelsPlacesTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels_places_map');
        Schema::dropIfExists('channels');
    }

    private function createChannelTable(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->unsignedBigInteger('flows_id');
            $table->unsignedBigInteger('channel_types_id');
            $table->integer('max_participants')->default(1)->nullable();
            $table->boolean('is_auto_confirm')->default(0);
            $table->boolean('is_default')->default(0);
            $table->timestamps();

            $table->unique(['organisations_id', 'flows_id', 'channel_types_id']);

            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->foreign('flows_id')->references('id')->on('flows');
            $table->foreign('channel_types_id')->references('id')->on('channel_types');

        });
    }

    private function createChannelsPlacesTable(): void
    {
        Schema::create('channels_places_map', function (Blueprint $table) {
            $table->unsignedBigInteger('channels_id');
            $table->unsignedBigInteger('places_id');
            $table->timestamps();

            $table->primary(['channels_id', 'places_id']);

            $table->foreign('channels_id')->references('id')->on('channels');
            $table->foreign('places_id')->references('id')->on('places');

        });
    }

};
