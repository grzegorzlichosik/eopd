<?php

use App\Models\Organisation;
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
        if (!Schema::hasColumn('organisations', 'created_by')) {
            Schema::table('organisations', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->after('phone_number')->nullable();
                $table->foreign('created_by')->references('id')->on('users');
            });
        }
        if (!Schema::hasColumn('organisations', 'is_platform')) {
            Schema::table('organisations', function (Blueprint $table) {
                $table->boolean('is_platform')->after('phone_number')->nullable()->unique();
            });
        }

        $orgs = Organisation::whereNull('created_by')
            ->with('users')
            ->get();

        foreach ($orgs as $org) {
            $org->created_by = $org->users->first()?->id;
            $org->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropForeign('organisations_created_by_foreign');
            $table->dropColumn(['is_platform', 'created_by']);
        });
    }
};
