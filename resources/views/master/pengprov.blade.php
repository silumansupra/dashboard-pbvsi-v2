@extends('layouts.app')

@section('title', 'Master Pengurus Provinsi (Pengprov)')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Organisasi</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Pengurus Provinsi</span></li>
        </ol>
    </nav>
    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Pengprov</button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar 37 Pengurus Provinsi (Pengprov) PBVSI Se-Indonesia</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Pengprov</th>
                                <th>Ketua Umum</th>
                                <th>Sekretaris Umum</th>
                                <th>Kontak HP / Telp</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengprov as $idx => $p)
                            <tr>
                                <td>{{ $pengprov->firstItem() + $idx }}</td>
                                <td>
                                    <strong>{{ $p->nama_pengprov }}</strong><br>
                                    <small class="text-muted">{{ $p->alamat ?? 'Alamat belum diatur' }}</small>
                                </td>
                                <td>{{ $p->ketum ?: '-' }}</td>
                                <td>{{ $p->sekum ?: '-' }}</td>
                                <td>
                                    @if($p->nohp_ketum || $p->nohp_sekum)
                                        <i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $p->nohp_ketum ?: $p->nohp_sekum }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $p->status_aktif ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $p->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen-to-square"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $pengprov->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
