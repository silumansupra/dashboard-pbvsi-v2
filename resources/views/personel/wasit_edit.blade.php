@extends('layouts.app')

@section('title', 'Edit Data Wasit: ' . ($wasit->user?->name ?? 'Wasit') . ' - PBVSI')

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
            <li class="breadcrumb-item"><a href="{{ route('personel.wasit') }}">Data Wasit</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-danger px-2 py-1">Edit Wasit</span></li>
        </ol>
    </nav>
    <a href="{{ route('personel.wasit') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Wasit
    </a>
</div>

<form action="{{ route('personel.wasit.update', $wasit->encrypted_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-flag text-danger me-2"></i> Perbarui Data & Lisensi Wasit
                    </h5>
                    <small class="text-muted">Lisensi: <span class="badge bg-danger-subtle text-danger border px-2 py-1">{{ $wasit->nomor_lisensi_wasit }}</span></small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap Wasit <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control text-uppercase @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $wasit->user?->name) }}" required autofocus>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $wasit->user?->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $wasit->user?->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Perwasitan <span class="text-danger">*</span></label>
                            <select name="kategori_wasit" class="form-select @error('kategori_wasit') is-invalid @enderror" required>
                                <option value="INDOOR" {{ old('kategori_wasit', $wasit->kategori_wasit) === 'INDOOR' ? 'selected' : '' }}>Bola Voli Indoor</option>
                                <option value="VOLI_PANTAI" {{ old('kategori_wasit', $wasit->kategori_wasit) === 'VOLI_PANTAI' ? 'selected' : '' }}>Bola Voli Pantai</option>
                            </select>
                            @error('kategori_wasit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Lisensi Wasit <span class="text-danger">*</span></label>
                            <input type="text" name="tingkat_wasit" class="form-control text-uppercase @error('tingkat_wasit') is-invalid @enderror" value="{{ old('tingkat_wasit', $wasit->tingkat_wasit) }}" required>
                            @error('tingkat_wasit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Lisensi Wasit <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_lisensi_wasit" class="form-control text-uppercase @error('nomor_lisensi_wasit') is-invalid @enderror" value="{{ old('nomor_lisensi_wasit', $wasit->nomor_lisensi_wasit) }}" required>
                            @error('nomor_lisensi_wasit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asal Pengurus Provinsi (Pengprov)</label>
                            <select name="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror">
                                <option value="">-- Korps Wasit Pusat PBVSI --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}" {{ old('pengprov_id', $wasit->pengprov_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Khusus Perwasitan</label>
                            <textarea name="catatan_perwasitan" class="form-control" rows="2">{{ old('catatan_perwasitan', $wasit->catatan_perwasitan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-toggle-on text-danger me-2"></i> Status Wasit
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Korps <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', $wasit->status_aktif ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif Ditugaskan</option>
                            <option value="0" {{ old('status_aktif', $wasit->status_aktif ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Pensiun</option>
                        </select>
                        @error('status_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Wasit
                        </button>
                        <a href="{{ route('personel.wasit') }}" class="btn btn-outline-secondary py-2">
                            <i class="fa-solid fa-xmark me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
