<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KejurnasController;
use App\Http\Controllers\KejuaraanController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PersonelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamPortalController;
use App\Http\Controllers\AuditLogController;

// Auth Routes (Login, Logout, & Lupa/Reset Password)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/lupa-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Verifikasi & Aktivasi Akun (Registrasi Anggota / Wasit)
Route::get('/verifikasi-akun', [AuthController::class, 'verifikasiAkun'])->name('verifikasi.akun');

// Dashboard Utama Terproteksi Auth
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index')->middleware(['auth']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth']);

// Modul Portal Tim & Kontingen (Admin Tim)
Route::prefix('tim')->name('tim.')->middleware(['auth'])->group(function () {
    Route::get('/switch/{id}', [TeamPortalController::class, 'switchTeam'])->name('switch');
    Route::get('/profil', [TeamPortalController::class, 'profil'])->name('profil');
    Route::post('/profil', [TeamPortalController::class, 'updateProfil'])->name('profil.update');
    Route::get('/pemain', [TeamPortalController::class, 'pemain'])->name('pemain');
    Route::post('/pemain', [TeamPortalController::class, 'storePemain'])->name('pemain.store');
    Route::delete('/pemain/{id}', [TeamPortalController::class, 'destroyPemain'])->name('pemain.destroy');
    Route::get('/official', [TeamPortalController::class, 'official'])->name('official');
    Route::post('/official', [TeamPortalController::class, 'storeOfficial'])->name('official.store');
    Route::delete('/official/{id}', [TeamPortalController::class, 'destroyOfficial'])->name('official.destroy');
    Route::get('/roster', [TeamPortalController::class, 'roster'])->name('roster');
    Route::post('/roster', [TeamPortalController::class, 'updateRoster'])->name('roster.update');
    Route::get('/roster/print-o2', [TeamPortalController::class, 'printO2'])->name('roster.print_o2');
    Route::get('/akun', [TeamPortalController::class, 'akun'])->name('akun');
    Route::put('/akun', [TeamPortalController::class, 'updateAkun'])->name('akun.update');
});

// Halaman Publik Legalitas PBVSI (Kebijakan Privasi & Syarat Ketentuan)
Route::get('/kebijakan-privasi', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/syarat-ketentuan', function () {
    return view('legal.terms');
})->name('legal.terms');

// Modul Universal Kompetisi & Kejuaraan Nasional PBVSI
Route::prefix('kejuaraan')->name('kejuaraan.')->middleware(['auth'])->group(function () {
    Route::get('/{slug}', [KejuaraanController::class, 'show'])->name('index');
    Route::get('/{slug}/{tahun}', [KejuaraanController::class, 'show'])->name('tahun');
    Route::post('/{slug}/edisi', [KejuaraanController::class, 'storeEdisi'])->name('edisi.store');
    Route::get('/{slug}/{tahun}/tim', [KejuaraanController::class, 'teams'])->name('teams');
    Route::get('/{slug}/{tahun}/pemain', [KejuaraanController::class, 'players'])->name('players');
});

// Backward compatibility alias redirects
Route::middleware(['auth'])->group(function () {
    Route::get('/kejurnas/{kelompok_umur}/{tahun}', function ($kelompok_umur, $tahun) {
        $slug = 'kejurnas-' . strtolower($kelompok_umur);
        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]);
    });
    Route::get('/kejurnas/{kelompok_umur}/{tahun}/tim', function ($kelompok_umur, $tahun) {
        $slug = 'kejurnas-' . strtolower($kelompok_umur);
        return redirect()->route('kejuaraan.teams', ['slug' => $slug, 'tahun' => $tahun]);
    });
    Route::get('/kejurnas/{kelompok_umur}/{tahun}/pemain', function ($kelompok_umur, $tahun) {
        $slug = 'kejurnas-' . strtolower($kelompok_umur);
        return redirect()->route('kejuaraan.players', ['slug' => $slug, 'tahun' => $tahun]);
    });
});

// Modul Profil Akun Saya (Semua Pengguna Terautentikasi)
Route::middleware(['auth'])->group(function () {
    Route::get('/profil-saya', [AuthController::class, 'profilSaya'])->name('akun.saya');
    Route::put('/profil-saya', [AuthController::class, 'updateProfilSaya'])->name('akun.saya.update');
});

// Modul Pengaturan & Master Data PBVSI Pusat (Khusus Superadmin)
Route::prefix('master')->name('master.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/wilayah', [MasterController::class, 'wilayah'])->name('wilayah');
    Route::post('/wilayah/provinsi', [MasterController::class, 'storeProvinsi'])->name('wilayah.provinsi.store');
    Route::put('/wilayah/provinsi/{id}', [MasterController::class, 'updateProvinsi'])->name('wilayah.provinsi.update');
    Route::delete('/wilayah/provinsi/{id}', [MasterController::class, 'destroyProvinsi'])->name('wilayah.provinsi.destroy');
    
    Route::post('/wilayah/kabupaten', [MasterController::class, 'storeKabupaten'])->name('wilayah.kabupaten.store');
    Route::put('/wilayah/kabupaten/{id}', [MasterController::class, 'updateKabupaten'])->name('wilayah.kabupaten.update');
    Route::delete('/wilayah/kabupaten/{id}', [MasterController::class, 'destroyKabupaten'])->name('wilayah.kabupaten.destroy');

    // 2. Master Pengprov
    Route::get('/pengprov', [MasterController::class, 'pengprov'])->name('pengprov');
    Route::get('/pengprov/create', [MasterController::class, 'createPengprov'])->name('pengprov.create');
    Route::post('/pengprov', [MasterController::class, 'storePengprov'])->name('pengprov.store');
    Route::get('/pengprov/{id}/edit', [MasterController::class, 'editPengprov'])->name('pengprov.edit');
    Route::put('/pengprov/{id}', [MasterController::class, 'updatePengprov'])->name('pengprov.update');
    Route::delete('/pengprov/{id}', [MasterController::class, 'destroyPengprov'])->name('pengprov.destroy');

    // 3. Master Pengkot / Pengkab
    Route::get('/pengkot', [MasterController::class, 'pengkot'])->name('pengkot');
    Route::get('/pengkot/create', [MasterController::class, 'createPengkot'])->name('pengkot.create');
    Route::post('/pengkot', [MasterController::class, 'storePengkot'])->name('pengkot.store');
    Route::get('/pengkot/{id}/edit', [MasterController::class, 'editPengkot'])->name('pengkot.edit');
    Route::put('/pengkot/{id}', [MasterController::class, 'updatePengkot'])->name('pengkot.update');
    Route::delete('/pengkot/{id}', [MasterController::class, 'destroyPengkot'])->name('pengkot.destroy');
    Route::get('/api/kabupaten-by-pengprov/{pengprovId}', [MasterController::class, 'apiKabupatenByPengprov'])->name('api.kabupaten_by_pengprov');
    Route::get('/api/pengkot-by-pengprov/{pengprovId}', [MasterController::class, 'apiPengkotByPengprov'])->name('api.pengkot_by_pengprov');

    // 4. Master Klub / Tim
    Route::get('/klub', [MasterController::class, 'klub'])->name('klub');
    Route::get('/klub/create', [MasterController::class, 'createKlub'])->name('klub.create');
    Route::get('/klub/{id}/edit', [MasterController::class, 'editKlub'])->name('klub.edit');
    Route::get('/klub/{tipe}/{id}', [MasterController::class, 'detailTim'])->whereIn('tipe', ['klub', 'pengprov'])->name('klub.detail');
    Route::get('/klub/{id}', function ($id) {
        $token = is_numeric($id) ? \App\Services\SecureId::encrypt($id, 'klub') : $id;
        return redirect()->route('master.klub.detail', ['tipe' => 'klub', 'id' => $token]);
    });
    Route::post('/klub', [MasterController::class, 'storeKlub'])->name('klub.store');
    Route::put('/klub/{id}', [MasterController::class, 'updateKlub'])->name('klub.update');
    Route::delete('/klub/{id}', [MasterController::class, 'destroyKlub'])->name('klub.destroy');

    // 5. Master Jabatan
    Route::get('/jabatan', [MasterController::class, 'jabatan'])->name('jabatan');
    Route::get('/jabatan/create', [MasterController::class, 'createJabatan'])->name('jabatan.create');
    Route::post('/jabatan', [MasterController::class, 'storeJabatan'])->name('jabatan.store');
    Route::get('/jabatan/{id}/edit', [MasterController::class, 'editJabatan'])->name('jabatan.edit');
    Route::put('/jabatan/{id}', [MasterController::class, 'updateJabatan'])->name('jabatan.update');
    Route::delete('/jabatan/{id}', [MasterController::class, 'destroyJabatan'])->name('jabatan.destroy');

    // 6. Master Posisi Pemain
    Route::get('/posisi', [MasterController::class, 'posisi'])->name('posisi');
    Route::get('/posisi/create', [MasterController::class, 'createPosisi'])->name('posisi.create');
    Route::post('/posisi', [MasterController::class, 'storePosisi'])->name('posisi.store');
    Route::get('/posisi/{id}/edit', [MasterController::class, 'editPosisi'])->name('posisi.edit');
    Route::put('/posisi/{id}', [MasterController::class, 'updatePosisi'])->name('posisi.update');
    Route::delete('/posisi/{id}', [MasterController::class, 'destroyPosisi'])->name('posisi.destroy');

    // 7. Master User & RBAC
    Route::get('/admin-tim', [MasterController::class, 'adminTim'])->name('admin_tim');
    Route::get('/users', [MasterController::class, 'users'])->name('users');
    Route::get('/users/create', [MasterController::class, 'createUser'])->name('users.create');
    Route::post('/users', [MasterController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [MasterController::class, 'editUser'])->name('users.edit');
    Route::get('/users/{id}/raw-password', [MasterController::class, 'getRawPassword'])->name('users.raw_password');
    Route::put('/users/{id}', [MasterController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [MasterController::class, 'destroyUser'])->name('users.destroy');
});

// Modul Database Personel & Anggota PBVSI Pusat (Khusus Superadmin)
Route::prefix('personel')->name('personel.')->middleware(['auth', 'role:superadmin'])->group(function () {
    // 1. Bank Pemain
    Route::get('/pemain', [PersonelController::class, 'pemain'])->name('pemain');
    Route::get('/pemain/create', [PersonelController::class, 'createPemain'])->name('pemain.create');
    Route::post('/pemain', [PersonelController::class, 'storePemain'])->name('pemain.store');
    Route::get('/pemain/{id}/edit', [PersonelController::class, 'editPemain'])->name('pemain.edit');
    Route::put('/pemain/{id}', [PersonelController::class, 'updatePemain'])->name('pemain.update');
    Route::delete('/pemain/{id}', [PersonelController::class, 'destroyPemain'])->name('pemain.destroy');
    Route::post('/pemain/{id}/verifikasi', [PersonelController::class, 'verifikasiPemain'])->name('pemain.verifikasi');

    // 2. Data Pelatih
    Route::get('/pelatih', [PersonelController::class, 'pelatih'])->name('pelatih');
    Route::get('/pelatih/create', [PersonelController::class, 'createPelatih'])->name('pelatih.create');
    Route::post('/pelatih', [PersonelController::class, 'storePelatih'])->name('pelatih.store');
    Route::get('/pelatih/{id}/edit', [PersonelController::class, 'editPelatih'])->name('pelatih.edit');
    Route::put('/pelatih/{id}', [PersonelController::class, 'updatePelatih'])->name('pelatih.update');
    Route::delete('/pelatih/{id}', [PersonelController::class, 'destroyPelatih'])->name('pelatih.destroy');

    // 3. Data Wasit
    Route::get('/wasit', [PersonelController::class, 'wasit'])->name('wasit');
    Route::get('/wasit/create', [PersonelController::class, 'createWasit'])->name('wasit.create');
    Route::post('/wasit', [PersonelController::class, 'storeWasit'])->name('wasit.store');
    Route::get('/wasit/{id}/edit', [PersonelController::class, 'editWasit'])->name('wasit.edit');
    Route::put('/wasit/{id}', [PersonelController::class, 'updateWasit'])->name('wasit.update');
    Route::delete('/wasit/{id}', [PersonelController::class, 'destroyWasit'])->name('wasit.destroy');

    // 4. Data Official
    Route::get('/official', [PersonelController::class, 'official'])->name('official');
    Route::get('/official/create', [PersonelController::class, 'createOfficial'])->name('official.create');
    Route::post('/official', [PersonelController::class, 'storeOfficial'])->name('official.store');
    Route::get('/official/{id}/edit', [PersonelController::class, 'editOfficial'])->name('official.edit');
    Route::put('/official/{id}', [PersonelController::class, 'updateOfficial'])->name('official.update');
    Route::delete('/official/{id}', [PersonelController::class, 'destroyOfficial'])->name('official.destroy');
});

// Modul Audit Log & Keamanan Platform (Khusus Superadmin)
Route::prefix('audit-logs')->name('audit-logs.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/', [AuditLogController::class, 'index'])->name('index');
    Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
});

// Shortcut alias redirects
Route::redirect('/pemain', '/personel/pemain');
Route::redirect('/pelatih', '/personel/pelatih');
Route::redirect('/wasit', '/personel/wasit');
Route::redirect('/official', '/personel/official');
