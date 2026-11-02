<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KejurnasController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PersonelController;

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
    Route::post('/wilayah/provinsi', [MasterController::class, 'storeProvinsi'])->name('wilayah.provinsi.store');
    Route::put('/wilayah/provinsi/{id}', [MasterController::class, 'updateProvinsi'])->name('wilayah.provinsi.update');
    Route::delete('/wilayah/provinsi/{id}', [MasterController::class, 'destroyProvinsi'])->name('wilayah.provinsi.destroy');
    
    Route::post('/wilayah/kabupaten', [MasterController::class, 'storeKabupaten'])->name('wilayah.kabupaten.store');
    Route::put('/wilayah/kabupaten/{id}', [MasterController::class, 'updateKabupaten'])->name('wilayah.kabupaten.update');
    Route::delete('/wilayah/kabupaten/{id}', [MasterController::class, 'destroyKabupaten'])->name('wilayah.kabupaten.destroy');

    // 2. Master Pengprov
    Route::get('/pengprov', [MasterController::class, 'pengprov'])->name('pengprov');
    Route::post('/pengprov', [MasterController::class, 'storePengprov'])->name('pengprov.store');
    Route::put('/pengprov/{id}', [MasterController::class, 'updatePengprov'])->name('pengprov.update');
    Route::delete('/pengprov/{id}', [MasterController::class, 'destroyPengprov'])->name('pengprov.destroy');

    // 3. Master Pengkot / Pengkab
    Route::get('/pengkot', [MasterController::class, 'pengkot'])->name('pengkot');
    Route::post('/pengkot', [MasterController::class, 'storePengkot'])->name('pengkot.store');
    Route::put('/pengkot/{id}', [MasterController::class, 'updatePengkot'])->name('pengkot.update');
    Route::delete('/pengkot/{id}', [MasterController::class, 'destroyPengkot'])->name('pengkot.destroy');
    Route::get('/api/kabupaten-by-pengprov/{pengprovId}', [MasterController::class, 'apiKabupatenByPengprov'])->name('api.kabupaten_by_pengprov');

    // 4. Master Klub / Tim
    Route::get('/klub', [MasterController::class, 'klub'])->name('klub');
    Route::post('/klub', [MasterController::class, 'storeKlub'])->name('klub.store');
    Route::put('/klub/{id}', [MasterController::class, 'updateKlub'])->name('klub.update');
    Route::delete('/klub/{id}', [MasterController::class, 'destroyKlub'])->name('klub.destroy');

    // 5. Master Jabatan
    Route::get('/jabatan', [MasterController::class, 'jabatan'])->name('jabatan');
    Route::post('/jabatan', [MasterController::class, 'storeJabatan'])->name('jabatan.store');
    Route::put('/jabatan/{id}', [MasterController::class, 'updateJabatan'])->name('jabatan.update');
    Route::delete('/jabatan/{id}', [MasterController::class, 'destroyJabatan'])->name('jabatan.destroy');

    // 6. Master Posisi Pemain
    Route::get('/posisi', [MasterController::class, 'posisi'])->name('posisi');
    Route::post('/posisi', [MasterController::class, 'storePosisi'])->name('posisi.store');
    Route::put('/posisi/{id}', [MasterController::class, 'updatePosisi'])->name('posisi.update');
    Route::delete('/posisi/{id}', [MasterController::class, 'destroyPosisi'])->name('posisi.destroy');

    // 7. Master User & RBAC
    Route::get('/users', [MasterController::class, 'users'])->name('users');
    Route::post('/users', [MasterController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [MasterController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [MasterController::class, 'destroyUser'])->name('users.destroy');
});

// Modul Database Personel & Anggota PBVSI Pusat
Route::prefix('personel')->name('personel.')->group(function () {
    // 1. Bank Pemain
    Route::get('/pemain', [PersonelController::class, 'pemain'])->name('pemain');
    Route::post('/pemain', [PersonelController::class, 'storePemain'])->name('pemain.store');
    Route::put('/pemain/{id}', [PersonelController::class, 'updatePemain'])->name('pemain.update');
    Route::delete('/pemain/{id}', [PersonelController::class, 'destroyPemain'])->name('pemain.destroy');
    Route::post('/pemain/{id}/verifikasi', [PersonelController::class, 'verifikasiPemain'])->name('pemain.verifikasi');

    // 2. Data Pelatih
    Route::get('/pelatih', [PersonelController::class, 'pelatih'])->name('pelatih');
    Route::post('/pelatih', [PersonelController::class, 'storePelatih'])->name('pelatih.store');
    Route::put('/pelatih/{id}', [PersonelController::class, 'updatePelatih'])->name('pelatih.update');
    Route::delete('/pelatih/{id}', [PersonelController::class, 'destroyPelatih'])->name('pelatih.destroy');

    // 3. Data Wasit
    Route::get('/wasit', [PersonelController::class, 'wasit'])->name('wasit');
    Route::post('/wasit', [PersonelController::class, 'storeWasit'])->name('wasit.store');
    Route::put('/wasit/{id}', [PersonelController::class, 'updateWasit'])->name('wasit.update');
    Route::delete('/wasit/{id}', [PersonelController::class, 'destroyWasit'])->name('wasit.destroy');

    // 4. Data Official
    Route::get('/official', [PersonelController::class, 'official'])->name('official');
    Route::post('/official', [PersonelController::class, 'storeOfficial'])->name('official.store');
    Route::put('/official/{id}', [PersonelController::class, 'updateOfficial'])->name('official.update');
    Route::delete('/official/{id}', [PersonelController::class, 'destroyOfficial'])->name('official.destroy');
});

// Shortcut alias redirects
Route::redirect('/pemain', '/personel/pemain');
Route::redirect('/pelatih', '/personel/pelatih');
Route::redirect('/wasit', '/personel/wasit');
Route::redirect('/official', '/personel/official');
