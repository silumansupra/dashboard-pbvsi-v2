@extends('layouts.app')

@section('title', 'Data Wasit Bola Voli PBVSI')

@section('content')
<!-- NOTIFIKASI -->
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
            <li class="breadcrumb-item text-secondary">Database Personel</li>
            <li class="breadcrumb-item active"><span class="badge bg-danger px-2 py-1">Data Wasit</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('personel.wasit.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Wasit Baru
        </a>
    </div>
</div>

<!-- STATS SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Wasit Terdaftar</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="bg-danger-subtle text-danger p-2 rounded-3">
                        <i class="fa-solid fa-flag fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Wasit Bola Voli Indoor</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stats['indoor']) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="fa-solid fa-volleyball fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Wasit Voli Pantai</div>
                        <div class="fs-4 fw-bold text-warning">{{ number_format($stats['volipantai']) }}</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-2 rounded-3">
                        <i class="fa-solid fa-umbrella-beach fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Wasit Status Aktif</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['aktif']) }}</div>
                    </div>
                    <div class="bg-success-subtle text-success p-2 rounded-3">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="card shadow-sm border-0">
    <!-- FILTER TOOLBAR -->
    <div class="card-header bg-white py-3 border-bottom">
        <form action="{{ route('personel.wasit') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama wasit, lisensi, user..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="kategori_wasit" class="form-select form-select-sm">
                    <option value="">-- Kategori --</option>
                    <option value="INDOOR" {{ request('kategori_wasit') == 'INDOOR' ? 'selected' : '' }}>Indoor</option>
                    <option value="VOLI_PANTAI" {{ request('kategori_wasit') == 'VOLI_PANTAI' ? 'selected' : '' }}>Voli Pantai</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="pengprov_id" class="form-select form-select-sm">
                    <option value="">-- Semua Pengprov --</option>
                    @foreach($pengprovList as $p)
                        <option value="{{ $p->encrypted_id }}" {{ (request('pengprov_id') === $p->encrypted_id || decrypt_id(request('pengprov_id'), 'pengprov') == $p->id || request('pengprov_id') == $p->id) ? 'selected' : '' }}>{{ $p->nama_pengprov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_aktif" class="form-select form-select-sm">
                    <option value="">-- Status Aktif --</option>
                    <option value="1" {{ request('status_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status_aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['search', 'kategori_wasit', 'tingkat_wasit', 'pengprov_id', 'status_aktif']))
                    <a href="{{ route('personel.wasit') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE BODY -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th style="min-width: 140px;">Nomor Lisensi</th>
                        <th style="min-width: 220px;">Nama Wasit & Akun</th>
                        <th>Kategori</th>
                        <th>Asal Pengprov</th>
                        <th>Tingkat</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wasit as $index => $row)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $wasit->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                <i class="fa-solid fa-id-badge text-danger me-1"></i>{{ $row->nomor_lisensi_wasit }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row->user?->name ?? 'Belum ada nama' }}</div>
                            <div class="small text-muted font-monospace">{{ $row->user?->username ?? '-' }}</div>
                        </td>
                        <td>
                            @if($row->kategori_wasit == 'VOLI_PANTAI')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="fa-solid fa-umbrella-beach me-1"></i>Voli Pantai
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="fa-solid fa-volleyball me-1"></i>Indoor
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($row->pengprov)
                                <span class="small fw-semibold text-dark">{{ $row->pengprov->nama_pengprov }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary small">PP PBVSI Pusat</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $row->tingkat_wasit ?? 'Nasional' }}</span>
                        </td>
                        <td>
                            @if($row->status_aktif)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('personel.wasit.edit', $row->encrypted_id) }}" class="btn btn-outline-danger btn-sm" title="Edit Wasit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="{{ $row->encrypted_id }}"
                                    data-nama="{{ $row->user?->name }}"
                                    title="Hapus Wasit">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-flag fa-3x text-secondary mb-3 d-block opacity-50"></i>
                            <div class="fw-bold text-muted">Belum ada data wasit yang cocok dengan filter pencarian.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    @if($wasit->hasPages() || $wasit->total() > 0)
    <div class="card-footer p-3 border-top bg-light">
        {{ $wasit->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('modals')
<!-- 3. MODAL HAPUS WASIT -->
<div class="modal fade" id="modalHapusWasit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHapusWasit" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus Wasit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fa-solid fa-trash-can fa-3x text-danger mb-3 d-block"></i>
                    <p class="mb-1">Apakah Anda yakin ingin menghapus wasit berikut?</p>
                    <h5 class="fw-bold text-dark" id="hapus_nama_wasit">-</h5>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus Wasit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete Modal Handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            document.getElementById('hapus_nama_wasit').innerText = nama;
            document.getElementById('formHapusWasit').action = "{{ url('personel/wasit') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalHapusWasit'));
            modal.show();
        });
    });
});
</script>
@endpush
