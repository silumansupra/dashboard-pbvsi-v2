@extends('layouts.app')

@section('title', 'Tambah Pemain Baru - PBVSI')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Pemain Baru</span></li>
        </ol>
    </nav>
    <a href="{{ route('personel.pemain') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pemain
    </a>
</div>

<form action="{{ route('personel.pemain.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- KOLOM UTAMA: IDENTITAS PEMAIN -->
        <div class="col-lg-8">
            <!-- KARTU 1: DATA PRIBADI & BIODATA -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-id-card text-primary me-2"></i> Biodata & Identitas Atlet
                    </h5>
                    <small class="text-muted">Masukkan data identitas resmi sesuai KTP / Kartu Keluarga / Akta Kelahiran.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Contoh: FARHAN HALIM" required autofocus>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Punggung Jersey</label>
                            <input type="text" name="nama_punggung" class="form-control text-uppercase @error('nama_punggung') is-invalid @enderror" value="{{ old('nama_punggung') }}" placeholder="Contoh: F. HALIM">
                            @error('nama_punggung')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="PA" {{ old('jenis_kelamin') === 'PA' ? 'selected' : '' }}>Putra (PA)</option>
                                <option value="PI" {{ old('jenis_kelamin') === 'PI' ? 'selected' : '' }}>Putri (PI)</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Kewarganegaraan <span class="text-danger">*</span></label>
                            <select name="status_kewarganegaraan" class="form-select @error('status_kewarganegaraan') is-invalid @enderror" required>
                                <option value="WNI_DAERAH" {{ old('status_kewarganegaraan') === 'WNI_DAERAH' ? 'selected' : '' }}>WNI Daerah</option>
                                <option value="WNI_NASIONAL" {{ old('status_kewarganegaraan') === 'WNI_NASIONAL' ? 'selected' : '' }}>WNI Nasional</option>
                                <option value="WNA_ASING" {{ old('status_kewarganegaraan') === 'WNA_ASING' ? 'selected' : '' }}>WNA Asing</option>
                            </select>
                            @error('status_kewarganegaraan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK (No. KTP / KIA)</label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="16 digit nomor induk kependudukan">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NISN (Nomor Induk Siswa Nasional)</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" placeholder="10 digit nomor NISN jika atlet pelajar">
                            @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control text-uppercase @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" placeholder="Contoh: BANDUNG">
                            @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
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
                    <small class="text-muted">Tentukan Pengurus Provinsi pembina dan klub tempat pemain terdaftar.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Provinsi (Pengprov)</label>
                            <select name="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror">
                                <option value="">-- Pilih Pengprov Asal --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}" {{ old('pengprov_id') == $p->id ? 'selected' : '' }}>
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
                                    <option value="{{ $k->id }}" {{ old('klub_id') == $k->id ? 'selected' : '' }}>
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
            <!-- KARTU FOTO PROFIL ATLET -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-camera text-primary me-2"></i> Foto Profil Atlet
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="fotoPreview" src="{{ asset('images/no-user.svg') }}" alt="Preview Foto Atlet" class="rounded-circle border border-3 border-light shadow-sm" style="width: 130px; height: 130px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/no-user.svg') }}';">
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-semibold small text-muted mb-1">Unggah Pas Foto (BunnyCDN)</label>
                        <input type="file" name="foto_file" id="fotoFileInput" class="form-control form-control-sm @error('foto_file') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                        @error('foto_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Format: JPG, PNG, WEBP. Maks 3MB.</small>
                    </div>
                </div>
            </div>

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
                                <option value="{{ $pos->kode_posisi }}" {{ old('posisi_utama') === $pos->kode_posisi ? 'selected' : '' }}>
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
                            <option value="PENDING" {{ old('status_verifikasi', 'PENDING') === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu Berkas)</option>
                            <option value="VERIFIED" {{ old('status_verifikasi') === 'VERIFIED' ? 'selected' : '' }}>VERIFIED (Sah Terverifikasi)</option>
                            <option value="REJECTED" {{ old('status_verifikasi') === 'REJECTED' ? 'selected' : '' }}>REJECTED (Ditolak)</option>
                        </select>
                        @error('status_verifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Contoh: Akta & ijazah asli sudah dicocokkan...">{{ old('catatan_verifikasi') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN & AKSI -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pemain Baru
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

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('fotoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
