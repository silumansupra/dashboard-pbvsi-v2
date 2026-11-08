@extends('layouts.app')

@section('title', 'Master Posisi Pemain Bolavoli')

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
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Posisi Pemain</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('master.posisi.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Posisi Baru
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- HEADER INFO & SEARCH -->
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <form action="{{ route('master.posisi') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama posisi..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('master.posisi') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                        <button type="submit" class="btn btn-secondary">Cari</button>
                    </div>
                </form>
                <div class="small text-muted">
                    Standar Posisi FIVB / PBVSI (Setter, Outside Hitter, Opposite, Middle Blocker, Libero)
                </div>
            </div>

            <!-- TABEL DAFTAR POSISI -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 120px;" class="text-center">Kode Posisi</th>
                                <th>Nama Lengkap Posisi</th>
                                <th>Deskripsi & Peran Taktikal</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posisi as $idx => $pos)
                            <tr>
                                <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6 px-3 py-1 font-monospace shadow-sm">
                                        {{ $pos->kode_posisi }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $pos->nama_posisi }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $pos->deskripsi ?: 'Tidak ada deskripsi' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pos->is_active)
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
                                    <a href="{{ route('master.posisi.edit', $pos->encrypted_id) }}"
                                       class="btn btn-sm btn-outline-secondary me-1"
                                       title="Edit Posisi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('master.posisi.destroy', $pos->encrypted_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus posisi {{ $pos->kode_posisi }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Posisi">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-person-running fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data posisi pemain yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
