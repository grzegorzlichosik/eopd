<?php

use App\Models\Place;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddTsmCurrentStateToPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('tsm_current_state')->nullable()->after('metadata');
        });

        Place::all()
            ->each(function ($place) {
                $state = $place->is_active
                    ? Place::STATE_ACTIVE
                    : Place::STATE_INACTIVE;

                $place->tsm_current_state = $state;
                $place->save();
            });

        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('places', function (Blueprint $table) {
            $table->boolean('is_active')->default(0)->after('email');
        });

        Place::all()
            ->each(function ($place) {
                if ($place->tsm_current_state === Place::STATE_ACTIVE) {
                    $place->is_active = 1;
                    $place->save();
                }
            });

        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('tsm_current_state');
        });
    }
}
