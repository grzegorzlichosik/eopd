<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Encounters\BookingController;
use App\Http\Controllers\Encounters\CancelBookingController;
use App\Http\Controllers\Encounters\RescheduleBookingController;
use App\Http\Controllers\Encounters\AvailabilityController;


Route::prefix('bookings')
    ->group(function () {
        Route::post('/', [BookingController::class, 'postShow'])
            ->name('encounters.booking.post');
        Route::get('/{uuid}', [BookingController::class, 'getShow'])
            ->name('encounters.booking.get');


        Route::post('/{uuid}/booking', [BookingController::class, 'store'])
            ->name('encounters.booking.post.store');

        Route::post('/{uuid}/availability', [AvailabilityController::class, 'index'])
            ->name('encounters.availability.index');

        Route::get('/manage/{uuid}/cancel', [CancelBookingController::class, 'cancel'])
            ->name('encounters.booking.post.cancel');
        Route::delete('/manage/{uuid}', [CancelBookingController::class, 'destroy'])
            ->name('encounters.booking.destroy');

        Route::get('/manage/{uuid}/reschedule', [RescheduleBookingController::class, 'index'])
            ->name('encounters.booking.post.reschedule');
        Route::put('/manage/{uuid}/reschedule', [RescheduleBookingController::class, 'update'])
            ->name('encounters.booking.update');

        Route::get('/manage/{uuid}/add_participants', [RescheduleBookingController::class, 'create'])
            ->name('encounters.booking.add_participants');

        Route::put('/manage/{uuid}/add_participants', [RescheduleBookingController::class, 'store'])
            ->name('encounters.booking.store_participants');

    });
