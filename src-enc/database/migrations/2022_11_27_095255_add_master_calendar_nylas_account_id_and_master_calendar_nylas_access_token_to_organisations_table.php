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
        Schema::table('organisations', function (Blueprint $table) {
            $table->text('master_calendar_nylas_account_id')->after('master_calendar_password')->nullable();
            $table->string('master_calendar_nylas_access_token')->after('master_calendar_nylas_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['master_calendar_nylas_account_id', 'master_calendar_nylas_access_token']);
        });
    }
};
