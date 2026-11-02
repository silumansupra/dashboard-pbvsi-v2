@extends('layouts.app')

@section('title', $edisi->nama_edisi)

@section('content')
<div class="row mb-3" x-data="{ activeTab: 'overview' }">
    <div class="col-12">
        <!-- BREADCRUMB & HEADER -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">PBVSI Pusat</a></li>
                <li class="breadcrumb-item">Kejurnas Umur</li>
                <li class="breadcrumb-item active">{{ $kelompok_umur }} ({{ $tahun }})</li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-primary mb-2 fw-bold">KEJURNAS {{ $kelompok_umur }} - TAHUN {{ $tahun }}</span>
                        <h2 class="text-white fw-bold mb-1">{{ $edisi->nama_edisi }}</h2>
                        <p class="mb-0 text-white-50">
                            <i class="fa-solid fa-location-dot me-1"></i> {{ $edisi->kota_tuan_rumah ?? 'Indonesia' }} &bull; 
                            <i class="fa-solid fa-building me-1 ms-2"></i> {{ $edisi->venue_utama ?? 'GOR Resmi PBVSI' }} &bull;
                            <i class="fa-solid fa-calendar me-1 ms-2"></i> {{ $edisi->tanggal_mulai ? date('d M Y', strtotime($edisi->tanggal_mulai)) : '-' }} s/d {{ $edisi->tanggal_selesai ? date('d M Y', strtotime($edisi->tanggal_selesai)) : '-' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning text-dark fs-6 p-2">Status: {{ $edisi->status_edisi }}</span>
                    </div>
                </div>

                <div class="row mt-3 pt-3 border-top border-white-50">
                    <div class="col-md-4">
                        <small class="text-white-50">Batas Tanggal Lahir Atlet:</small>
                        <div class="fw-bold">{{ $edisi->min_tanggal_lahir ?? '-' }} s/d {{ $edisi->max_tanggal_lahir ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white-50">Maksimal Roster per Tim:</small>
                        <div class="fw-bold">{{ $edisi->max_kuota_pemain ?? 14 }} Pemain (Maks. 2 Libero)</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white-50">Terintegrasi Master PBVSI:</small>
                        <div class="fw-bold"><i class="fa-solid fa-circle-check text-success"></i> Single Entity & Verified Database</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION (ALPINE.JS) -->
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <button class="nav-link" :class="activeTab === 'overview' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'overview'">
                    <i class="fa-solid fa-gauge-high me-1"></i> Overview & Statistik
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="activeTab === 'teams' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'teams'">
                    <i class="fa-solid fa-shield me-1"></i> Tim Peserta (Putra / Putri)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="activeTab === 'roster' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'roster'">
                    <i class="fa-solid fa-users me-1"></i> Roster Atlet
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="activeTab === 'otwo' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'otwo'">
                    <i class="fa-solid fa-file-excel me-1 text-success"></i> Formulir O-2 (FIVB / VIS)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="activeTab === 'schedule' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'schedule'">
                    <i class="fa-solid fa-calendar-days me-1"></i> Jadwal Pertandingan
                </button>
            </li>
        </ul>

        <!-- TAB CONTENT: OVERVIEW -->
        <div x-show="activeTab === 'overview'" x-transition>
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Petunjuk Regulasi & Verifikasi Umur {{ $kelompok_umur }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info me-2"></i> Seluruh atlet yang didaftarkan ke Kejurnas {{ $kelompok_umur }} otomatis divalidasi tanggal kelahirannya terhadap batas usia resmi <strong>({{ $edisi->min_tanggal_lahir }} s/d {{ $edisi->max_tanggal_lahir }})</strong>.
                            </div>
                            <ol class="mb-0">
                                <li class="mb-2">Admin Tim/Klub mendaftarkan skuad dari <strong>Bank Pemain Master PBVSI</strong>.</li>
                                <li class="mb-2">Verifikator Pengprov memberikan rekomendasi dan verifikasi dokumen legal (Akta & Ijazah).</li>
                                <li class="mb-2">Panitia Pelaksana menerbitkan lembar akreditasi dan <strong>Formulir O-2 Tim</strong> resmi untuk Technical Meeting.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Aksi Cepat Panitia</h5>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <button class="btn btn-outline-primary text-start">
                                <i class="fa-solid fa-user-plus me-2"></i> Daftarkan Tim Peserta Baru
                            </button>
                            <button class="btn btn-outline-success text-start" @click="activeTab = 'otwo'">
                                <i class="fa-solid fa-file-excel me-2"></i> Import / Export Template O-2
                            </button>
                            <button class="btn btn-outline-dark text-start">
                                <i class="fa-solid fa-id-card me-2"></i> Cetak ID Card Massal
                            </button>
                            <button class="btn btn-outline-secondary text-start">
                                <i class="fa-solid fa-award me-2"></i> Cetak Piagam / Sertifikat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: TIM PESERTA -->
        <div x-show="activeTab === 'teams'" x-transition style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Tim Peserta (PA & PI)</h5>
                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Tim</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Tim / Klub</th>
                                    <th>Kategori</th>
                                    <th>Pengprov Asal</th>
                                    <th>Pool</th>
                                    <th>Status Verifikasi</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($edisi->timPeserta as $idx => $t)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td><strong>{{ $t->nama_tim }}</strong></td>
                                    <td><span class="badge {{ $t->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }}">{{ $t->kategori_gender == 'PA' ? 'PUTRA' : 'PUTRI' }}</span></td>
                                    <td>{{ $t->pengprov->nama_pengprov ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $t->pool_grup ?? 'Belum Diundi' }}</span></td>
                                    <td><span class="badge bg-success">{{ $t->status_verifikasi }}</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary">Detail Roster</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-volleyball fa-2x mb-2 d-block"></i>
                                        Belum ada tim peserta yang didaftarkan untuk edisi ini.<br>
                                        <small>Gunakan fitur Import Excel O-2 atau form Tambah Tim untuk menambahkan peserta.</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: FORMULIR O-2 -->
        <div x-show="activeTab === 'otwo'" x-transition style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Modul Formulir O-2 Tim & Pemain (Standar FIVB / VIS)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Import Data Tim O-2 dari Excel</h6>
                            <p class="text-muted small">Mendukung format file spreadsheet VIS / FIVB O-2 (Sheet Team, Player, Official) seperti <code>0. ALL_TIM_{{ $kelompok_umur }}_{{ $tahun }}.xlsx</code>.</p>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pilih File Excel O-2</label>
                                <input type="file" class="form-control" accept=".xlsx, .xls">
                            </div>
                            <button class="btn btn-success btn-sm"><i class="fa-solid fa-upload me-1"></i> Mulai Ekstrak & Validasi Data</button>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="fw-bold"><i class="fa-solid fa-file-export text-success me-2"></i>Export Formulir O-2 Siap Cetak</h6>
                            <p class="text-muted small">Cetak atau unduh formulir resmi O-2 Tim dan O-2 BIS per pertandingan untuk diserahkan ke Technical Delegate.</p>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm text-start">
                                    <i class="fa-solid fa-file-excel text-success me-2"></i> Export Rekap Seluruh Tim (ALL_TIM_{{ $kelompok_umur }}.xlsx)
                                </button>
                                <button class="btn btn-outline-dark btn-sm text-start">
                                    <i class="fa-solid fa-file-pdf text-danger me-2"></i> Cetak O-2 BIS Match Lineup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: ROSTER ATLET -->
        <div x-show="activeTab === 'roster'" x-transition style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Daftar Roster Terdaftar di Kejurnas {{ $kelompok_umur }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Seluruh pemain yang terdaftar terhubung langsung dengan ID Nasional PBVSI.</p>
                    <div class="alert alert-secondary">
                        Silakan pilih tim di tab <strong>Tim Peserta</strong> untuk melihat dan memverifikasi roster spesifik per tim.
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: JADWAL PERTANDINGAN -->
        <div x-show="activeTab === 'schedule'" x-transition style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Jadwal & Hasil Pertandingan</h5>
                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Match</button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Belum ada pertandingan yang dijadwalkan.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
