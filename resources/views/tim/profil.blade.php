@extends('layouts.app')

@section('title', 'Profil & Jersey Tim - ' . $activeTeam->nama_tim)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Portal Tim</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil & Jersey Tim</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- HEADER CARD -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center shadow overflow-hidden" style="width: 72px; height: 72px; font-size: 2rem; font-weight: bold;">
                    @if($activeTeam->logo_url)
                        <img src="{{ $activeTeam->logo_url }}" alt="{{ $activeTeam->nama_tim }}" class="rounded-circle w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/no-logo.svg') }}';">
                    @else
                        <img src="{{ asset('images/no-logo.svg') }}" alt="{{ $activeTeam->nama_tim }}" class="rounded-circle w-100 h-100 object-fit-cover p-2">
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-white">{{ $activeTeam->nama_tim }}</h3>
                    <span class="badge {{ $activeTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }} px-3 py-1 fs-6">
                        <i class="fa-solid {{ $activeTeam->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                        {{ $activeTeam->kategori_gender == 'PA' ? 'TIM PUTRA (PA)' : 'TIM PUTRI (PI)' }}
                    </span>
                    @if($activeTeam->status_verifikasi == 'VERIFIED')
                        <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> VERIFIED PBVSI</span>
                    @elseif($activeTeam->status_verifikasi == 'SUBMITTED')
                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-clock me-1"></i> MENUNGGU VERIFIKASI</span>
                    @else
                        <span class="badge bg-secondary px-2 py-1"><i class="fa-solid fa-file-pen me-1"></i> DRAFT</span>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                    @if($activeTeam->edisi && $activeTeam->edisi->turnamen)
                        <div><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $activeTeam->edisi->turnamen->nama_turnamen }} ({{ $activeTeam->edisi->tahun_edisi }})</div>
                    @endif
                    @if($activeTeam->pengprov)
                        <div><i class="fa-solid fa-location-dot text-info me-1"></i> {{ $activeTeam->pengprov->nama_pengprov }}</div>
                    @endif
                    <div><i class="fa-solid fa-user-tie text-success me-1"></i> Admin: <strong>{{ $user->name }}</strong></div>
                </div>
            </div>

            @if($teams->count() > 1)
            <div class="col-12 col-md-auto text-md-end">
                <div class="small text-white-50 mb-1"><i class="fa-solid fa-repeat me-1"></i> Ganti Tim:</div>
                <div class="btn-group shadow-sm" role="group">
                    @foreach($teams as $t)
                        <a href="{{ route('tim.switch', $t->id) }}" 
                           class="btn btn-sm {{ $t->id == $activeTeam->id ? ($t->kategori_gender == 'PA' ? 'btn-primary' : 'btn-danger') : 'btn-outline-light' }} px-3 py-2 fw-semibold">
                            <i class="fa-solid {{ $t->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                            {{ $t->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<form action="{{ route('tim.profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- DETAIL INFORMASI TIM -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Informasi Tim & Kontingen
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Nama Tim</td>
                            <td class="fw-bold text-dark">{{ $activeTeam->nama_tim }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kategori</td>
                            <td>
                                <span class="badge {{ $activeTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ $activeTeam->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Turnamen / Edisi</td>
                            <td class="fw-semibold text-dark">{{ $activeTeam->edisi?->turnamen?->nama_turnamen ?? '-' }} ({{ $activeTeam->edisi?->tahun_edisi ?? '' }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengurus Provinsi</td>
                            <td class="text-dark">{{ $activeTeam->pengprov?->nama_pengprov ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Klub Asal</td>
                            <td class="text-dark">{{ $activeTeam->klub?->nama_klub ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pool / Grup</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $activeTeam->pool_grup ?: 'Belum diundi' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status PBVSI</td>
                            <td>
                                @if($activeTeam->status_verifikasi == 'VERIFIED')
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> VERIFIED</span>
                                @elseif($activeTeam->status_verifikasi == 'SUBMITTED')
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> SUBMITTED</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-file-pen me-1"></i> DRAFT</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-image me-2 text-info"></i>Logo & Foto Skuad</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Upload / Perbarui Logo Tim</label>
                        <input type="file" name="logo_tim" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Format: PNG, JPG, WebP. Maks 2MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Upload / Perbarui Foto Tim / Skuad</label>
                        <input type="file" name="foto_skuad" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Format: PNG, JPG, WebP. Maks 4MB.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM WARNA JERSEY -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-shirt text-danger me-2"></i>Spesifikasi Warna Jersey Pertandingan
                    </h5>
                    <span class="badge bg-info-subtle text-info border">Regulasi FIVB / PBVSI</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Sesuai regulasi Kejurnas PBVSI, setiap kontingen wajib mendaftarkan minimal 2 kombinasi warna jersey pemain dan warna jersey khusus libero yang kontras.
                    </p>

                    <div class="row g-3">
                        <!-- Jersey 1 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jersey Utama (Home / Jersey 1)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-shirt text-primary"></i></span>
                                <input type="text" name="warna_jersey_1" class="form-control" placeholder="Contoh: Biru Tua / Putih" value="{{ old('warna_jersey_1', $activeTeam->warna_jersey_1) }}">
                            </div>
                        </div>

                        <!-- Jersey 2 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jersey Kedua (Away / Jersey 2)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-shirt text-danger"></i></span>
                                <input type="text" name="warna_jersey_2" class="form-control" placeholder="Contoh: Merah / Hitam" value="{{ old('warna_jersey_2', $activeTeam->warna_jersey_2) }}">
                            </div>
                        </div>

                        <!-- Jersey 3 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jersey Ketiga (Alternatif / Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-shirt text-secondary"></i></span>
                                <input type="text" name="warna_jersey_3" class="form-control" placeholder="Contoh: Kuning / Hijau" value="{{ old('warna_jersey_3', $activeTeam->warna_jersey_3) }}">
                            </div>
                        </div>

                        <!-- Jersey Libero 1 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-warning-emphasis">Jersey Libero 1 (Wajib Kontras)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-vest text-warning"></i></span>
                                <input type="text" name="warna_jersey_libero_1" class="form-control" placeholder="Contoh: Hijau Stabilo / Hitam" value="{{ old('warna_jersey_libero_1', $activeTeam->warna_jersey_libero_1) }}">
                            </div>
                        </div>

                        <!-- Jersey Libero 2 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-warning-emphasis">Jersey Libero 2 (Alternatif)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-vest text-warning"></i></span>
                                <input type="text" name="warna_jersey_libero_2" class="form-control" placeholder="Contoh: Oranye / Putih" value="{{ old('warna_jersey_libero_2', $activeTeam->warna_jersey_libero_2) }}">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4 small d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fa-lg me-2"></i>
                        <div>Warna jersey libero harus memiliki kontras visual yang jelas dengan warna jersey pemain reguler tim Anda dan tim lawan saat technical meeting.</div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Profil & Jersey
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
