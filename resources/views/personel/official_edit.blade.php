@extends('layouts.app')

@section('title', 'Edit Official: ' . $official->nama_lengkap . ' - PBVSI')

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
            <li class="breadcrumb-item"><a href="{{ route('personel.official') }}">Data Official</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Edit Official</span></li>
        </ol>
    </nav>
    <a href="{{ route('personel.official') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Official
    </a>
</div>

<form action="{{ route('personel.official.update', $official->encrypted_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-tie text-info me-2"></i> Perbarui Data Official Tim
                    </h5>
                    <small class="text-muted">Lisensi: <span class="badge bg-info-subtle text-info border px-2 py-1">{{ $official->nomor_lisensi }}</span></small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Official <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control text-uppercase @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $official->nama_lengkap) }}" required autofocus>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="L" {{ old('jenis_kelamin', $official->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="P" {{ old('jenis_kelamin', $official->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $official->nik) }}">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan Penugasan <span class="text-danger">*</span></label>
                            <select name="jabatan_official" class="form-select @error('jabatan_official') is-invalid @enderror" required>
                                <option value="MANAGER" {{ old('jabatan_official', $official->jabatan_official) === 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                                <option value="ASISTEN MANAGER" {{ old('jabatan_official', $official->jabatan_official) === 'ASISTEN MANAGER' ? 'selected' : '' }}>ASISTEN MANAGER</option>
                                <option value="DOKTER TIM" {{ old('jabatan_official', $official->jabatan_official) === 'DOKTER TIM' ? 'selected' : '' }}>DOKTER TIM</option>
                                <option value="PHYSIOTHERAPIST" {{ old('jabatan_official', $official->jabatan_official) === 'PHYSIOTHERAPIST' ? 'selected' : '' }}>PHYSIOTHERAPIST</option>
                                <option value="STATISTICIAN" {{ old('jabatan_official', $official->jabatan_official) === 'STATISTICIAN' ? 'selected' : '' }}>STATISTICIAN</option>
                                <option value="PELATIH FISIK" {{ old('jabatan_official', $official->jabatan_official) === 'PELATIH FISIK' ? 'selected' : '' }}>PELATIH FISIK</option>
                                <option value="MASSEUR" {{ old('jabatan_official', $official->jabatan_official) === 'MASSEUR' ? 'selected' : '' }}>MASSEUR</option>
                                <option value="OFFICIAL UMUM" {{ old('jabatan_official', $official->jabatan_official) === 'OFFICIAL UMUM' ? 'selected' : '' }}>OFFICIAL UMUM</option>
                            </select>
                            @error('jabatan_official')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $official->user?->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $official->user?->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nomor Lisensi / STR Medis</label>
                            <input type="text" name="nomor_lisensi" class="form-control text-uppercase @error('nomor_lisensi') is-invalid @enderror" value="{{ old('nomor_lisensi', $official->nomor_lisensi) }}">
                            @error('nomor_lisensi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-building-flag text-primary me-2"></i> Afiliasi & Penempatan Tim
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Provinsi (Pengprov)</label>
                            <select name="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror">
                                <option value="">-- Tanpa Pengprov / Nasional --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}" {{ old('pengprov_id', $official->pengprov_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Klub Terdaftar</label>
                            <select name="klub_id" class="form-select @error('klub_id') is-invalid @enderror">
                                <option value="">-- Tanpa Klub (Bebas / Independen) --</option>
                                @foreach($klubList as $k)
                                    <option value="{{ $k->id }}" {{ old('klub_id', $official->klub_id) == $k->id ? 'selected' : '' }}>
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

        <div class="col-lg-4">
            <!-- KARTU FOTO PROFIL OFFICIAL -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-camera text-primary me-2"></i> Foto Profil Official
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="fotoPreview" src="{{ $official->foto_url }}" alt="{{ $official->nama_lengkap }}" class="rounded-circle border border-3 border-light shadow-sm" style="width: 130px; height: 130px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/no-user.svg') }}';">
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-semibold small text-muted mb-1">Unggah / Ganti Foto (BunnyCDN)</label>
                        <input type="file" name="foto_file" id="fotoFileInput" class="form-control form-control-sm @error('foto_file') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                        @error('foto_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Format: JPG, PNG, WEBP. Maks 3MB.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-toggle-on text-info me-2"></i> Status Official
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Keaktifan <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', $official->status_aktif ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif', $official->status_aktif ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Official
                        </button>
                        <a href="{{ route('personel.official') }}" class="btn btn-outline-secondary py-2">
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
