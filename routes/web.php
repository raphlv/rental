<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalDashboardController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [RentalDashboardController::class, 'index'])->name('dashboard');
Route::get('/history', [RentalDashboardController::class, 'history'])->name('history');

Route::prefix('units')->name('units.')->group(function () {
    Route::post('/store', [UnitController::class, 'store'])->name('store');
    Route::post('/update/{id}', [UnitController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('destroy');
    Route::post('/reset/{type}', [UnitController::class, 'reset'])->name('reset');
});

Route::prefix('rentals')->name('rentals.')->group(function () {
    Route::post('/start', [RentalController::class, 'start'])->name('start');
    Route::post('/complete/{id}', [RentalController::class, 'complete'])->name('complete');
});
