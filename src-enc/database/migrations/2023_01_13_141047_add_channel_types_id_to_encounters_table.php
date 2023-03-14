<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Encounter;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_types_id')->after('channels_id')->nullable();
        });

        Encounter::with('channel')
            ->get()
            ->each(function ($encounter) {
                $encounter->channel_types_id = $encounter->channel?->channel_types_id;
                $encounter->save();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn('channel_types_id');
        });
    }
};
