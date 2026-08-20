<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalHistoryController;
use App\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| Web Routes - Abdillans Gaming PS Rental
|--------------------------------------------------------------------------
*/

// Category 1: Sheets / Mendata Pelanggan (Dashboard Grid View)
Route::get('/', [RentalDashboardController::class, 'index'])->name('dashboard');
Route::get('/sheets', [RentalDashboardController::class, 'index'])->name('sheets');

// Category 2: Data Pelanggan (Customer Directory & Photo Upload / Webcam)
Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
    Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
});

// Category 3: Riwayat Pelanggan (Periodic History Reports - Daily, Weekly, Monthly, Yearly)
Route::get('/history', [RentalHistoryController::class, 'index'])->name('history');

// Rental Actions (Start, Complete, Cancel)
Route::prefix('rentals')->name('rentals.')->group(function () {
    Route::post('/start', [RentalController::class, 'start'])->name('start');
    Route::post('/complete/{id}', [RentalController::class, 'complete'])->name('complete');
    Route::post('/cancel/{id}', [RentalController::class, 'cancel'])->name('cancel');
});

// Units Management (Add Unit, Update Status/Price)
Route::prefix('units')->name('units.')->group(function () {
    Route::post('/store', [UnitController::class, 'store'])->name('store');
    Route::put('/{id}', [UnitController::class, 'update'])->name('update');
    Route::delete('/{id}', [UnitController::class, 'destroy'])->name('destroy');
});
