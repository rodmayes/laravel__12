<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Playtomic\ClubController;
use App\Http\Controllers\Playtomic\ResourceController;
use App\Http\Controllers\Playtomic\BookingController;
use App\Http\Controllers\Playtomic\PlaytomicController;
use App\Http\Controllers\Playtomic\TimetableController;

    Route::get('login',[PlaytomicController::class, 'login'])->name('login');
    Route::resource('resources', ResourceController::class, ['except' => ['store', 'update', 'destroy']]);
    Route::resource('timetables', TimetableController::class, ['except' => ['create', 'show', 'edit']]);
    //Route::resource('bookings', BookingController::class, ['except' => ['store', 'update', 'destroy']]);
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('', [BookingController::class, 'index'])->name('index');
        Route::get('create/{start_date?}', [BookingController::class, 'create'])->name('create');
        Route::get('{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::get('view-calendar', [BookingController::class, 'viewCalendar'])->name('view-calendar');
        Route::get('make-booking', [BookingController::class, 'makeBooking'])->name('make-booking');
        Route::get('booking', [BookingController::class, 'prebooking'])->name('booking');
        Route::get('generate-links/{booking}', [BookingController::class, 'generatelinks'])->name('generate-links');
        Route::get('{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('pre-booking', [BookingController::class, 'prebooking'])->name('pre-booking');
    });

    Route::get('timetable/get-list',[TimetableController::class, 'getList'])->name('timetable.get-list');
    Route::get('resources/get-list/{club?}',[ResourceController::class, 'getList'])->name('resources.get-list');

// CLUBS
Route::prefix('club')->name('club.')->group(function () {
    Route::get('', [ClubController::class, 'index'])->name('index');
    Route::get('getData', [ClubController::class, 'getDataResults'])->name('getData');
    Route::post('refreshData', [ClubController::class, 'resultsRefresh'])->name('refreshData');
    Route::put('{result}', [ClubController::class, 'update'])->name('update');
    Route::post('', [ClubController::class, 'store'])->name('store');
    Route::get('availability/{club}',[ClubController::class, 'availability'])->name('availability');
    Route::get('get-list',[ClubController::class, 'getList'])->name('get-list');
});
