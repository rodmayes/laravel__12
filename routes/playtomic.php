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
        Route::get('toggle-booked/{booking}', [BookingController::class, 'toggleBooked'])->name('toggle-booked');
    });

    Route::get('timetable/get-list',[TimetableController::class, 'getList'])->name('timetable.get-list');
    Route::get('resources/get-list/{club?}',[ResourceController::class, 'getList'])->name('resources.get-list');

// CLUBS
Route::prefix('club')->name('club.')->group(function () {
    Route::get('', [ClubController::class, 'index'])->name('index');
    Route::post('getData', [ClubController::class, 'getData'])->name('getData');
    Route::post('refreshData', [ClubController::class, 'refreshData'])->name('refreshData');
    Route::put('{club}', [ClubController::class, 'update'])->name('update');
    Route::post('', [ClubController::class, 'store'])->name('store');
    Route::get('availability/{club}',[ClubController::class, 'availability'])->name('availability');
    Route::get('get-list',[ClubController::class, 'getList'])->name('get-list');
    Route::get('sync-resources/{club}',[ClubController::class, 'syncResources'])->name('sync-resources');
    Route::delete('{club}',[ClubController::class, 'destroy'])->name('destroy');
});

// TIMETABLES
Route::prefix('timetables')->name('timetables.')->group(function () {
    Route::get('', [TimetableController::class, 'index'])->name('index');
    Route::post('getData', [TimetableController::class, 'getData'])->name('getData');
    Route::post('refreshData', [TimetableController::class, 'refreshData'])->name('refreshData');
    Route::put('{timetable}', [TimetableController::class, 'update'])->name('update');
    Route::post('', [TimetableController::class, 'store'])->name('store');
    Route::get('get-list',[TimetableController::class, 'getList'])->name('get-list');
    Route::delete('{timetable}',[TimetableController::class, 'destroy'])->name('timetable');
});

// RESOURCES
Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('', [ResourceController::class, 'index'])->name('index');
    Route::post('filter', [ResourceController::class, 'filter'])->name('filter');
    Route::post('refreshData', [ResourceController::class, 'refreshData'])->name('refreshData');
    Route::put('{resource}', [ResourceController::class, 'update'])->name('update');
    Route::post('', [ResourceController::class, 'store'])->name('store');
    Route::get('get-list',[ResourceController::class, 'getList'])->name('get-list');
    Route::delete('{resource}',[ResourceController::class, 'destroy'])->name('destroy');
});

// BOOKINGS
Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('', [BookingController::class, 'index'])->name('index');
    Route::post('getData', [BookingController::class, 'getData'])->name('getData');
    Route::get('create', [BookingController::class, 'create'])->name('create');
    Route::get('edit/{booking}', [BookingController::class, 'edit'])->name('edit');
    Route::post('refreshData', [BookingController::class, 'refreshData'])->name('refreshData');
    Route::put('{booking}', [BookingController::class, 'update'])->name('update');
    Route::post('', [BookingController::class, 'store'])->name('store');
    Route::get('get-list',[BookingController::class, 'getList'])->name('get-list');
    Route::delete('{booking}',[BookingController::class, 'destroy'])->name('destroy');
    Route::get('{booking}',[BookingController::class, 'toggleBooked'])->name('toggle-booked');
});

// USERS
Route::prefix('user')->name('user.')->group(function () {
    Route::put('update/{user}', [PlaytomicController::class, 'updateUser'])->name('update');
    Route::put('save-password/{user}', [PlaytomicController::class, 'storePassword'])->name('save-password');
    Route::get('refresh-token/{user}', [PlaytomicController::class, 'refreshToken'])->name('refresh-token');
});
