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
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['locations_id']);
            $table->dropColumn('locations_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('files_id')->after('organisations_id')->nullable();
            $table->foreign('files_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['files_id']);
            $table->dropColumn('files_id');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('locations_id')->nullable()->after('users_id');
            $table->foreign('locations_id')->references('id')->on('locations');
        });

    }
};
