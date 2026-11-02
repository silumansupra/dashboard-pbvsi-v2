@extends('layouts.app')

@section('title', 'Edit Pemain: ' . $pemain->nama_lengkap . ' - PBVSI')

@section('content')
<!-- NOTIFIKASI ERROR VALIDASI -->
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

<!-- BREADCRUMB & HEADER -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('personel.pemain') }}">Bank Pemain</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Edit Pemain</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <a href="{{ route('personel.pemain') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<form action="{{ route('personel.pemain.update', $pemain->encrypted_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- KOLOM UTAMA: IDENTITAS PEMAIN -->
        <div class="col-lg-8">
            <!-- KARTU 1: DATA PRIBADI & BIODATA -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-0">
                            <i class="fa-solid fa-user-pen text-primary me-2"></i> Perbarui Biodata Atlet
                        </h5>
                        <small class="text-muted">ID PBVSI: <span class="badge bg-primary-subtle text-primary border px-2 py-1">{{ $pemain->pbvsi_player_id }}</span></small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $pemain->nama_lengkap) }}" required autofocus>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Punggung Jersey</label>
                            <input type="text" name="nama_punggung" class="form-control text-uppercase @error('nama_punggung') is-invalid @enderror" value="{{ old('nama_punggung', $pemain->nama_punggung) }}">
                            @error('nama_punggung')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="PA" {{ old('jenis_kelamin', $pemain->jenis_kelamin) === 'PA' ? 'selected' : '' }}>Putra (PA)</option>
                                <option value="PI" {{ old('jenis_kelamin', $pemain->jenis_kelamin) === 'PI' ? 'selected' : '' }}>Putri (PI)</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Kewarganegaraan <span class="text-danger">*</span></label>
                            <select name="status_kewarganegaraan" class="form-select @error('status_kewarganegaraan') is-invalid @enderror" required>
                                <option value="WNI_DAERAH" {{ old('status_kewarganegaraan', $pemain->status_kewarganegaraan) === 'WNI_DAERAH' ? 'selected' : '' }}>WNI Daerah</option>
                                <option value="WNI_NASIONAL" {{ old('status_kewarganegaraan', $pemain->status_kewarganegaraan) === 'WNI_NASIONAL' ? 'selected' : '' }}>WNI Nasional</option>
                                <option value="WNA_ASING" {{ old('status_kewarganegaraan', $pemain->status_kewarganegaraan) === 'WNA_ASING' ? 'selected' : '' }}>WNA Asing</option>
                            </select>
                            @error('status_kewarganegaraan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK (No. KTP / KIA)</label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $pemain->nik) }}">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NISN (Nomor Induk Siswa Nasional)</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $pemain->nisn) }}">
                            @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control text-uppercase @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $pemain->tempat_lahir) }}">
                            @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $pemain->tanggal_lahir?->format('Y-m-d')) }}">
                            @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- KARTU 2: ASAL AFILIASI ORGANISASI & KLUB -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-shield-halved text-primary me-2"></i> Afiliasi Klub & Asal Daerah
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Provinsi (Pengprov)</label>
                            <select name="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror">
                                <option value="">-- Pilih Pengprov Asal --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}" {{ old('pengprov_id', $pemain->pengprov_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Klub Terdaftar</label>
                            <select name="klub_id" class="form-select @error('klub_id') is-invalid @enderror">
                                <option value="">-- Tanpa Klub (Bebas Transfer / Mandiri) --</option>
                                @foreach($klubList as $k)
                                    <option value="{{ $k->id }}" {{ old('klub_id', $pemain->klub_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_klub }}
                                    </option>
                                @endforeach
                            </select>
                            @error('klub_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: SPESIFIKASI ATLET & VERIFIKASI -->
        <div class="col-lg-4">
            <!-- KARTU SPESIFIKASI ATLET -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-volleyball text-primary me-2"></i> Peran & Posisi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Posisi Utama</label>
                        <select name="posisi_utama" class="form-select @error('posisi_utama') is-invalid @enderror">
                            <option value="">-- Pilih Posisi Lapangan --</option>
                            @foreach($posisiList as $pos)
                                <option value="{{ $pos->kode_posisi }}" {{ old('posisi_utama', $pemain->posisi_utama) === $pos->kode_posisi ? 'selected' : '' }}>
                                    {{ $pos->kode_posisi }} - {{ $pos->nama_posisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('posisi_utama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- KARTU STATUS & KEABSAHAN -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-certificate text-primary me-2"></i> Status Keabsahan Dokumen
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                        <select name="status_verifikasi" class="form-select @error('status_verifikasi') is-invalid @enderror" required>
                            <option value="PENDING" {{ old('status_verifikasi', $pemain->status_verifikasi) === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu Berkas)</option>
                            <option value="VERIFIED" {{ old('status_verifikasi', $pemain->status_verifikasi) === 'VERIFIED' ? 'selected' : '' }}>VERIFIED (Sah Terverifikasi)</option>
                            <option value="REJECTED" {{ old('status_verifikasi', $pemain->status_verifikasi) === 'REJECTED' ? 'selected' : '' }}>REJECTED (Ditolak)</option>
                        </select>
                        @error('status_verifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3">{{ old('catatan_verifikasi', $pemain->catatan_verifikasi) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN & AKSI -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Pemain
                        </button>
                        <a href="{{ route('personel.pemain') }}" class="btn btn-outline-secondary py-2">
                            <i class="fa-solid fa-xmark me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
