<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KejurnasController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Sub-Dashboard Kejurnas Umur Dinamis (U-16, U-17, U-18, U-19)
Route::prefix('kejurnas/{kelompok_umur}/{tahun}')->name('kejurnas.')->group(function () {
    Route::get('/', [KejurnasController::class, 'show'])->name('panel');
    Route::get('/tim', [KejurnasController::class, 'teams'])->name('teams');
    Route::get('/pemain', [KejurnasController::class, 'players'])->name('players');
});
