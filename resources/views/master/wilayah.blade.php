@extends('layouts.app')

@section('title', 'Master Wilayah (Provinsi & Kabupaten)')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a>
            </li>
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Master Wilayah</span></li>
        </ol>
    </nav>
    <div class="d-none d-md-flex align-items-center text-muted small">
        <i class="fa-solid fa-map-location-dot text-success me-1"></i> 37 Provinsi &bull; 514 Kota/Kabupaten
    </div>
</div>

<div class="row">
    <!-- KOLOM KIRI: DAFTAR PROVINSI -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-map me-1 text-primary"></i> Daftar Provinsi PBVSI ({{ $provinsi->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                    @foreach($provinsi as $p)
                    <a href="{{ route('master.wilayah', ['provinsi_id' => $p->id]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 {{ $p->id == $selectedProvId ? 'active bg-primary text-white border-primary' : '' }}">
                        <div>
                            <span class="badge {{ $p->id == $selectedProvId ? 'bg-light text-dark' : 'bg-secondary' }} me-2">{{ $p->kode_provinsi }}</span>
                            <strong>{{ $p->nama_provinsi }}</strong>
                        </div>
                        <span class="badge {{ $p->id == $selectedProvId ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} rounded-pill">
                            {{ $p->kabupaten_count }} Kota/Kab
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: DAFTAR KOTA / KABUPATEN -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-city me-1 text-success"></i> Kabupaten / Kota di {{ $provinsi->firstWhere('id', $selectedProvId)?->nama_provinsi }}
                    </h5>
                    <small class="text-muted">Total terdata pada provinsi ini</small>
                </div>
                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i> Tambah Kota/Kab</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nama Kabupaten / Kota</th>
                                <th>Tipe Wilayah</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kabupaten as $idx => $kab)
                            <tr>
                                <td>{{ $kabupaten->firstItem() + $idx }}</td>
                                <td><strong>{{ $kab->nama_kabupaten }}</strong></td>
                                <td>
                                    <span class="badge {{ $kab->tipe == 'KOTA' ? 'bg-info' : 'bg-secondary' }}">
                                        {{ $kab->tipe }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen-to-square"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada kabupaten terdata untuk provinsi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $kabupaten->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
