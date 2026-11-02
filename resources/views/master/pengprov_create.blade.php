@extends('layouts.app')

@section('title', 'Tambah Pengprov PBVSI Baru')

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
            <li class="breadcrumb-item"><a href="{{ route('master.pengprov') }}">Master Pengprov</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Pengprov Baru</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.pengprov') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pengprov
    </a>
</div>

<form action="{{ route('master.pengprov.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-landmark text-primary me-2"></i> Data Pokok Pengprov PBVSI
                    </h5>
                    <small class="text-muted">Lengkapi identitas organisasi dan wilayah pengurus provinsi.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Wilayah Provinsi Induk <span class="text-danger">*</span></label>
                            <select name="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" required autofocus>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}" {{ old('provinsi_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_provinsi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provinsi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Resmi Pengprov <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengprov" class="form-control text-uppercase @error('nama_pengprov') is-invalid @enderror" value="{{ old('nama_pengprov') }}" placeholder="Contoh: PENGPROV PBVSI JAWA TIMUR" required>
                            @error('nama_pengprov')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ketua Umum (Ketum)</label>
                            <input type="text" name="ketum" class="form-control @error('ketum') is-invalid @enderror" value="{{ old('ketum') }}" placeholder="Nama Ketua Umum">
                            @error('ketum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Ketum</label>
                            <input type="text" name="nohp_ketum" class="form-control @error('nohp_ketum') is-invalid @enderror" value="{{ old('nohp_ketum') }}" placeholder="08xxxxxxxxxx">
                            @error('nohp_ketum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Umum (Sekum)</label>
                            <input type="text" name="sekum" class="form-control @error('sekum') is-invalid @enderror" value="{{ old('sekum') }}" placeholder="Nama Sekretaris Umum">
                            @error('sekum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Sekum</label>
                            <input type="text" name="nohp_sekum" class="form-control @error('nohp_sekum') is-invalid @enderror" value="{{ old('nohp_sekum') }}" placeholder="08xxxxxxxxxx">
                            @error('nohp_sekum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Resmi</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="pengprov@pbvsi.or.id">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Telepon Kantor</label>
                            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" placeholder="(021) xxxxxxxx">
                            @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Website / Portal Resmi</label>
                            <input type="text" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://pbvsijatim.or.id">
                            @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Sekretariat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Alamat lengkap kantor sekretariat Pengprov...">{{ old('alamat') }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-toggle-on text-primary me-2"></i> Status Keaktifan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pengprov <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif (SK Berlaku)</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif / Demisioner</option>
                        </select>
                        @error('status_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <small class="text-muted d-block mb-1"><i class="fa-solid fa-circle-info text-primary me-1"></i>Catatan Sistem</small>
                        <small class="text-secondary">Pengprov yang aktif dapat mendaftarkan tim kontingen ke kejurnas dan membina pengurus pengkot/pengkab di wilayahnya.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data Pengprov
                        </button>
                        <a href="{{ route('master.pengprov') }}" class="btn btn-outline-secondary py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
