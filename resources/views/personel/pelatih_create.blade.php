@extends('layouts.app')

@section('title', 'Tambah Pelatih Baru - PBVSI')

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
            <li class="breadcrumb-item"><a href="{{ route('personel.pelatih') }}">Data Pelatih</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Pelatih Baru</span></li>
        </ol>
    </nav>
    <a href="{{ route('personel.pelatih') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pelatih
    </a>
</div>

<form action="{{ route('personel.pelatih.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-chalkboard-user text-primary me-2"></i> Identitas & Lisensi Kepelatihan
                    </h5>
                    <small class="text-muted">Masukkan data resmi pelatih berlisensi PBVSI / FIVB.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap Pelatih <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control text-uppercase @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Contoh: LI QIANG atau SAMSUL JAIS" required autofocus>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="pelatih@pbvsi.or.id">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Lisensi <span class="text-danger">*</span></label>
                            <input type="text" name="tingkat_pelatih" class="form-control text-uppercase @error('tingkat_pelatih') is-invalid @enderror" value="{{ old('tingkat_pelatih') }}" placeholder="Contoh: NASIONAL A / FIVB LEVEL 1" required>
                            @error('tingkat_pelatih')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Lisensi Pelatih</label>
                            <input type="text" name="nomor_lisensi_pelatih" class="form-control text-uppercase @error('nomor_lisensi_pelatih') is-invalid @enderror" value="{{ old('nomor_lisensi_pelatih') }}" placeholder="Auto-generate jika dikosongkan">
                            @error('nomor_lisensi_pelatih')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Asal Pengurus Provinsi (Pengprov)</label>
                            <select name="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror">
                                <option value="">-- Pengurus Pusat PBVSI (Nasional) --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}" {{ old('pengprov_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- KARTU FOTO PROFIL PELATIH -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-camera text-primary me-2"></i> Foto Profil Pelatih
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="fotoPreview" src="{{ asset('images/no-user.svg') }}" alt="Preview Foto Pelatih" class="rounded-circle border border-3 border-light shadow-sm" style="width: 130px; height: 130px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/no-user.svg') }}';">
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-semibold small text-muted mb-1">Unggah Pas Foto (BunnyCDN)</label>
                        <input type="file" name="foto_file" id="fotoFileInput" class="form-control form-control-sm @error('foto_file') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                        @error('foto_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Format: JPG, PNG, WEBP. Maks 3MB.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-toggle-on text-primary me-2"></i> Status Keanggotaan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pelatih <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pelatih Baru
                        </button>
                        <a href="{{ route('personel.pelatih') }}" class="btn btn-outline-secondary py-2">
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
