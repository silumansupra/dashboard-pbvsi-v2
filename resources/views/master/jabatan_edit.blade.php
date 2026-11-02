@extends('layouts.app')

@section('title', 'Edit Jabatan - ' . $jabatan->nama_jabatan)

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
            <li class="breadcrumb-item"><a href="{{ route('master.jabatan') }}">Master Jabatan</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Edit Jabatan</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.jabatan') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Jabatan
    </a>
</div>

<form action="{{ route('master.jabatan.update', $jabatan->encrypted_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-tag text-primary me-2"></i> Edit Data Master Jabatan
                    </h5>
                    <small class="text-muted">Perbarui nama jabatan dan kategori peruntukannya.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" class="form-control text-uppercase @error('nama_jabatan') is-invalid @enderror" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required autofocus>
                            @error('nama_jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Peruntukan <span class="text-danger">*</span></label>
                            <select name="is_official_tim" class="form-select @error('is_official_tim') is-invalid @enderror" required>
                                <option value="1" {{ old('is_official_tim', $jabatan->is_official_tim ? '1' : '0') == '1' ? 'selected' : '' }}>Official Tim / Roster Pertandingan</option>
                                <option value="0" {{ old('is_official_tim', $jabatan->is_official_tim ? '1' : '0') == '0' ? 'selected' : '' }}>Pengurus Organisasi PBVSI</option>
                            </select>
                            @error('is_official_tim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Keaktifan <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', $jabatan->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif Digunakan</option>
                                <option value="0" {{ old('is_active', $jabatan->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-floppy-disk text-primary me-2"></i> Perbarui
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Jabatan
                        </button>
                        <a href="{{ route('master.jabatan') }}" class="btn btn-outline-secondary py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
