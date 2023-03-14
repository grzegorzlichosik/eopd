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
        $this->createFlowsTable();
        $this->createFlowsUsersTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flows_users_map');
        Schema::dropIfExists('flows');
    }

    private function createFlowsTable(): void
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->string('name');
            $table->string('objective');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['organisations_id', 'name']);

            $table->foreign('organisations_id')->references('id')->on('organisations');
        });
    }

    private function createFlowsUsersTable(): void
    {
        Schema::create('flows_users_map', function (Blueprint $table) {
            $table->unsignedBigInteger('flows_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('pools_id')->default(null);
            $table->timestamps();

            $table->primary(['flows_id', 'users_id', 'pools_id']);

            $table->foreign('flows_id')->references('id')->on('flows');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('pools_id')->references('id')->on('pools');
        });
    }
};
