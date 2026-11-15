@extends('layouts.app')

@section('title', 'Atur Pul & Partisipasi: ' . $team->nama_tim . ' - ' . $edisi->nama_edisi)

@section('content')
<!-- NOTIFIKASI ERROR -->
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

<!-- BREADCRUMB -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}">
                    <i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}">
                    {{ $turnamen->nama_turnamen }} ({{ $tahun }})
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kejuaraan.tim.show', ['slug' => $slug, 'tahun' => $tahun, 'id' => $team->encrypted_id]) }}">
                    {{ $team->nama_tim }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <span class="badge bg-primary px-2 py-1">Atur Pul & Partisipasi</span>
            </li>
        </ol>
    </nav>

    <div class="d-flex gap-2">
        <a href="{{ route('kejuaraan.tim.show', ['slug' => $slug, 'tahun' => $tahun, 'id' => $team->encrypted_id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm bg-white">
            <i class="fa-solid fa-eye me-1"></i> Lihat Detail & Roster
        </a>
        <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Tim
        </a>
    </div>
</div>

<!-- FORM ATUR PUL & PARTISIPASI TIM -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-shuffle text-primary me-2"></i> Atur Pul & Status Kepesertaan Turnamen
                        </h5>
                        <p class="small text-muted mb-0">Tim: <strong>{{ $team->nama_tim }}</strong> &bull; Edisi: {{ $edisi->nama_edisi }}</p>
                    </div>
                    <span class="badge {{ $team->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                        {{ $team->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}
                    </span>
                </div>
            </div>

            <form action="{{ route('kejuaraan.tim.atur_pul', ['slug' => $slug, 'tahun' => $tahun, 'id' => $team->encrypted_id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body p-4">
                    <!-- INFORMASI INDUK ORGANISASI (READ ONLY) -->
                    <div class="alert alert-light border shadow-sm mb-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($team->logo_url)
                                <div class="rounded border p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <img src="{{ $team->logo_url }}" alt="{{ $team->nama_tim }}" class="img-fluid object-fit-contain" style="max-height: 44px; max-width: 44px;">
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold fs-6 text-dark">{{ $team->nama_tim }}</div>
                                <div class="small text-muted">
                                    @if($team->pengprov)
                                        <i class="fa-solid fa-flag me-1 text-primary"></i> Kontingen: {{ $team->pengprov->nama_pengprov }}
                                    @elseif($team->klub)
                                        <i class="fa-solid fa-shield-halved me-1 text-success"></i> Master Klub: {{ $team->klub->nama_klub }} ({{ $team->klub->kode_klub ?: '-' }})
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PENGATURAN TEKNIS TURNAMEN -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Gender Pertandingan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="kategori_gender" id="genderPA" value="PA" {{ old('kategori_gender', $team->kategori_gender) === 'PA' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-bold text-info" for="genderPA">
                                        <i class="fa-solid fa-mars me-1"></i> Putra (PA)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="kategori_gender" id="genderPI" value="PI" {{ old('kategori_gender', $team->kategori_gender) === 'PI' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="genderPI">
                                        <i class="fa-solid fa-venus me-1"></i> Putri (PI)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penempatan Pul / Pool Grup</label>
                            <select name="pool_grup" class="form-select">
                                <option value="">-- Belum Diundi / Bebas --</option>
                                @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $pul)
                                    <option value="{{ $pul }}" {{ old('pool_grup', $team->pool_grup) === $pul ? 'selected' : '' }}>Pul {{ $pul }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Penyesuaian Nama Tampil Tim</label>
                            <input type="text" name="nama_tim_custom" class="form-control" value="{{ old('nama_tim_custom', $team->nama_tim) }}">
                            <small class="text-muted">Nama yang akan dimunculkan di bagan turnamen, jadwal, dan e-Score.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Verifikasi Turnamen</label>
                            <select name="status_verifikasi" class="form-select">
                                <option value="VERIFIED" {{ old('status_verifikasi', $team->status_verifikasi) === 'VERIFIED' ? 'selected' : '' }}>VERIFIED (Disetujui PBVSI)</option>
                                <option value="SUBMITTED" {{ old('status_verifikasi', $team->status_verifikasi) === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED (Diajukan)</option>
                                <option value="DRAFT" {{ old('status_verifikasi', $team->status_verifikasi) === 'DRAFT' ? 'selected' : '' }}>DRAFT (Tahap Input)</option>
                                <option value="REJECTED" {{ old('status_verifikasi', $team->status_verifikasi) === 'REJECTED' ? 'selected' : '' }}>REJECTED (Perlu Revisi)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch pt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $team->is_active) ? 'checked' : '' }} style="cursor: pointer; width: 2.2em; height: 1.2em;">
                                <label class="form-check-label fw-semibold ms-2" for="is_active">
                                    Status Keikutsertaan Tim Aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary border-0 bg-light small mb-0">
                        <i class="fa-solid fa-shield-halved text-primary me-1"></i>
                        <strong>Catatan Tata Kelola PBVSI:</strong> Perubahan data legalitas klub (seperti nama legal, alamat, SK, logo induk) dilakukan melalui menu Master Data PBVSI oleh Superadmin. Sedangkan pengisian seragam jersey, official, dan roster pemain dilakukan secara mandiri oleh Admin Tim di portal tim.
                    </div>
                </div>

                <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
