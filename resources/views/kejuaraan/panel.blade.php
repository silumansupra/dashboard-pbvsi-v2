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
        <strong>Terdapat kesalahan validasi input:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- BREADCRUMB & HEADER AKSI -->
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
                    Liga Bolavoli Indonesia (Livoli)
                @elseif($turnamen->kategori == 'KAPOLRI_CUP')
                    Piala Kapolri
                @elseif($turnamen->kategori == 'VOLI_PANTAI')
                    Bolavoli Pantai
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

    <!-- ACTION BUTTONS: TAHUN SWITCHER, KELOLA EDISI & ASSIGN TIM -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- DROPDOWN PILIH TAHUN EDISI -->
        <div class="dropdown">
            <button class="btn btn-sm btn-white bg-white border shadow-sm dropdown-toggle fw-semibold text-dark px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-calendar-days text-primary me-1"></i> Edisi: <span class="text-primary fw-bold">{{ $tahun }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li class="dropdown-header small text-uppercase text-muted">Pilih Kalender Edisi</li>
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
                        <i class="fa-solid fa-plus-circle me-1"></i> Buka Edisi Tahun Baru
                    </a>
                </li>
            </ul>
        </div>

        @if($edisi)
        <!-- TOMBOL EDIT PENGATURAN EDISI TAHUN INI -->
        <button type="button" class="btn btn-sm btn-white bg-white border shadow-sm text-dark px-3" data-bs-toggle="modal" data-bs-target="#modalEditEdisi" title="Edit Pengaturan Edisi Tahun {{ $tahun }}">
            <i class="fa-solid fa-gear text-secondary me-1"></i> Pengaturan Edisi
        </button>

        <!-- TOMBOL ASSIGN TIM PESERTA -->
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAssignTim">
            <i class="fa-solid fa-user-plus me-1"></i> Assign Tim Peserta
        </button>
        @endif
    </div>
</div>

@if(!$edisi)
<!-- EMPTY STATE JIKA TAHUN BELUM MEMILIKI EDISI -->
<div class="card shadow-sm border-0 mb-4 text-center py-5">
    <div class="card-body">
        <i class="fa-solid fa-calendar-xmark fa-4x text-muted opacity-50 mb-3"></i>
        <h4 class="fw-bold text-dark">Edisi Tahun {{ $tahun }} Belum Dibuat</h4>
        <p class="text-muted col-md-6 mx-auto mb-4">
            Turnamen {{ $turnamen->nama_turnamen }} belum memiliki data edisi kalender resmi untuk tahun {{ $tahun }}.
        </p>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahEdisi">
            <i class="fa-solid fa-plus-circle me-1"></i> Buka Edisi Tahun {{ $tahun }} Sekarang
        </button>
    </div>
</div>
@else

<!-- RINGKASAN EDISI & STATISTIK CEPAT (COMPACT) -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3 px-4">
        <div class="row align-items-center g-2">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="fw-bold text-dark mb-0">{{ $edisi->nama_edisi }}</h5>
                    <span class="badge {{ $edisi->status_edisi == 'REGISTRASI' ? 'bg-success' : ($edisi->status_edisi == 'FINISHED' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                        {{ $edisi->status_edisi }}
                    </span>
                </div>
                <div class="small text-muted">
                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $edisi->kota_tuan_rumah ?: 'Indonesia' }} ({{ $edisi->venue_utama ?: 'GOR PBVSI' }})
                    <span class="mx-1">&bull;</span>
                    <i class="fa-solid fa-calendar me-1 text-primary"></i> {{ $edisi->tanggal_mulai ? date('d M Y', strtotime($edisi->tanggal_mulai)) : '-' }} s/d {{ $edisi->tanggal_selesai ? date('d M Y', strtotime($edisi->tanggal_selesai)) : '-' }}
                    <span class="mx-1">&bull;</span>
                    <i class="fa-solid fa-users me-1 text-secondary"></i> Kuota: <strong>{{ $edisi->max_kuota_pemain ?? 14 }} Atlet/Tim</strong>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="fa-solid fa-shield me-1 text-primary"></i> Total Tim: <strong>{{ $stats['total'] }}</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info border px-3 py-2">
                        <i class="fa-solid fa-mars me-1"></i> Putra: <strong>{{ $stats['putra'] }}</strong>
                    </span>
                    <span class="badge bg-danger-subtle text-danger border px-3 py-2">
                        <i class="fa-solid fa-venus me-1"></i> Putri: <strong>{{ $stats['putri'] }}</strong>
                    </span>
                    <span class="badge bg-success-subtle text-success border px-3 py-2">
                        <i class="fa-solid fa-circle-check me-1"></i> Aktif: <strong>{{ $stats['aktif'] }}</strong>
                    </span>
                    @if($stats['trashed'] > 0)
                    <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun, 'status' => 'trashed']) }}" 
                       class="badge bg-secondary-subtle text-secondary border px-3 py-2 text-decoration-none" title="Lihat tim yang dikeluarkan">
                        <i class="fa-solid fa-trash-can me-1"></i> Dikeluarkan: <strong>{{ $stats['trashed'] }}</strong>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CARD TABEL UTAMA TIM PESERTA -->
<div class="card shadow-sm border-0">
    <!-- TOOLBAR PENCARIAN & FILTER -->
    <div class="card-header bg-white py-3 border-bottom">
        <form action="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari tim, kode, pul, pengprov/klub..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-2 col-6">
                <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="PA" {{ request('gender') === 'PA' ? 'selected' : '' }}>Putra (PA)</option>
                    <option value="PI" {{ request('gender') === 'PI' ? 'selected' : '' }}>Putri (PI)</option>
                </select>
            </div>

            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all_active" {{ request('status', 'all_active') === 'all_active' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    <option value="trashed" {{ request('status') === 'trashed' ? 'selected' : '' }}>Dikeluarkan ({{ $stats['trashed'] }})</option>
                    <option value="with_trashed" {{ request('status') === 'with_trashed' ? 'selected' : '' }}>Semua Termasuk Dikeluarkan</option>
                </select>
            </div>

            <div class="col-md-2 col-6">
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / Halaman</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / Halaman</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / Halaman</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Halaman</option>
                </select>
            </div>

            <div class="col-md-2 col-6 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('gender') || request('status') || request('per_page'))
                    <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL TIM PESERTA -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">#</th>
                        <th>Tim Peserta Bolavoli</th>
                        <th class="text-center" style="width: 120px;">Kategori</th>
                        <th>Asal Master Organisasi</th>
                        <th class="text-center" style="width: 110px;">Pul / Grup</th>
                        <th class="text-center" style="width: 110px;">Roster</th>
                        <th class="text-center" style="width: 130px;">Status Aktif</th>
                        <th class="text-end pe-3" style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $index => $tim)
                    <tr class="{{ $tim->trashed() ? 'table-light opacity-75' : '' }}">
                        <td class="ps-3 text-center text-muted small">
                            {{ $teams->firstItem() ? $teams->firstItem() + $index : $index + 1 }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($tim->logo_url)
                                    <div class="rounded border p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                        <img src="{{ $tim->logo_url }}" alt="{{ $tim->nama_tim }}" class="img-fluid object-fit-contain" style="max-height: 32px; max-width: 32px;" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-shield-halved text-primary\'></i>';">
                                    </div>
                                @else
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <a href="{{ route('kejuaraan.tim.show', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}" class="text-dark text-decoration-none hover-primary">
                                            {{ $tim->nama_tim }}
                                        </a>
                                        @if($tim->trashed())
                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Dikeluarkan</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted font-monospace">
                                        Kode: {{ $tim->kode_tim ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($tim->kategori_gender == 'PA')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="fa-solid fa-mars me-1"></i> Putra (PA)
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fa-solid fa-venus me-1"></i> Putri (PI)
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($tim->pengprov)
                                <div><i class="fa-solid fa-flag text-secondary me-1"></i> <strong>{{ $tim->pengprov->nama_pengprov }}</strong></div>
                            @elseif($tim->klub)
                                <div><i class="fa-solid fa-shield-halved text-secondary me-1"></i> <strong>{{ $tim->klub->nama_klub }}</strong></div>
                                <div class="small text-muted">{{ $tim->klub->pengprov?->nama_pengprov ?: '-' }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($tim->pool_grup)
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1 fw-bold">
                                    Pul {{ $tim->pool_grup }}
                                </span>
                            @else
                                <span class="text-muted small">Belum diundi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kejuaraan.tim.show', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}" 
                               class="badge bg-light text-dark border text-decoration-none px-2 py-1 hover-shadow" title="Lihat Roster Atlet">
                                <i class="fa-solid fa-users text-primary me-1"></i> {{ $tim->roster_count }} Atlet
                            </a>
                        </td>
                        <td class="text-center">
                            @if($tim->trashed())
                                <span class="badge bg-secondary">Nonaktif</span>
                            @else
                                <!-- TOGGLE SWITCH AKTIF / NONAKTIFKAN -->
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input toggle-status-switch" 
                                           type="checkbox" 
                                           role="switch" 
                                           id="toggle_{{ $tim->id }}"
                                           data-url="{{ route('kejuaraan.tim.toggle_active', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}"
                                           {{ $tim->is_active ? 'checked' : '' }}
                                           style="cursor: pointer; width: 2.2em; height: 1.2em;">
                                    <label class="form-check-label small ms-1 {{ $tim->is_active ? 'text-success fw-bold' : 'text-muted' }}" for="toggle_{{ $tim->id }}" id="label_{{ $tim->id }}">
                                        {{ $tim->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <!-- TOMBOL DETAIL -->
                                <a href="{{ route('kejuaraan.tim.show', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Lihat Detail Profil, Roster & Official">
                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                </a>

                                @if(!$tim->trashed())
                                    <!-- TOMBOL ATUR PUL / KEPESERTAAN -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAturPul"
                                            data-action="{{ route('kejuaraan.tim.atur_pul', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}"
                                            data-nama="{{ $tim->nama_tim }}"
                                            data-gender="{{ $tim->kategori_gender }}"
                                            data-pul="{{ $tim->pool_grup }}"
                                            title="Atur Pul / Pool Grup Pertandingan">
                                        <i class="fa-solid fa-shuffle"></i> Pul
                                    </button>

                                    <!-- TOMBOL KELUARKAN DARI TURNAMEN (SOFT DELETE PARTICIPATION) -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDeleteTim" 
                                            data-action="{{ route('kejuaraan.tim.destroy', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}"
                                            data-nama="{{ $tim->nama_tim }}"
                                            title="Keluarkan dari Turnamen Ini">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @else
                                    <!-- TOMBOL PULIHKAN KEPESERTAAN -->
                                    <form action="{{ route('kejuaraan.tim.restore', ['slug' => $slug, 'tahun' => $tahun, 'id' => $tim->encrypted_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan keikutsertaan tim {{ $tim->nama_tim }} di turnamen ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Pulihkan Tim">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Pulihkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="fa-solid fa-shield-halved fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Tim Peserta yang Di-Assign</h6>
                                <p class="small text-muted mb-3">
                                    @if(request('search') || request('gender') || request('status'))
                                        Tidak ada tim peserta yang sesuai dengan kriteria filter pencarian.
                                    @else
                                        Pilih dan assign tim dari Master Klub / Pengprov PBVSI untuk mengikuti edisi {{ $edisi->nama_edisi }}.
                                    @endif
                                </p>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAssignTim">
                                    <i class="fa-solid fa-user-plus me-1"></i> Assign Tim Peserta Sekarang
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    @if($teams && ($teams->hasPages() || $teams->total() > 0))
    <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Menampilkan <strong>{{ $teams->firstItem() ?: 0 }}</strong> - <strong>{{ $teams->lastItem() ?: 0 }}</strong> dari <strong>{{ $teams->total() }}</strong> Tim Peserta
        </div>
        <div>
            {{ $teams->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endif

<!-- ============================================================== -->
<!-- MODAL ASSIGN TIM PESERTA (PILIH DARI MASTER KLUB / PENGPROV)   -->
<!-- ============================================================== -->
@if($edisi)
<div class="modal fade" id="modalAssignTim" tabindex="-1" aria-labelledby="modalAssignTimLabel" aria-hidden="true" x-data="{ tipeAsal: 'pengprov' }">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('kejuaraan.tim.assign', ['slug' => $slug, 'tahun' => $tahun]) }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalAssignTimLabel">
                        <i class="fa-solid fa-user-plus me-1"></i> Assign Tim ke Edisi {{ $tahun }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-3 small">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Pilih tim dari database resmi <strong>Master PBVSI</strong>. Data profil dan legalitas tim dikelola di Master Data PBVSI, sedangkan roster atlet dan official diatur secara mandiri oleh Admin Tim masing-masing di portal tim.
                    </div>

                    <div class="row g-3">
                        <!-- PILIH ASAL: PENGPROV ATAU KLUB -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jenis Asal Tim Peserta <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="tipe_asal" id="tipePengprov" value="pengprov" x-model="tipeAsal" checked>
                                    <label class="form-check-label fw-bold text-dark" for="tipePengprov">
                                        <i class="fa-solid fa-flag text-primary me-1"></i> Kontingen Daerah (Pengprov)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="tipe_asal" id="tipeKlub" value="klub" x-model="tipeAsal">
                                    <label class="form-check-label fw-bold text-dark" for="tipeKlub">
                                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Klub Bolavoli PBVSI
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- DROPDOWN PENGPROV -->
                        <div class="col-md-12" x-show="tipeAsal === 'pengprov'">
                            <label class="form-label fw-semibold">Pilih Kontingen Pengprov PBVSI <span class="text-danger">*</span></label>
                            <select name="pengprov_id" class="form-select">
                                <option value="">-- Pilih Pengprov Peserta --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->nama_pengprov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DROPDOWN KLUB -->
                        <div class="col-md-12" x-show="tipeAsal === 'klub'" x-cloak>
                            <label class="form-label fw-semibold">Pilih Klub Bolavoli PBVSI <span class="text-danger">*</span></label>
                            <select name="klub_id" class="form-select">
                                <option value="">-- Pilih Klub Bolavoli --</option>
                                @foreach($klubList as $k)
                                    <option value="{{ $k->id }}">
                                        {{ $k->nama_klub }} ({{ $k->kode_klub ?: '-' }}) - {{ $k->pengprov?->nama_pengprov ?: 'PBVSI' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KATEGORI GENDER -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Gender <span class="text-danger">*</span></label>
                            <select name="kategori_gender" class="form-select" required>
                                <option value="PA" selected>Putra (PA)</option>
                                <option value="PI">Putri (PI)</option>
                            </select>
                        </div>

                        <!-- PUL / POOL GRUP -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pul / Pool Grup</label>
                            <select name="pool_grup" class="form-select">
                                <option value="">-- Belum Diundi / Bebas --</option>
                                @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $pul)
                                    <option value="{{ $pul }}">Pul {{ $pul }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- NAMA TIM CUSTOM (OPSIONAL) -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Nama Tim Penyesuaian (Opsional)</label>
                            <input type="text" name="nama_tim_custom" class="form-control form-control-sm" placeholder="Kosongkan jika ingin mengikuti nama resmi Pengprov/Klub otomatis">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Assign Tim Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL ATUR PUL / KEPESERTAAN TIM                               -->
<!-- ============================================================== -->
<div class="modal fade" id="modalAturPul" tabindex="-1" aria-labelledby="modalAturPulLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formAturPul" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalAturPulLabel">
                        <i class="fa-solid fa-shuffle me-1 text-primary"></i> Atur Pul Pertandingan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border py-2 px-3 mb-3 fw-bold fs-6 text-dark" id="aturPulNamaTim">
                        -
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Gender</label>
                            <select name="kategori_gender" id="aturPulGender" class="form-select" required>
                                <option value="PA">Putra (PA)</option>
                                <option value="PI">Putri (PI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penempatan Pul / Grup</label>
                            <select name="pool_grup" id="aturPulPul" class="form-select">
                                <option value="">-- Belum Diundi --</option>
                                @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $pul)
                                    <option value="{{ $pul }}">Pul {{ $pul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Penyesuaian Nama Tampil Tim</label>
                            <input type="text" name="nama_tim_custom" id="aturPulNamaCustom" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penempatan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL EDIT PENGATURAN EDISI TAHUN                              -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditEdisi" tabindex="-1" aria-labelledby="modalEditEdisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('kejuaraan.edisi.update', ['slug' => $slug, 'tahun' => $tahun]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalEditEdisiLabel">
                        <i class="fa-solid fa-gear me-1 text-primary"></i> Pengaturan Edisi Tahun {{ $tahun }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Resmi Edisi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_edisi" class="form-control" value="{{ old('nama_edisi', $edisi->nama_edisi) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kota Tuan Rumah</label>
                            <input type="text" name="kota_tuan_rumah" class="form-control" value="{{ old('kota_tuan_rumah', $edisi->kota_tuan_rumah) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Venue Utama</label>
                            <input type="text" name="venue_utama" class="form-control" value="{{ old('venue_utama', $edisi->venue_utama) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $edisi->tanggal_mulai ? date('Y-m-d', strtotime($edisi->tanggal_mulai)) : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $edisi->tanggal_selesai ? date('Y-m-d', strtotime($edisi->tanggal_selesai)) : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Min. Tanggal Lahir</label>
                            <input type="date" name="min_tanggal_lahir" class="form-control" value="{{ old('min_tanggal_lahir', $edisi->min_tanggal_lahir ? date('Y-m-d', strtotime($edisi->min_tanggal_lahir)) : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maks. Tanggal Lahir</label>
                            <input type="date" name="max_tanggal_lahir" class="form-control" value="{{ old('max_tanggal_lahir', $edisi->max_tanggal_lahir ? date('Y-m-d', strtotime($edisi->max_tanggal_lahir)) : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maks. Kuota Pemain</label>
                            <input type="number" name="max_kuota_pemain" class="form-control" value="{{ old('max_kuota_pemain', $edisi->max_kuota_pemain ?? 14) }}" min="6" max="20" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Status Pelaksanaan Edisi</label>
                            <select name="status_edisi" class="form-select" required>
                                <option value="REGISTRASI" {{ $edisi->status_edisi === 'REGISTRASI' ? 'selected' : '' }}>REGISTRASI (Masa Pendaftaran Terbuka)</option>
                                <option value="DRAFT" {{ $edisi->status_edisi === 'DRAFT' ? 'selected' : '' }}>DRAFT (Persiapan Teknis)</option>
                                <option value="RUNNING" {{ $edisi->status_edisi === 'RUNNING' ? 'selected' : '' }}>RUNNING (Sedang Berjalan)</option>
                                <option value="FINISHED" {{ $edisi->status_edisi === 'FINISHED' ? 'selected' : '' }}>FINISHED (Kejuaraan Selesai / Arsip)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <!-- TOMBOL HAPUS EDISI (SOFT DELETE) -->
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDeleteEdisi">
                        <i class="fa-solid fa-trash-can me-1"></i> Hapus / Arsipkan Edisi Ini
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL KONFIRMASI HAPUS EDISI                                   -->
<!-- ============================================================== -->
<div class="modal fade" id="modalDeleteEdisi" tabindex="-1" aria-labelledby="modalDeleteEdisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('kejuaraan.edisi.destroy', ['slug' => $slug, 'tahun' => $tahun]) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="modalDeleteEdisiLabel">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus Edisi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus / mengarsipkan edisi berikut?</p>
                    <div class="alert alert-light border fw-bold text-dark fs-6 py-2 px-3 mb-3">
                        {{ $edisi->nama_edisi }} (Tahun {{ $tahun }})
                    </div>
                    <div class="small text-muted">
                        <i class="fa-solid fa-circle-info text-info me-1"></i> Seluruh data kepesertaan edisi ini akan diarsipkan (soft delete).
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus Edisi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ============================================================== -->
<!-- MODAL KONFIRMASI KELUARKAN TIM DARI TURNAMEN (UNASSIGN)        -->
<!-- ============================================================== -->
<div class="modal fade" id="modalDeleteTim" tabindex="-1" aria-labelledby="modalDeleteTimLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDeleteTim" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="modalDeleteTimLabel">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Keluarkan Tim
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Keluarkan tim berikut dari keikutsertaan turnamen ini?</p>
                    <div class="alert alert-light border fw-bold text-dark fs-6 py-2 px-3 mb-3" id="deleteTimNama">
                        -
                    </div>
                    <div class="small text-muted">
                        <i class="fa-solid fa-circle-info text-info me-1"></i> Tindakan ini hanya melepaskan tim dari turnamen edisi ini (soft delete). <strong>Data master klub/pengprov PBVSI tidak akan terhapus</strong> dan tim dapat dimasukkan kembali sewaktu-waktu.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-trash-can me-1"></i> Keluarkan Tim
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL BUKA EDISI TAHUN BARU                                    -->
<!-- ============================================================== -->
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
                            <input type="text" name="venue_utama" class="form-control" placeholder="Contoh: GOR DBL Arena">
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
                            <label class="form-label fw-semibold">Min. Tanggal Lahir</label>
                            <input type="date" name="min_tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maks. Tanggal Lahir</label>
                            <input type="date" name="max_tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Maks. Kuota Pemain</label>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Handling Modal Delete Tim
    const modalDeleteTim = document.getElementById('modalDeleteTim');
    if (modalDeleteTim) {
        modalDeleteTim.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-action');
            const nama = button.getAttribute('data-nama');

            const form = modalDeleteTim.querySelector('#formDeleteTim');
            const labelNama = modalDeleteTim.querySelector('#deleteTimNama');

            form.action = action;
            labelNama.textContent = nama;
        });
    }

    // 2. Handling Modal Atur Pul
    const modalAturPul = document.getElementById('modalAturPul');
    if (modalAturPul) {
        modalAturPul.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-action');
            const nama = button.getAttribute('data-nama');
            const gender = button.getAttribute('data-gender');
            const pul = button.getAttribute('data-pul') || '';

            const form = modalAturPul.querySelector('#formAturPul');
            const labelNama = modalAturPul.querySelector('#aturPulNamaTim');
            const selectGender = modalAturPul.querySelector('#aturPulGender');
            const selectPul = modalAturPul.querySelector('#aturPulPul');
            const inputCustom = modalAturPul.querySelector('#aturPulNamaCustom');

            form.action = action;
            labelNama.textContent = nama;
            if (selectGender) selectGender.value = gender;
            if (selectPul) selectPul.value = pul;
            if (inputCustom) inputCustom.value = nama;
        });
    }

    // 3. AJAX Toggle Status Aktif / Nonaktif
    const toggleSwitches = document.querySelectorAll('.toggle-status-switch');
    toggleSwitches.forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const url = this.getAttribute('data-url');
            const isChecked = this.checked;
            const timId = this.id.replace('toggle_', '');
            const label = document.getElementById('label_' + timId);

            // Optimistic UI update
            if (label) {
                label.textContent = isChecked ? 'Aktif' : 'Nonaktif';
                label.className = 'form-check-label small ms-1 ' + (isChecked ? 'text-success fw-bold' : 'text-muted');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_active: isChecked
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memperbarui status');
                }
                return response.json();
            })
            .then(data => {
                if (label && typeof data.is_active !== 'undefined') {
                    label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                    label.className = 'form-check-label small ms-1 ' + (data.is_active ? 'text-success fw-bold' : 'text-muted');
                }
            })
            .catch(error => {
                console.error('Error toggle status:', error);
                toggle.checked = !isChecked;
                if (label) {
                    label.textContent = !isChecked ? 'Aktif' : 'Nonaktif';
                    label.className = 'form-check-label small ms-1 ' + (!isChecked ? 'text-success fw-bold' : 'text-muted');
                }
                alert('Terjadi kesalahan saat memperbarui status aktif tim.');
            });
        });
    });
});
</script>
@endpush
