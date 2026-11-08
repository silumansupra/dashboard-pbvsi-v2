@extends('layouts.app')

@section('title', 'Master Tim & Klub Bolavoli')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Data Klub & Tim</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        @if($tipe === 'klub')
        <a href="{{ route('master.klub.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Klub Baru
        </a>
        @endif
    </div>
</div>

<!-- TOGGLE TIPE: KLUB VS KONTINGEN PENGPROV -->
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('master.klub', array_merge(request()->except(['page']), ['tipe' => 'klub'])) }}"
       class="btn btn-sm rounded-pill px-3 py-2 {{ $tipe === 'klub' ? 'btn-primary shadow-sm' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-shield-halved me-1"></i> Klub Bolavoli PBVSI
        <span class="badge {{ $tipe === 'klub' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $totalKlubCount }} Klub</span>
    </a>
    <a href="{{ route('master.klub', array_merge(request()->except(['page']), ['tipe' => 'pengprov'])) }}"
       class="btn btn-sm rounded-pill px-3 py-2 {{ $tipe === 'pengprov' ? 'btn-primary shadow-sm' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-flag me-1"></i> Kontingen Daerah (Pengprov)
        <span class="badge {{ $tipe === 'pengprov' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $totalPengprovCount }} Provinsi</span>
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.klub') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tipe" value="{{ $tipe }}">

                    <div class="col-md-4">
                        <select name="pengprov_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Pengprov Induk</option>
                            @foreach($pengprovList as $pp)
                                <option value="{{ $pp->encrypted_id }}" {{ (request('pengprov_id') === $pp->encrypted_id || decrypt_id(request('pengprov_id'), 'pengprov') == $pp->id || request('pengprov_id') == $pp->id) ? 'selected' : '' }}>
                                    {{ $pp->nama_pengprov }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama tim, kode, pengurus..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($tipe === 'klub')
                    <div class="col-md-2">
                        <select name="status_verifikasi" class="form-select form-select-sm">
                            <option value="">Semua Verifikasi</option>
                            <option value="VERIFIED" {{ request('status_verifikasi') == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                            <option value="PENDING" {{ request('status_verifikasi') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                            <option value="REJECTED" {{ request('status_verifikasi') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                        </select>
                    </div>
                    @else
                    <div class="col-md-2"></div>
                    @endif

                    <div class="col-md-1">
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request('pengprov_id') || request('search') || request('status_verifikasi') || request('per_page'))
                            <a href="{{ route('master.klub', ['tipe' => $tipe]) }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR TIM / KLUB -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    @if($tipe === 'pengprov')
                    <!-- TABEL KONTINGEN PENGPROV -->
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 140px;">Kode</th>
                                <th>Nama Kontingen Daerah</th>
                                <th>Wilayah Provinsi</th>
                                <th>Turnamen Diikuti</th>
                                <th>Ketua Umum & Sekum</th>
                                <th class="text-end" style="width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengprovTeams as $idx => $p)
                            <tr>
                                <td class="text-center text-muted">{{ $pengprovTeams->firstItem() + $idx }}</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-dark border font-monospace">
                                        PENGPROV-{{ $p->provinsi_id ?? $p->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-warning-subtle text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                            <i class="fa-solid fa-flag"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $p->nama_pengprov }}</strong><br>
                                            <small class="text-muted"><i class="fa-solid fa-circle-check text-success me-1"></i>Kontingen PBVSI Resmi</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $p->provinsi?->nama_provinsi ?? $p->nama_pengprov }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                        <i class="fa-solid fa-trophy me-1"></i> {{ $p->peserta_kontingen_count }} Kejuaraan
                                    </span>
                                </td>
                                <td>
                                    <div><strong>Ketum:</strong> {{ $p->ketum ?: '-' }}</div>
                                    <div class="small text-muted"><strong>Sekum:</strong> {{ $p->sekum ?: '-' }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('master.klub.detail', ['tipe' => 'pengprov', 'id' => $p->encrypted_id]) }}"
                                       class="btn btn-sm btn-primary shadow-sm"
                                       title="Lihat Riwayat Turnamen & Roster Pemain">
                                        <i class="fa-solid fa-eye me-1"></i> Riwayat & Roster
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-flag fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Kontingen Pengprov yang sesuai filter.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @else
                    <!-- TABEL KLUB Bolavoli PBVSI -->
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 140px;">Kode Klub</th>
                                <th>Nama Tim / Klub</th>
                                <th>Pengprov / Pengkot Asal</th>
                                <th>Turnamen Diikuti</th>
                                <th>Pemilik & Sekretaris</th>
                                <th class="text-center" style="width: 130px;">Verifikasi</th>
                                <th class="text-end" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klub as $idx => $k)
                            <tr>
                                <td class="text-center text-muted">{{ $klub->firstItem() + $idx }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ $k->kode_klub }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($k->logo_url)
                                            <div class="rounded border p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <img src="{{ $k->logo_url }}" alt="{{ $k->nama_klub }}" class="img-fluid object-fit-contain" style="max-height: 32px; max-width: 32px;" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-shield-halved text-primary\'></i>';">
                                            </div>
                                        @else
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong class="text-dark">{{ $k->nama_klub }}</strong><br>
                                            <small class="text-muted">{{ $k->alamat ? Str::limit($k->alamat, 35) : 'Alamat belum diatur' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong>{{ $k->pengprov?->nama_pengprov ?? '-' }}</strong></div>
                                    <small class="text-muted">{{ $k->pengkot?->nama_pengkot ?? 'Pengkot/Pengkab belum ditentukan' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                        <i class="fa-solid fa-trophy me-1"></i> {{ $k->peserta_turnamen_count }} Kejuaraan
                                    </span>
                                </td>
                                <td>
                                    <div><strong>Pemilik:</strong> {{ $k->pemilik_klub ?: '-' }}</div>
                                    <div class="small text-muted"><strong>Sekretaris:</strong> {{ $k->sekretaris_klub ?: '-' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($k->status_verifikasi === 'VERIFIED')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> TERVERIFIKASI
                                        </span>
                                    @elseif($k->status_verifikasi === 'REJECTED')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('master.klub.detail', ['tipe' => 'klub', 'id' => $k->encrypted_id]) }}"
                                       class="btn btn-sm btn-primary me-1 shadow-sm"
                                       title="Lihat Riwayat Turnamen & Roster">
                                        <i class="fa-solid fa-eye me-1"></i> Riwayat & Roster
                                    </a>

                                    <a href="{{ route('master.klub.edit', $k->encrypted_id) }}"
                                       class="btn btn-sm btn-outline-secondary me-1"
                                       title="Edit Data Klub">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('master.klub.destroy', $k->encrypted_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus klub {{ $k->nama_klub }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Klub">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-shield-halved fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Klub yang ditemukan sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @endif
                </div>

                @if($tipe === 'pengprov' && $pengprovTeams && ($pengprovTeams->hasPages() || $pengprovTeams->total() > 0))
                <div class="p-3 border-top bg-light">
                    {{ $pengprovTeams->links('vendor.pagination.bootstrap-5') }}
                </div>
                @elseif($tipe === 'klub' && $klub && ($klub->hasPages() || $klub->total() > 0))
                <div class="p-3 border-top bg-light">
                    {{ $klub->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
