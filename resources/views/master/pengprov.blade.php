@extends('layouts.app')

@section('title', 'Master Pengurus Provinsi (Pengprov)')

@section('content')
<!-- NOTIFIKASI SUKSES / ERROR -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation fa-lg me-2 text-danger"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

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

<!-- BREADCRUMB & HEADER -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Organisasi</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Pengurus Provinsi</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('master.pengprov.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Pengprov
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.pengprov') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama pengprov, ketum, sekum, email..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="status_aktif" class="form-select form-select-sm">
                            <option value="">Semua Status Organisasi</option>
                            <option value="1" {{ request('status_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status_aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / hal</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request('search') || request('status_aktif') || request('per_page'))
                            <a href="{{ route('master.pengprov') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR PENGPROV -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Pengurus Provinsi</th>
                                <th>Ketua & Sekretaris Umum</th>
                                <th>Kontak & Alamat</th>
                                <th class="text-center" style="width: 140px;">Binaan</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th class="text-end" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengprov as $idx => $p)
                            <tr>
                                <td class="text-center text-muted">{{ $pengprov->firstItem() + $idx }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            {{ $p->provinsi?->kode_provinsi ?: '-' }}
                                        </span>
                                        <div>
                                            <strong class="text-dark">{{ $p->nama_pengprov }}</strong><br>
                                            <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-secondary"></i>{{ $p->provinsi?->nama_provinsi }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong>Ketum:</strong> {{ $p->ketum ?: '-' }}</div>
                                    <div class="small text-muted"><strong>Sekum:</strong> {{ $p->sekum ?: '-' }}</div>
                                </td>
                                <td>
                                    @if($p->nohp_ketum || $p->nohp_sekum || $p->no_telp)
                                        <div class="small"><i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $p->nohp_ketum ?: ($p->nohp_sekum ?: $p->no_telp) }}</div>
                                    @endif
                                    @if($p->email)
                                        <div class="small text-muted"><i class="fa-regular fa-envelope me-1"></i> {{ $p->email }}</div>
                                    @endif
                                    @if($p->alamat)
                                        <div class="small text-truncate text-secondary" style="max-width: 220px;" title="{{ $p->alamat }}">
                                            <i class="fa-solid fa-map-pin me-1"></i> {{ $p->alamat }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" title="Jumlah Pengkot/Pengkab">
                                        {{ $p->pengkot_count }} Pengkot
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 mt-1" title="Jumlah Klub Terdaftar">
                                        {{ $p->klub_count }} Klub
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($p->status_aktif)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-check me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('master.pengprov.edit', $p->encrypted_id) }}" 
                                       class="btn btn-sm btn-outline-secondary me-1" 
                                       title="Edit Data Pengprov">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('master.pengprov.destroy', $p->encrypted_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Pengprov {{ $p->nama_pengprov }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ ($p->pengkot_count > 0 || $p->klub_count > 0) ? 'disabled title="Tidak bisa dihapus karena memiliki Pengkot atau Klub binaan"' : 'title="Hapus Pengprov"' }}>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-building-circle-exclamation fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Pengurus Provinsi yang sesuai dengan kriteria filter.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pengprov->hasPages() || $pengprov->total() > 0)
                <div class="p-3 border-top bg-light">
                    {{ $pengprov->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
