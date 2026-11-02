@extends('layouts.app')

@section('title', 'Edit Pengkot / Pengkab - ' . $pengkot->nama_pengkot)

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
            <li class="breadcrumb-item"><a href="{{ route('master.pengkot') }}">Master Pengkot / Pengkab</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Edit Pengkot</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.pengkot') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pengkot
    </a>
</div>

<form action="{{ route('master.pengkot.update', $pengkot->encrypted_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-city text-primary me-2"></i> Edit Data Pengurus Kota / Kabupaten
                    </h5>
                    <small class="text-muted">Perbarui relasi wilayah dan kontak kepengurusan.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengurus Provinsi Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" id="pengprov_id" class="form-select @error('pengprov_id') is-invalid @enderror" required autofocus>
                                <option value="">-- Pilih Pengprov --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}" {{ old('pengprov_id', $pengkot->pengprov_id) == $pp->id ? 'selected' : '' }}>
                                        {{ $pp->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pengprov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Wilayah Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten_id" id="kabupaten_id" class="form-select @error('kabupaten_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kota / Kabupaten --</option>
                                @foreach($kabupatenList as $kab)
                                    <option value="{{ $kab->id }}" {{ old('kabupaten_id', $pengkot->kabupaten_id) == $kab->id ? 'selected' : '' }}>
                                        {{ $kab->tipe ? $kab->tipe . ' ' : '' }}{{ $kab->nama_kabupaten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kabupaten_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Resmi Pengkot / Pengkab <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengkot" id="nama_pengkot" class="form-control text-uppercase @error('nama_pengkot') is-invalid @enderror" value="{{ old('nama_pengkot', $pengkot->nama_pengkot) }}" required>
                            @error('nama_pengkot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ketua Umum</label>
                            <input type="text" name="ketum" class="form-control @error('ketum') is-invalid @enderror" value="{{ old('ketum', $pengkot->ketum) }}" placeholder="Nama Ketua Umum">
                            @error('ketum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Ketum</label>
                            <input type="text" name="nohp_ketum" class="form-control @error('nohp_ketum') is-invalid @enderror" value="{{ old('nohp_ketum', $pengkot->nohp_ketum) }}" placeholder="08xxxxxxxxxx">
                            @error('nohp_ketum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Umum</label>
                            <input type="text" name="sekum" class="form-control @error('sekum') is-invalid @enderror" value="{{ old('sekum', $pengkot->sekum) }}" placeholder="Nama Sekretaris Umum">
                            @error('sekum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Sekum</label>
                            <input type="text" name="nohp_sekum" class="form-control @error('nohp_sekum') is-invalid @enderror" value="{{ old('nohp_sekum', $pengkot->nohp_sekum) }}" placeholder="08xxxxxxxxxx">
                            @error('nohp_sekum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Resmi</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pengkot->email) }}" placeholder="pengkot@pbvsi.or.id">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon Kantor</label>
                            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $pengkot->no_telp) }}" placeholder="(031) xxxxxxxx">
                            @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Kantor / Sekretariat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Alamat kantor sekretariat...">{{ old('alamat', $pengkot->alamat) }}</textarea>
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
                        <label class="form-label fw-semibold">Status Pengkot / Pengkab <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', $pengkot->status_aktif ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif (SK Berlaku)</option>
                            <option value="0" {{ old('status_aktif', $pengkot->status_aktif ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Demisioner</option>
                        </select>
                        @error('status_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Pengkot
                        </button>
                        <a href="{{ route('master.pengkot') }}" class="btn btn-outline-secondary py-2">
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
    const kabSelect = document.getElementById('kabupaten_id');

    pengprovSelect.addEventListener('change', function() {
        const pengprovId = this.value;
        if (!pengprovId) {
            kabSelect.innerHTML = '<option value="">-- Pilih Pengprov terlebih dahulu --</option>';
            return;
        }

        kabSelect.innerHTML = '<option value="">Memuat data kabupaten...</option>';
        fetch("{{ url('master/api/kabupaten-by-pengprov') }}/" + pengprovId)
            .then(res => res.json())
            .then(data => {
                kabSelect.innerHTML = '<option value="">-- Pilih Kota / Kabupaten --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = (item.tipe ? item.tipe + ' ' : '') + item.nama_kabupaten;
                    kabSelect.appendChild(opt);
                });
            })
            .catch(err => {
                console.error(err);
                kabSelect.innerHTML = '<option value="">Gagal memuat kabupaten</option>';
            });
    });
});
</script>
@endpush
@endsection
