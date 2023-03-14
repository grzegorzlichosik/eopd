<?php

use App\Models\Flow;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTsmCurrentStateToFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flows', function (Blueprint $table) {
            $table->string('tsm_current_state')->nullable()->after('metadata');
        });

        Flow::get()->each(function($flow) {
            $flow->tsm_current_state = Flow::STATE_PUBLISHED;
            $flow->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::table('flows', function (Blueprint $table) {
                $table->dropColumn('tsm_current_state');
            });
    }
}
