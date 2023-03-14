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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->unsignedBigInteger('organisations_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->integer('failed_logins')->default(0);
            $table->integer('failed_2fa_counter')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_super_admin')->default(0);
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('two_factor_reset_request_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('organisations_id')->references('id')->on('organisations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
