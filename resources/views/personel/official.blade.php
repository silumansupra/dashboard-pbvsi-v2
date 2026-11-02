@extends('layouts.app')

@section('title', 'Data Official Tim Bola Voli PBVSI')

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
            <li class="breadcrumb-item active"><span class="badge bg-info text-dark px-2 py-1">Official Tim</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('personel.official.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Official Baru
        </a>
    </div>
</div>

<!-- STATS SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-info border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Official Terdaftar</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="bg-info-subtle text-info p-2 rounded-3">
                        <i class="fa-solid fa-user-tie fa-lg"></i>
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
                        <div class="text-muted small fw-semibold">Manager Tim</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stats['manager']) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="fa-solid fa-briefcase fa-lg"></i>
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
                        <div class="text-muted small fw-semibold">Tim Medis & Therapist</div>
                        <div class="fs-4 fw-bold text-warning">{{ number_format($stats['medis']) }}</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-2 rounded-3">
                        <i class="fa-solid fa-kit-medical fa-lg"></i>
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
                        <div class="text-muted small fw-semibold">Official Aktif</div>
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
        <form action="{{ route('personel.official') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama official, NIK, lisensi..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="jabatan_official" class="form-select form-select-sm">
                    <option value="">-- Semua Jabatan --</option>
                    <option value="MANAGER" {{ request('jabatan_official') == 'MANAGER' ? 'selected' : '' }}>Team Manager</option>
                    <option value="ASST_MANAGER" {{ request('jabatan_official') == 'ASST_MANAGER' ? 'selected' : '' }}>Assistant Manager</option>
                    <option value="THERAPIST" {{ request('jabatan_official') == 'THERAPIST' ? 'selected' : '' }}>Therapist</option>
                    <option value="MEDIS" {{ request('jabatan_official') == 'MEDIS' ? 'selected' : '' }}>Medis / Dokter Tim</option>
                    <option value="MASSEUR" {{ request('jabatan_official') == 'MASSEUR' ? 'selected' : '' }}>Masseur</option>
                    <option value="TRANSLATOR" {{ request('jabatan_official') == 'TRANSLATOR' ? 'selected' : '' }}>Penerjemah / Translator</option>
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
            <div class="col-md-1">
                <select name="status_aktif" class="form-select form-select-sm">
                    <option value="">-- Status --</option>
                    <option value="1" {{ request('status_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status_aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['search', 'jabatan_official', 'pengprov_id', 'status_aktif']))
                    <a href="{{ route('personel.official') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
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
                        <th style="min-width: 140px;">Nomor ID/Lisensi</th>
                        <th style="min-width: 220px;">Nama Official & NIK</th>
                        <th>Jabatan Official</th>
                        <th>Asal Pengprov</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($official as $index => $row)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $official->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                <i class="fa-solid fa-id-badge text-info me-1"></i>{{ $row->nomor_lisensi ?? ('OFF-' . str_pad($row->id, 5, '0', STR_PAD_LEFT)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2 rounded-circle flex-shrink-0" style="width: 36px; height: 36px;">
                                    <img src="{{ $row->foto_url }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/no-user.svg') }}';" alt="{{ $row->nama_lengkap }}">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $row->nama_lengkap }}</div>
                                    <div class="small text-muted font-monospace">{{ $row->nik ?? ($row->user?->username ?? '-') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if(str_contains($row->jabatan_official, 'MANAGER'))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">{{ $row->jabatan_official }}</span>
                            @elseif(str_contains($row->jabatan_official, 'MEDIS') || str_contains($row->jabatan_official, 'THERAPIST'))
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">{{ $row->jabatan_official }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">{{ $row->jabatan_official }}</span>
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
                            @if($row->jenis_kelamin == 'P')
                                <span class="badge bg-danger-subtle text-danger px-2"><i class="fa-solid fa-venus me-1"></i>P</span>
                            @else
                                <span class="badge bg-info-subtle text-info px-2"><i class="fa-solid fa-mars me-1"></i>L</span>
                            @endif
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
                                <a href="{{ route('personel.official.edit', $row->encrypted_id) }}" class="btn btn-outline-primary btn-sm" title="Edit Official">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="{{ $row->encrypted_id }}"
                                    data-nama="{{ $row->nama_lengkap }}"
                                    title="Hapus Official">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-user-tie fa-3x text-secondary mb-3 d-block opacity-50"></i>
                            <div class="fw-bold text-muted">Belum ada data official yang sesuai dengan filter pencarian.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    @if($official->hasPages() || $official->total() > 0)
    <div class="card-footer p-3 border-top bg-light">
        {{ $official->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('modals')
<!-- 3. MODAL HAPUS OFFICIAL -->
<div class="modal fade" id="modalHapusOfficial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHapusOfficial" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus Official
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fa-solid fa-trash-can fa-3x text-danger mb-3 d-block"></i>
                    <p class="mb-1">Apakah Anda yakin ingin menghapus data official berikut?</p>
                    <h5 class="fw-bold text-dark" id="hapus_nama_off">-</h5>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus</button>
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
            document.getElementById('hapus_nama_off').innerText = nama;
            document.getElementById('formHapusOfficial').action = "{{ url('personel/official') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalHapusOfficial'));
            modal.show();
        });
    });
});
</script>
@endpush
