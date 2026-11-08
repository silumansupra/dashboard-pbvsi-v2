@extends('layouts.app')

@section('title', 'Edit Data Klub - ' . $klub->nama_klub)

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
            <li class="breadcrumb-item"><a href="{{ route('master.klub') }}">Master Klub / Tim</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Edit Klub</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.klub') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Data Klub
    </a>
</div>

<form action="{{ route('master.klub.update', $klub->encrypted_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-shield-halved text-primary me-2"></i> Identitas Klub / Tim
                    </h5>
                    <small class="text-muted">Perbarui data klub, pengprov induk, dan pengkot binaan.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Nama Klub / Tim <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klub" class="form-control text-uppercase @error('nama_klub') is-invalid @enderror" value="{{ old('nama_klub', $klub->nama_klub) }}" required autofocus>
                            @error('nama_klub')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Kode Unik Tim <span class="text-danger">*</span></label>
                            <input type="text" name="kode_klub" class="form-control text-uppercase @error('kode_klub') is-invalid @enderror" value="{{ old('kode_klub', $klub->kode_klub) }}" required>
                            @error('kode_klub')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Provinsi Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" id="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pengprov Asal --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}" {{ old('pengprov_id', $klub->pengprov_id) == $pp->id ? 'selected' : '' }}>
                                        {{ $pp->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Pengkot / Pengkab</label>
                            <select name="pengkot_id" id="pengkot_id" class="form-select @error('pengkot_id') is-invalid @enderror">
                                <option value="">-- Pengkot/Pengkab belum ditentukan --</option>
                                @foreach($pengkotList as $pk)
                                    <option value="{{ $pk->id }}" {{ old('pengkot_id', $klub->pengkot_id) == $pk->id ? 'selected' : '' }}>
                                        {{ $pk->nama_pengkot }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengkot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pemilik / Manajer Klub</label>
                            <input type="text" name="pemilik_klub" class="form-control @error('pemilik_klub') is-invalid @enderror" value="{{ old('pemilik_klub', $klub->pemilik_klub) }}" placeholder="Nama pemilik / ketua klub">
                            @error('pemilik_klub')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Tim</label>
                            <input type="text" name="sekretaris_klub" class="form-control @error('sekretaris_klub') is-invalid @enderror" value="{{ old('sekretaris_klub', $klub->sekretaris_klub) }}" placeholder="Nama sekretaris tim">
                            @error('sekretaris_klub')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Basecamp / GOR Klub</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Alamat lengkap basecamp latihan...">{{ old('alamat', $klub->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- LOGO KLUB (BUNNYCDN) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i> Logo Klub (BunnyCDN)
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="border rounded-4 p-2 bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px;">
                            @if($klub->logo_url)
                                <img id="logo_preview" src="{{ $klub->logo_url }}" alt="Logo {{ $klub->nama_klub }}" class="img-fluid object-fit-contain" style="max-height: 104px; max-width: 104px;" onerror="this.onerror=null; this.src='{{ asset('images/no-logo.svg') }}';">
                                <i id="logo_placeholder" class="fa-solid fa-shield-halved text-secondary opacity-50 fa-4x d-none"></i>
                            @else
                                <img id="logo_preview" src="" alt="Preview Logo" class="img-fluid object-fit-contain d-none" style="max-height: 104px; max-width: 104px;">
                                <i id="logo_placeholder" class="fa-solid fa-shield-halved text-secondary opacity-50 fa-4x"></i>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Ganti File Logo (BunnyCDN)</label>
                        <input type="file" name="logo_file" id="logo_file" class="form-control form-control-sm @error('logo_file') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                        @error('logo_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            Kosongkan jika tidak ingin mengubah logo. Format: PNG, JPG, WEBP. Maks 3 MB.
                        </small>
                    </div>
                </div>
            </div>

            <!-- STATUS & SUBMIT -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-clipboard-check text-primary me-2"></i> Status Verifikasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Tim <span class="text-danger">*</span></label>
                        <select name="status_verifikasi" class="form-select @error('status_verifikasi') is-invalid @enderror" required>
                            <option value="VERIFIED" {{ old('status_verifikasi', $klub->status_verifikasi) == 'VERIFIED' ? 'selected' : '' }}>VERIFIED (Terverifikasi)</option>
                            <option value="PENDING" {{ old('status_verifikasi', $klub->status_verifikasi) == 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu)</option>
                            <option value="REJECTED" {{ old('status_verifikasi', $klub->status_verifikasi) == 'REJECTED' ? 'selected' : '' }}>REJECTED (Ditolak)</option>
                        </select>
                        @error('status_verifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Klub
                        </button>
                        <a href="{{ route('master.klub') }}" class="btn btn-outline-secondary py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pengprovSelect = document.getElementById('pengprov_id');
    const pengkotSelect = document.getElementById('pengkot_id');
    const logoInput = document.getElementById('logo_file');
    const logoPreview = document.getElementById('logo_preview');
    const logoPlaceholder = document.getElementById('logo_placeholder');

    pengprovSelect.addEventListener('change', function() {
        const pengprovId = this.value;
        if (!pengprovId) {
            pengkotSelect.innerHTML = '<option value="">-- Pengkot/Pengkab belum ditentukan --</option>';
            return;
        }

        pengkotSelect.innerHTML = '<option value="">Memuat data pengkot...</option>';
        fetch("{{ url('master/api/pengkot-by-pengprov') }}/" + pengprovId)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Pengkot/Pengkab belum ditentukan --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.nama_pengkot}</option>`;
                });
                pengkotSelect.innerHTML = options;
            })
            .catch(() => {
                pengkotSelect.innerHTML = '<option value="">Gagal memuat pengkot</option>';
            });
    });

    logoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('d-none');
                logoPlaceholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection
