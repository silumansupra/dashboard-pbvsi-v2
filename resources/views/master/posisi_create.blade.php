@extends('layouts.app')

@section('title', 'Tambah Posisi Pemain Baru')

@section('content')
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-danger"></i>
        <strong>Terdapat kesalahan pengisian formulir:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.posisi') }}">Master Posisi Pemain</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Posisi</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.posisi') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Posisi
    </a>
</div>

<form action="{{ route('master.posisi.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-volleyball text-primary me-2"></i> Data Master Posisi Pemain
                    </h5>
                    <small class="text-muted">Masukkan kode singkatan dan nama resmi posisi bermain Bolavoli.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode Posisi <span class="text-danger">*</span></label>
                            <input type="text" name="kode_posisi" class="form-control text-uppercase @error('kode_posisi') is-invalid @enderror" value="{{ old('kode_posisi') }}" placeholder="Contoh: OH, S, MB, OP, L" required autofocus>
                            @error('kode_posisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Posisi Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_posisi" class="form-control @error('nama_posisi') is-invalid @enderror" value="{{ old('nama_posisi') }}" placeholder="Contoh: Outside Hitter / Open Spiker" required>
                            @error('nama_posisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi / Peran Lapangan</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Deskripsi peran dan tugas posisi di lapangan...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-toggle-on text-primary me-2"></i> Status
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Posisi <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif Digunakan</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Posisi
                        </button>
                        <a href="{{ route('master.posisi') }}" class="btn btn-outline-secondary py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
