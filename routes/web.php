<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KejurnasController;
use App\Http\Controllers\MasterController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Sub-Dashboard Kejurnas Umur Dinamis (U-16, U-17, U-18, U-19)
Route::prefix('kejurnas/{kelompok_umur}/{tahun}')->name('kejurnas.')->group(function () {
    Route::get('/', [KejurnasController::class, 'show'])->name('panel');
    Route::get('/tim', [KejurnasController::class, 'teams'])->name('teams');
    Route::get('/pemain', [KejurnasController::class, 'players'])->name('players');
});

// Modul Pengaturan & Master Data PBVSI Pusat
Route::prefix('master')->name('master.')->group(function () {
    Route::get('/wilayah', [MasterController::class, 'wilayah'])->name('wilayah');
    Route::get('/jabatan', [MasterController::class, 'jabatan'])->name('jabatan');
    Route::get('/posisi', [MasterController::class, 'posisi'])->name('posisi');
    Route::get('/pengprov', [MasterController::class, 'pengprov'])->name('pengprov');
    Route::get('/pengkot', [MasterController::class, 'pengkot'])->name('pengkot');
    Route::get('/klub', [MasterController::class, 'klub'])->name('klub');
    Route::get('/users', [MasterController::class, 'users'])->name('users');
});
