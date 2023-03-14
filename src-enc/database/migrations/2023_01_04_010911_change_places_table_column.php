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
        Schema::table('places', function (Blueprint $table) {
            $table->string('email')->unsigned()->nullable()->change();
            $table->json('metadata')->unsigned()->nullable()->change();
            $table->string('external_id')->unsigned()->nullable()->change();
        });
    }
};
