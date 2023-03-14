<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PoolController;
use App\Http\Controllers\Admin\PoolUserController;

Route::prefix('agent')
    ->middleware(['auth', 'verified'])->group(function () {
        Route::prefix('encounters')
            ->group(function () {
                Route::get('/all', [\App\Http\Controllers\Agent\EncounterController::class, 'allEncounters'])->name('agent.encounters.all');
                Route::get('/calendar', [\App\Http\Controllers\Agent\EncounterController::class, 'calendar'])->name('agent.encounters.calendar');
                Route::get('/{uuid}', [\App\Http\Controllers\Agent\EncounterController::class, 'show'])->name('agent.encounters.show');
                Route::post('/calendar/data', [\App\Http\Controllers\Agent\EncounterController::class, 'getCalendarEncounter'])->name('agent.encounters.showEvents');

            });
    });
