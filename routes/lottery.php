<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Lottery\LotteryController;

Route::prefix('combinations')->name('combinations.')->group(function () {
    Route::get('', [LotteryController::class, 'combinationsIndex'])->name('index');
    Route::post('make-magik-numbers', [LotteryController::class, 'makeMagikNumbers'])->name('make-magik-numbers');
    Route::post('send-mail-with-combinations', [LotteryController::class, 'sendMailWithCombinations'])->name('send-mail-with-combinations');
});

Route::prefix('results')->name('results.')->group(function () {
    Route::get('', [LotteryController::class, 'resultsIndex'])->name('index');
    Route::get('getData', [LotteryController::class, 'getDataResults'])->name('getData');
    Route::post('refreshData', [LotteryController::class, 'resultsRefresh'])->name('refreshData');
    Route::put('{result}', [LotteryController::class, 'update'])->name('update');
    Route::post('', [LotteryController::class, 'store'])->name('store');
    Route::post('import', [LotteryController::class, 'importResults'])->name('import');
});
