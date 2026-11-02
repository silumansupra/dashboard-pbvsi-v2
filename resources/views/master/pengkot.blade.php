@extends('layouts.app')

@section('title', 'Master Pengurus Kota/Kabupaten (Pengkot/Pengkab)')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Organisasi</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Pengurus Kota/Kabupaten</span></li>
        </ol>
    </nav>
    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Pengkot/Pengkab</button>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" action="{{ route('master.pengkot') }}" class="d-flex gap-2">
            <select name="pengprov_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Pengurus Provinsi --</option>
                @foreach($pengprovList as $pp)
                    <option value="{{ $pp->id }}" {{ request('pengprov_id') == $pp->id ? 'selected' : '' }}>
                        {{ $pp->nama_pengprov }}
                    </option>
                @endforeach
            </select>
            @if(request('pengprov_id'))
                <a href="{{ route('master.pengkot') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar Pengurus Kota & Kabupaten Terdata</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Pengkot / Pengkab</th>
                                <th>Pengprov Induk</th>
                                <th>Ketua Umum</th>
                                <th>Sekretaris</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengkot as $idx => $pk)
                            <tr>
                                <td>{{ $pengkot->firstItem() + $idx }}</td>
                                <td><strong>{{ $pk->nama_pengkot }}</strong></td>
                                <td>{{ $pk->pengprov->nama_pengprov ?? '-' }}</td>
                                <td>{{ $pk->ketum ?: '-' }}</td>
                                <td>{{ $pk->sekum ?: '-' }}</td>
                                <td>
                                    <span class="badge {{ $pk->status_aktif ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $pk->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen-to-square"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada pengurus kota/kabupaten terdata untuk filter ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $pengkot->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
