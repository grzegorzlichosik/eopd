<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Platform\OrganisationController;
use App\Http\Controllers\Platform\NylasLinkController;

Route::prefix('platform')
    ->middleware(['auth', 'verified', 'platform'])->group(function () {

        Route::prefix('organisations')
            ->group(function () {
                Route::get('/', [OrganisationController::class, 'index'])->name('platform.organisations.index');
                Route::put('/{uuid}/master_calendar', [OrganisationController::class, 'createMasterCalendar'])->name('platform.organisations.master_calendar.create');
                Route::delete('/{uuid}/master_calendar', [OrganisationController::class, 'deleteMasterCalendar'])->name('platform.organisations.master_calendar.delete');
                Route::get('/{uuid}/master_calendar', [OrganisationController::class, 'showMasterCalendarPassword'])->name('platform.organisations.master_calendar.show');
                Route::get('/{uuid}/master_nylas', [NylasLinkController::class, 'masterHosted'])->name('platform.organisations.master');
                Route::get('/master_retry', [NylasLinkController::class, 'retry'])->name('platform.organisations.retry');
            });


    });
