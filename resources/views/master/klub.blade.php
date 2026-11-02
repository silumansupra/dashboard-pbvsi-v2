@extends('layouts.app')

@section('title', 'Master Klub Resmi PBVSI')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Organisasi</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Klub Bola Voli</span></li>
        </ol>
    </nav>
    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Daftarkan Klub Baru</button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar Klub Resmi Anggota PBVSI</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Klub</th>
                                <th>Pengprov Induk</th>
                                <th>Pemilik / Penanggung Jawab</th>
                                <th>Sekretaris</th>
                                <th>Status Verifikasi</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klub as $idx => $k)
                            <tr>
                                <td>{{ $klub->firstItem() + $idx }}</td>
                                <td>
                                    <strong>{{ $k->nama_klub }}</strong><br>
                                    <small class="text-muted">Kode: {{ $k->kode_klub ?? '-' }}</small>
                                </td>
                                <td>{{ $k->pengprov->nama_pengprov ?? '-' }}</td>
                                <td>{{ $k->pemilik_klub ?: '-' }}</td>
                                <td>{{ $k->sekretaris_klub ?: '-' }}</td>
                                <td>
                                    <span class="badge {{ $k->status_verifikasi == 'VERIFIED' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $k->status_verifikasi }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen-to-square"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada klub terdata. Gunakan tombol Daftarkan Klub Baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $klub->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
