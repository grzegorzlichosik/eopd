<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PoolController;
use App\Http\Controllers\Admin\PoolUserController;
use App\Http\Controllers\Admin\FlowController;
use App\Http\Controllers\Admin\FlowDetailsController;
use App\Http\Controllers\Admin\ScheduleManagementController;
use App\Http\Controllers\Admin\FlowAgentController;
USE App\Http\Controllers\Admin\FlowResourceController;

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'superAdminOrAdmin'])->group(function () {

        Route::prefix('users')
            ->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('admin.users');
                Route::post('/', [UserController::class, 'create'])->name('admin.users.create');
                Route::put('/{uuid}', [UserController::class, 'update'])->name('admin.users.update');
                Route::put('/{uuid}/2fa', [UserController::class, 'reset2fa'])->name('admin.users.reset2fa');
                Route::delete('/{uuid}', [UserController::class, 'destroy'])->name('admin.users.delete');
            });

        Route::prefix('pools')
            ->group(function () {
                Route::get('/', [PoolController::class, 'index'])->name('admin.pools');
                Route::post('/', [PoolController::class, 'store'])->name('admin.pools.create');
                Route::get('/{uuid}', [PoolController::class, 'show'])->name('admin.pools.show');
                Route::put('/{uuid}', [PoolController::class, 'update'])->name('admin.pools.update');

                Route::get('/{uuid}/users', [PoolUserController::class, 'search'])->name('admin.pools.users.search');
                Route::post('/{uuid}/users', [PoolUserController::class, 'store'])->name('admin.pools.users.store');
                Route::delete('/{uuid}/users/{user_uuid}', [PoolUserController::class, 'destroy'])->name('admin.pools.users.delete');
            });

        Route::prefix('resources')
            ->group(function () {

                Route::prefix('locations')
                    ->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('admin.resources.locations.index');
                        Route::post('/', [\App\Http\Controllers\Admin\LocationController::class, 'store'])->name('admin.resources.locations.create');
                        Route::get('/instructions/{fileName}', [\App\Http\Controllers\Admin\LocationController::class, 'downloadInstructions'])->name('admin.resources.locations.instructions');
                        Route::put('/{uuid}', [\App\Http\Controllers\Admin\LocationController::class, 'update'])->name('admin.resources.locations.update');
                        Route::post('/{uuid}/upload', [\App\Http\Controllers\Admin\LocationController::class, 'updateFile'])->name('admin.resources.locations.upload');
                    });

                Route::prefix('places')
                    ->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\PlaceController::class, 'index'])->name('admin.resources.places.index');
                        Route::post('/', [\App\Http\Controllers\Admin\PlaceController::class, 'store'])->name('admin.resources.places.store');
                        Route::put('/{uuid}', [\App\Http\Controllers\Admin\PlaceController::class, 'update'])->name('admin.resources.places.update');
                        Route::delete('/{uuid}', [\App\Http\Controllers\Admin\PlaceController::class, 'destroy'])->name('admin.resources.places.delete');

                    });
            });

        Route::prefix('encounters')
            ->group(function () {
                Route::get('/upcoming', [ScheduleManagementController::class, 'index'])->name('admin.encounters.upcoming');
                Route::get('/completed', [ScheduleManagementController::class, 'completed'])->name('admin.encounters.completed');
                Route::get('/all', [ScheduleManagementController::class, 'allEncounters'])->name('admin.encounters.all');
                Route::get('/cancelled', [ScheduleManagementController::class, 'cancelled'])->name('admin.encounters.cancelled');
                Route::get('/{uuid}', [ScheduleManagementController::class, 'show'])->name('admin.encounters.show');
                Route::post('/{uuid}/calendar', [ScheduleManagementController::class, 'calendar'])->name('admin.encounters.calendar');
            });

        Route::prefix('flows')
            ->group(function () {
                Route::get('/', [FlowController::class, 'index'])->name('admin.flows.index');
                Route::post('/', [FlowController::class, 'create'])->name('admin.flows.create');
                Route::put('/{uuid}', [FlowController::class, 'update'])->name('admin.flows.update');

                Route::get('/{uuid}/agents', [FlowDetailsController::class, 'agents'])->name('admin.flows.agents');
                Route::get('/{uuid}/resources', [FlowDetailsController::class, 'resources'])->name('admin.flows.resources');
                Route::get('/{uuid}/encounters', [FlowDetailsController::class, 'encounters'])->name('admin.flows.encounters');

                Route::delete('/{flowUuid}/deleteAgent/{uuid}', [FlowAgentController::class, 'destroy'])->name('admin.flows.agent.delete');
                Route::put('/{uuid}/addAgentChannel', [FlowAgentController::class, 'update'])->name('admin.flows.agent.addChannel');
                Route::put('/{uuid}/removeAgentChannel', [FlowAgentController::class, 'update'])->name('admin.flows.agent.removeChannel');
                Route::get('/{uuid}/agents/search', [FlowAgentController::class, 'searchAgent'])->name('admin.flows.agent.search');
                Route::post('/{uuid}/agents', [FlowAgentController::class, 'store'])->name('admin.flows.agents.store');

                Route::get('/{uuid}/places', [FlowResourceController::class, 'searchPlace'])->name('admin.flows.place.search');
                Route::post('/{uuid}/places', [FlowResourceController::class, 'store'])->name('admin.flows.place.store');
                Route::delete('/{uuid}/places/{place}/{channel}', [FlowResourceController::class, 'destroy'])->name('admin.flows.place.delete');
            });

    });
