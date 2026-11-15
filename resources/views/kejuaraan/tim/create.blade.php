@extends('layouts.app')

@section('title', 'Assign Tim Peserta - ' . $edisi->nama_edisi)

@section('content')
<!-- NOTIFIKASI ERROR -->
@if(isset($errors) && $errors->any())
<div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-warning"></i>
        <strong>Terdapat kesalahan validasi input:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- BREADCRUMB -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}">
                    <i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}">
                    {{ $turnamen->nama_turnamen }} ({{ $tahun }})
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <span class="badge bg-primary px-2 py-1">Assign Tim Peserta</span>
            </li>
        </ol>
    </nav>

    <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm bg-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Tim
    </a>
</div>

<!-- FORM ASSIGN TIM PESERTA -->
<div class="row justify-content-center" x-data="{ tipeAsal: 'pengprov' }">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-user-plus text-primary me-2"></i> Assign Tim ke Edisi Turnamen
                        </h5>
                        <p class="small text-muted mb-0">Edisi: <strong>{{ $edisi->nama_edisi }}</strong> (Tahun {{ $tahun }})</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border px-3 py-2">
                        <i class="fa-solid fa-trophy me-1"></i> {{ $turnamen->nama_turnamen }}
                    </span>
                </div>
            </div>

            <form action="{{ route('kejuaraan.tim.assign', ['slug' => $slug, 'tahun' => $tahun]) }}" method="POST">
                @csrf
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4 small">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Pilih tim dari database resmi <strong>Master PBVSI</strong>. Data identitas dan legalitas klub dikelola di Master Data PBVSI, sedangkan roster atlet dan official diatur secara mandiri oleh Admin Tim masing-masing di portal tim.
                    </div>

                    <!-- PILIH JENIS ASAL: PENGPROV VS KLUB -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jenis Asal Tim Peserta <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline border rounded p-3 flex-fill bg-light">
                                <input class="form-check-input" type="radio" name="tipe_asal" id="tipePengprovPage" value="pengprov" x-model="tipeAsal" checked>
                                <label class="form-check-label fw-bold text-dark" for="tipePengprovPage">
                                    <i class="fa-solid fa-flag text-primary me-1"></i> Kontingen Daerah (Pengprov)
                                </label>
                                <div class="small text-muted ps-4">Untuk Kejuaraan Antar Daerah (Kejurnas Junior/Senior).</div>
                            </div>
                            <div class="form-check form-check-inline border rounded p-3 flex-fill bg-light">
                                <input class="form-check-input" type="radio" name="tipe_asal" id="tipeKlubPage" value="klub" x-model="tipeAsal">
                                <label class="form-check-label fw-bold text-dark" for="tipeKlubPage">
                                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Klub Bolavoli PBVSI
                                </label>
                                <div class="small text-muted ps-4">Untuk Livoli / Kejuaraan Antar Klub PBVSI.</div>
                            </div>
                        </div>
                    </div>

                    <!-- DROPDOWN PENGPROV -->
                    <div class="mb-3" x-show="tipeAsal === 'pengprov'">
                        <label class="form-label fw-semibold">Pilih Kontingen Pengprov PBVSI <span class="text-danger">*</span></label>
                        <select name="pengprov_id" class="form-select">
                            <option value="">-- Pilih Pengprov Peserta --</option>
                            @foreach($pengprovList as $pp)
                                <option value="{{ $pp->id }}" {{ old('pengprov_id') == $pp->id ? 'selected' : '' }}>
                                    {{ $pp->nama_pengprov }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DROPDOWN KLUB -->
                    <div class="mb-3" x-show="tipeAsal === 'klub'" x-cloak>
                        <label class="form-label fw-semibold">Pilih Klub Bolavoli PBVSI <span class="text-danger">*</span></label>
                        <select name="klub_id" class="form-select">
                            <option value="">-- Pilih Klub Bolavoli --</option>
                            @foreach($klubList as $k)
                                <option value="{{ $k->id }}" {{ old('klub_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_klub }} ({{ $k->kode_klub ?: '-' }}) - {{ $k->pengprov?->nama_pengprov ?: 'PBVSI' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- KATEGORI GENDER & PUL / GRUP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Gender <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="kategori_gender" id="genderPAPage" value="PA" {{ old('kategori_gender', 'PA') === 'PA' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-bold text-info" for="genderPAPage">
                                        <i class="fa-solid fa-mars me-1"></i> Putra (PA)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline border rounded p-2 px-3 flex-fill bg-light">
                                    <input class="form-check-input" type="radio" name="kategori_gender" id="genderPIPage" value="PI" {{ old('kategori_gender') === 'PI' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="genderPIPage">
                                        <i class="fa-solid fa-venus me-1"></i> Putri (PI)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pul / Pool Grup Pertandingan</label>
                            <select name="pool_grup" class="form-select">
                                <option value="">-- Belum Diundi / Bebas --</option>
                                @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $pul)
                                    <option value="{{ $pul }}" {{ old('pool_grup') === $pul ? 'selected' : '' }}>Pul {{ $pul }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Dapat ditentukan saat technical meeting / drawing.</small>
                        </div>
                    </div>

                    <!-- PENYESUAIAN NAMA TAMPIL TIM (OPSIONAL) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tampil Tim Penyesuaian (Opsional)</label>
                        <input type="text" name="nama_tim_custom" class="form-control" placeholder="Contoh: Jawa Timur U-19 Putra (Kosongkan jika mengikuti nama master otomatis)" value="{{ old('nama_tim_custom') }}">
                    </div>

                    <!-- STATUS AKTIF -->
                    <div class="mb-2">
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active_page" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="cursor: pointer; width: 2.2em; height: 1.2em;">
                            <label class="form-check-label fw-semibold ms-2" for="is_active_page">
                                Status Keikutsertaan Tim Aktif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Assign Tim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
