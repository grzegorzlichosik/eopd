<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\OrganisationController;


Route::prefix('organisation')->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/logo', [OrganisationController::class, 'showImage'])
            ->name('organisation.logo');
        Route::get('/logo/{size}', [OrganisationController::class, 'showImage'])
            ->name('organisation.logo.{size}');
    });

Route::prefix('organisation')->middleware(['auth', 'verified', 'superAdmin'])
    ->group(function () {
        Route::get('/', [OrganisationController::class, 'show'])
            ->name('organisation.show');
        Route::post('/update', [OrganisationController::class, 'update'])
            ->name('organisation.update');
        Route::delete('/logo/delete', [OrganisationController::class, 'destroy'])
            ->name('organisation.logo.delete');
    });
