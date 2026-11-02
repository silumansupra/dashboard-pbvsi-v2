@extends('layouts.app')

@section('title', ($edisi ? $edisi->nama_edisi : $turnamen->nama_turnamen . ' - Tahun ' . $tahun))

@section('content')
<!-- NOTIFIKASI -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation fa-lg me-2 text-danger"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-warning"></i>
        <strong>Terdapat kesalahan input:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mb-3" x-data="{ activeTab: 'overview' }">
    <div class="col-12">
        <!-- BREADCRUMB & HEADER -->
        <div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}">
                            <i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat
                        </a>
                    </li>
                    <li class="breadcrumb-item text-secondary">
                        @if($turnamen->kategori == 'KEJURNAS_UMUR')
                            Kejuaraan Kelompok Umur
                        @elseif($turnamen->kategori == 'LIVOLI')
                            Liga Voli Indonesia (Livoli)
                        @elseif($turnamen->kategori == 'KAPOLRI_CUP')
                            Piala Kapolri
                        @elseif($turnamen->kategori == 'VOLI_PANTAI')
                            Bola Voli Pantai
                        @else
                            Kompetisi Nasional
                        @endif
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="badge bg-primary px-2 py-1">
                            {{ $turnamen->nama_turnamen }} ({{ $tahun }})
                        </span>
                    </li>
                </ol>
            </nav>

            <!-- YEAR SWITCHER & ACTION BUTTON -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- DROPDOWN TAHUN -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-white bg-white border shadow-sm dropdown-toggle fw-bold text-dark px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-calendar-days text-primary me-1"></i> Edisi Tahun: <span class="text-primary">{{ $tahun }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="dropdown-header small text-uppercase text-muted">Pilih Edisi Tahun</li>
                        @forelse($edisiList as $item)
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $item->tahun == $tahun ? 'active fw-bold' : '' }}" 
                               href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $item->tahun]) }}">
                                <span>Tahun {{ $item->tahun }}</span>
                                <span class="badge {{ $item->status_edisi == 'REGISTRASI' ? 'bg-success' : ($item->status_edisi == 'FINISHED' ? 'bg-secondary' : 'bg-warning') }} ms-2" style="font-size: 0.65rem;">
                                    {{ $item->status_edisi }}
                                </span>
                            </a>
                        </li>
                        @empty
                        <li><span class="dropdown-item text-muted small">Belum ada edisi</span></li>
                        @endforelse
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-primary small fw-semibold" href="#" data-bs-toggle="modal" data-bs-target="#modalTambahEdisi">
                                <i class="fa-solid fa-plus-circle me-1"></i> Tambah Edisi Tahun Baru
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- TOMBOL TAMBAH EDISI -->
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm bg-white" data-bs-toggle="modal" data-bs-target="#modalTambahEdisi">
                    <i class="fa-solid fa-plus me-1"></i> Buka Edisi Baru
                </button>
            </div>
        </div>

        <!-- ROW PILLS TAHUN UNTUK AKSES CEPAT -->
        <div class="d-flex gap-2 mb-3 overflow-auto pb-1 align-items-center">
            <span class="small text-muted me-1 fw-semibold text-nowrap"><i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Edisi:</span>
            @foreach($edisiList as $item)
                <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $item->tahun]) }}" 
                   class="badge text-decoration-none px-3 py-2 rounded-pill border {{ $item->tahun == $tahun ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-dark border-light-subtle hover-shadow' }}">
                    {{ $item->tahun }}
                    @if($item->status_edisi == 'REGISTRASI' || $item->status_edisi == 'RUNNING')
                        <span class="badge bg-success ms-1" style="font-size: 0.6rem;">Aktif</span>
                    @endif
                </a>
            @endforeach
            <button type="button" class="badge text-decoration-none px-3 py-2 rounded-pill border border-dashed text-primary bg-white hover-shadow" data-bs-toggle="modal" data-bs-target="#modalTambahEdisi">
                <i class="fa-solid fa-plus me-1"></i>Tambah Edisi
            </button>
        </div>

        @if(!$edisi)
        <!-- EMPTY STATE JIKA TAHUN TERSEBUT BELUM MEMILIKI EDISI -->
        <div class="card shadow-sm border-0 mb-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-calendar-xmark fa-4x text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold text-dark">Edisi Tahun {{ $tahun }} Belum Dibuat</h4>
                <p class="text-muted col-md-6 mx-auto mb-4">
                    Turnamen {{ $turnamen->nama_turnamen }} belum memiliki data edisi resmi untuk kalender tahun {{ $tahun }}.
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahEdisi">
                    <i class="fa-solid fa-plus-circle me-1"></i> Buka Edisi Tahun {{ $tahun }} Sekarang
                </button>
            </div>
        </div>
        @else
        <!-- HERO CARD EDISI TURNAMEN -->
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-white text-primary fw-bold font-monospace">{{ $turnamen->slug }}</span>
                            <span class="badge bg-warning text-dark fw-bold">TAHUN {{ $tahun }}</span>
                        </div>
                        <h2 class="text-white fw-bold mb-1">{{ $edisi->nama_edisi }}</h2>
                        <p class="mb-0 text-white-50 small">
                            <i class="fa-solid fa-location-dot me-1"></i> {{ $edisi->kota_tuan_rumah ?? 'Indonesia' }} &bull; 
                            <i class="fa-solid fa-building me-1 ms-2"></i> {{ $edisi->venue_utama ?? 'GOR Resmi PBVSI' }} &bull;
                            <i class="fa-solid fa-calendar me-1 ms-2"></i> {{ $edisi->tanggal_mulai ? date('d M Y', strtotime($edisi->tanggal_mulai)) : '-' }} s/d {{ $edisi->tanggal_selesai ? date('d M Y', strtotime($edisi->tanggal_selesai)) : '-' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Status: {{ $edisi->status_edisi }}</span>
                    </div>
                </div>

                <div class="row mt-3 pt-3 border-top border-white-50 small">
                    <div class="col-md-4">
                        <span class="text-white-50">Batas Tanggal Lahir Atlet:</span>
                        <div class="fw-bold fs-6">
                            @if($edisi->min_tanggal_lahir && $edisi->max_tanggal_lahir)
                                {{ date('d M Y', strtotime($edisi->min_tanggal_lahir)) }} s/d {{ date('d M Y', strtotime($edisi->max_tanggal_lahir)) }}
                            @else
                                <span class="fw-normal">Bebas / Kategori Senior (Open)</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="text-white-50">Maksimal Roster per Tim:</span>
                        <div class="fw-bold fs-6">{{ $edisi->max_kuota_pemain ?? 14 }} Pemain (Termasuk Libero)</div>
                    </div>
                    <div class="col-md-4">
                        <span class="text-white-50">Terintegrasi Master Data:</span>
                        <div class="fw-bold fs-6"><i class="fa-solid fa-circle-check text-success"></i> Single Source of Truth PBVSI</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION (ALPINE.JS) -->
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <button class="nav-link rounded-pill px-3" :class="activeTab === 'overview' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'overview'">
                    <i class="fa-solid fa-gauge-high me-1"></i> Overview & Statistik
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-3" :class="activeTab === 'teams' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'teams'">
                    <i class="fa-solid fa-shield me-1"></i> Tim Peserta ({{ $edisi->timPeserta->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-3" :class="activeTab === 'roster' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'roster'">
                    <i class="fa-solid fa-users me-1"></i> Roster Atlet
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-3" :class="activeTab === 'otwo' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'otwo'">
                    <i class="fa-solid fa-file-excel me-1 text-success"></i> Formulir O-2 (FIVB / VIS)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-3" :class="activeTab === 'schedule' ? 'active bg-primary' : 'bg-white shadow-sm text-dark'" @click="activeTab = 'schedule'">
                    <i class="fa-solid fa-calendar-days me-1"></i> Jadwal Pertandingan
                </button>
            </li>
        </ul>

        <!-- TAB CONTENT: OVERVIEW -->
        <div x-show="activeTab === 'overview'" x-transition>
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold text-dark mb-0">Petunjuk Regulasi & Ketentuan Kompetisi</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info border-0 shadow-sm mb-3">
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-shield-halved me-1"></i> Integritas Data & Verifikasi Dokumen</h6>
                                <p class="small mb-0">
                                    Setiap atlet yang didaftarkan ke dalam edisi {{ $edisi->nama_edisi }} wajib lolos verifikasi berkas (Akta Kelahiran, Ijazah, NISN/NIK, dan Kartu Keluarga) yang tersinkronisasi langsung dengan <strong>Bank Pemain PBVSI Pusat</strong>.
                                </p>
                            </div>

                            <div class="row g-3 text-center">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small">Total Tim Peserta</div>
                                        <div class="fs-3 fw-bold text-primary">{{ $edisi->timPeserta->count() }}</div>
                                        <span class="small text-muted">{{ $timPutra->count() }} Putra / {{ $timPutri->count() }} Putri</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small">Status Kalender</div>
                                        <div class="fs-4 fw-bold text-success mt-1">{{ $edisi->status_edisi }}</div>
                                        <span class="small text-muted">Edisi Tahun {{ $tahun }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small">Batas Kuota Tim</div>
                                        <div class="fs-3 fw-bold text-dark">{{ $edisi->max_kuota_pemain ?? 14 }}</div>
                                        <span class="small text-muted">Maks. Pemain / Tim</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold text-dark mb-0">Informasi Lokasi & Venue</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Kota Penyelenggara:</span>
                                    <span class="fw-semibold text-dark">{{ $edisi->kota_tuan_rumah ?? '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Venue Pertandingan:</span>
                                    <span class="fw-semibold text-dark">{{ $edisi->venue_utama ?? '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Tanggal Pelaksanaan:</span>
                                    <span class="fw-semibold text-dark">
                                        {{ $edisi->tanggal_mulai ? date('d/m/Y', strtotime($edisi->tanggal_mulai)) : '-' }} s/d {{ $edisi->tanggal_selesai ? date('d/m/Y', strtotime($edisi->tanggal_selesai)) : '-' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: TEAMS -->
        <div x-show="activeTab === 'teams'" x-transition>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">Daftar Tim Peserta Edisi {{ $tahun }}</h5>
                    <span class="badge bg-primary">{{ $edisi->timPeserta->count() }} Tim Terdaftar</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">#</th>
                                    <th>Nama Tim / Klub</th>
                                    <th>Kategori Gender</th>
                                    <th>Asal Pengprov</th>
                                    <th>Jumlah Roster</th>
                                    <th>Status Tim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($edisi->timPeserta as $index => $tim)
                                <tr>
                                    <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tim->nama_tim }}</div>
                                        <div class="small text-muted">{{ $tim->kode_tim ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if($tim->kategori_gender == 'PA')
                                            <span class="badge bg-info-subtle text-info border px-2"><i class="fa-solid fa-mars me-1"></i>Putra (PA)</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border px-2"><i class="fa-solid fa-venus me-1"></i>Putri (PI)</span>
                                        @endif
                                    </td>
                                    <td>{{ $tim->pengprov?->nama_pengprov ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $tim->roster->count() }} Atlet</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-check me-1"></i>Terverifikasi</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fa-solid fa-shield-halved fa-3x text-secondary mb-3 d-block opacity-50"></i>
                                        <div class="fw-bold text-muted">Belum ada tim yang mendaftar pada edisi {{ $tahun }} ini.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: ROSTER -->
        <div x-show="activeTab === 'roster'" x-transition>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-users fa-3x text-primary mb-3 d-block opacity-50"></i>
                    <h5 class="fw-bold text-dark">Roster Atlet Edisi {{ $tahun }}</h5>
                    <p class="text-muted col-md-6 mx-auto">
                        Daftar atlet pemain yang terdaftar pada tim peserta edisi {{ $tahun }}.
                    </p>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: O-2 FORM -->
        <div x-show="activeTab === 'otwo'" x-transition>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-file-excel fa-3x text-success mb-3 d-block opacity-50"></i>
                    <h5 class="fw-bold text-dark">Formulir O-2 Resmi (VIS & FIVB Standard)</h5>
                    <p class="text-muted col-md-6 mx-auto">
                        Formulir O-2 resmi tim peserta edisi {{ $tahun }} untuk pendaftaran kejuaraan nasional PBVSI.
                    </p>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: SCHEDULE -->
        <div x-show="activeTab === 'schedule'" x-transition>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-calendar-days fa-3x text-warning mb-3 d-block opacity-50"></i>
                    <h5 class="fw-bold text-dark">Jadwal & Hasil Pertandingan {{ $tahun }}</h5>
                    <p class="text-muted col-md-6 mx-auto">
                        Jadwal pertandingan fase penyisihan pul, babak gugur, dan perebutan medali edisi {{ $tahun }}.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- MODAL TAMBAH EDISI TAHUN BARU -->
<div class="modal fade" id="modalTambahEdisi" tabindex="-1" aria-labelledby="modalTambahEdisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('kejuaraan.edisi.store', ['slug' => $slug]) }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahEdisiLabel">
                        <i class="fa-solid fa-calendar-plus me-1"></i> Buka Edisi Tahun Baru: {{ $turnamen->nama_turnamen }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun Kalender <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') + 1 }}" min="2020" max="2035" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Resmi Edisi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_edisi" class="form-control" required placeholder="Contoh: {{ $turnamen->nama_turnamen }} Tahun {{ date('Y') + 1 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kota Tuan Rumah</label>
                            <input type="text" name="kota_tuan_rumah" class="form-control" placeholder="Contoh: Surabaya, Jawa Timur">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Venue Utama</label>
                            <input type="text" name="venue_utama" class="form-control" placeholder="Contoh: DBL Arena Surabaya">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Min. Tanggal Lahir Atlet</label>
                            <input type="date" name="min_tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maks. Tanggal Lahir Atlet</label>
                            <input type="date" name="max_tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maksimal Kuota Pemain</label>
                            <input type="number" name="max_kuota_pemain" class="form-control" value="14" min="6" max="20" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Status Awal Edisi</label>
                            <select name="status_edisi" class="form-select" required>
                                <option value="REGISTRASI" selected>REGISTRASI (Masa Pendaftaran Terbuka)</option>
                                <option value="DRAFT">DRAFT (Persiapan Teknis)</option>
                                <option value="RUNNING">RUNNING (Sedang Berjalan)</option>
                                <option value="FINISHED">FINISHED (Kejuaraan Selesai / Arsip)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Buka Edisi Baru</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush
