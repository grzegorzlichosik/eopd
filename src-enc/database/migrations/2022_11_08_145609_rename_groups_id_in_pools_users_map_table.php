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
        Schema::table('pools_users_map', function (Blueprint $table) {
            $table->renameColumn('groups_id', 'pools_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pools_users_map', function (Blueprint $table) {
            $table->renameColumn('pools_id', 'groups_id');
        });
    }
};
