<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('organisations', 'address')){

            Schema::table('organisations', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
    }
};
