@extends('layouts.app')

@section('title', 'Bank Pemain Bolavoli PBVSI')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Bank Pemain</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('personel.pemain.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Registrasi Pemain Baru
        </a>
    </div>
</div>

<!-- STATS SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Bank Pemain</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="fa-solid fa-id-card-clip fa-lg"></i>
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
                        <div class="text-muted small fw-semibold">Pemain Terverifikasi</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['verified']) }}</div>
                    </div>
                    <div class="bg-success-subtle text-success p-2 rounded-3">
                        <i class="fa-solid fa-certificate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-info border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Atlet Putra (PA)</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($stats['pa']) }}</div>
                    </div>
                    <div class="bg-info-subtle text-info p-2 rounded-3">
                        <i class="fa-solid fa-mars fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Atlet Putri (PI)</div>
                        <div class="fs-4 fw-bold text-danger">{{ number_format($stats['pi']) }}</div>
                    </div>
                    <div class="bg-danger-subtle text-danger p-2 rounded-3">
                        <i class="fa-solid fa-venus fa-lg"></i>
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
        <form action="{{ route('personel.pemain') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama, ID, NIK, punggung..." value="{{ request('search') }}">
                </div>
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
                <select name="jenis_kelamin" class="form-select form-select-sm">
                    <option value="">-- Gender --</option>
                    <option value="PA" {{ request('jenis_kelamin') == 'PA' ? 'selected' : '' }}>Putra (PA)</option>
                    <option value="PI" {{ request('jenis_kelamin') == 'PI' ? 'selected' : '' }}>Putri (PI)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_verifikasi" class="form-select form-select-sm">
                    <option value="">-- Status Verifikasi --</option>
                    <option value="VERIFIED" {{ request('status_verifikasi') == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                    <option value="PENDING" {{ request('status_verifikasi') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="REJECTED" {{ request('status_verifikasi') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'pengprov_id', 'jenis_kelamin', 'status_verifikasi', 'posisi_utama']))
                    <a href="{{ route('personel.pemain') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                        <th style="min-width: 200px;">Pemain & ID PBVSI</th>
                        <th>Posisi</th>
                        <th>Gender</th>
                        <th>Asal Pengprov / Daerah</th>
                        <th>Tanggal Lahir / Usia</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemain as $index => $row)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $pemain->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2 rounded-circle flex-shrink-0" style="width: 38px; height: 38px;">
                                    <img src="{{ $row->foto_url }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/no-user.svg') }}';" alt="{{ $row->nama_lengkap }}">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $row->nama_lengkap }}</div>
                                    <div class="small text-muted d-flex align-items-center gap-1">
                                        <span class="badge bg-secondary-subtle text-secondary border font-monospace" style="font-size: 0.7rem;">{{ $row->pbvsi_player_id }}</span>
                                        @if($row->nama_punggung)
                                            <span class="text-secondary fst-italic">"{{ $row->nama_punggung }}"</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($row->posisi_utama)
                                <span class="badge bg-light text-dark border fw-semibold">{{ $row->posisi_utama }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->jenis_kelamin == 'PA')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2"><i class="fa-solid fa-mars me-1"></i>Putra</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2"><i class="fa-solid fa-venus me-1"></i>Putri</span>
                            @endif
                        </td>
                        <td>
                            @if($row->pengprov)
                                <span class="small fw-semibold text-dark">{{ $row->pengprov->nama_pengprov }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary small">Belum terasosiasi</span>
                            @endif
                        </td>
                        <td>
                            @if($row->tanggal_lahir)
                                <div class="small fw-semibold text-dark">{{ $row->tanggal_lahir->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $row->tanggal_lahir->age }} Tahun</div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status_verifikasi == 'VERIFIED')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Verified</span>
                            @elseif($row->status_verifikasi == 'REJECTED')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-xmark me-1"></i>Ditolak</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fa-regular fa-clock me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info btn-sm btn-detail"
                                    data-id="{{ $row->id }}"
                                    data-nama="{{ $row->nama_lengkap }}"
                                    data-pbvsi-id="{{ $row->pbvsi_player_id }}"
                                    data-punggung="{{ $row->nama_punggung }}"
                                    data-nik="{{ $row->nik }}"
                                    data-nisn="{{ $row->nisn }}"
                                    data-jk="{{ $row->jenis_kelamin == 'PA' ? 'Putra' : 'Putri' }}"
                                    data-tempat-lahir="{{ $row->tempat_lahir }}"
                                    data-tgl-lahir="{{ $row->tanggal_lahir ? $row->tanggal_lahir->format('d F Y') : '-' }}"
                                    data-usia="{{ $row->tanggal_lahir ? $row->tanggal_lahir->age . ' Tahun' : '-' }}"
                                    data-posisi="{{ $row->posisi_utama ?? '-' }}"
                                    data-pengprov="{{ $row->pengprov ? $row->pengprov->nama_pengprov : '-' }}"
                                    data-kewarganegaraan="{{ $row->status_kewarganegaraan }}"
                                    data-status="{{ $row->status_verifikasi }}"
                                    data-catatan="{{ $row->catatan_verifikasi ?? 'Tidak ada catatan' }}"
                                    title="Detail Lengkap">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('personel.pemain.edit', $row->encrypted_id) }}" class="btn btn-outline-primary btn-sm" title="Edit Pemain">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="{{ $row->encrypted_id }}"
                                    data-nama="{{ $row->nama_lengkap }}"
                                    title="Hapus Pemain">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-user-slash fa-3x text-secondary mb-3 d-block opacity-50"></i>
                            <div class="fw-bold text-muted">Tidak ada data pemain yang cocok dengan kriteria pencarian.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    @if($pemain->hasPages() || $pemain->total() > 0)
    <div class="card-footer p-3 border-top bg-light">
        {{ $pemain->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('modals')
<!-- 1. MODAL DETAIL LENGKAP PEMAIN -->
<div class="modal fade" id="modalDetailPemain" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-id-card me-1 text-info"></i> Profil Atlet PBVSI
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-2 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold fs-3 border" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0" id="detail_nama">-</h5>
                    <div class="text-muted small" id="detail_punggung">-</div>
                    <span class="badge bg-primary-subtle text-primary border font-monospace mt-1" id="detail_pbvsi_id">-</span>
                </div>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Jenis Kelamin:</span>
                        <span class="fw-semibold text-dark" id="detail_jk">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Posisi Bermain:</span>
                        <span class="fw-semibold text-dark" id="detail_posisi">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tempat, Tanggal Lahir:</span>
                        <span class="fw-semibold text-dark" id="detail_ttl">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Usia Saat Ini:</span>
                        <span class="fw-semibold text-dark" id="detail_usia">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Asal Pengprov:</span>
                        <span class="fw-semibold text-dark" id="detail_pengprov">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">NIK:</span>
                        <span class="fw-semibold text-dark font-monospace" id="detail_nik">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">NISN:</span>
                        <span class="fw-semibold text-dark font-monospace" id="detail_nisn">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Status Verifikasi:</span>
                        <span class="fw-bold" id="detail_status">-</span>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted d-block mb-1">Catatan Verifikasi:</span>
                        <div class="p-2 bg-light rounded text-muted" id="detail_catatan">-</div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. MODAL KONFIRMASI HAPUS -->
<div class="modal fade" id="modalHapusPemain" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHapusPemain" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus Pemain
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fa-solid fa-trash-can fa-3x text-danger mb-3 d-block"></i>
                    <p class="mb-1">Apakah Anda yakin ingin menghapus data pemain berikut dari Bank Pemain?</p>
                    <h5 class="fw-bold text-dark" id="hapus_nama_pemain">-</h5>
                    <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus Pemain</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Detail Modal Handler
    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('detail_nama').innerText = this.getAttribute('data-nama');
            document.getElementById('detail_punggung').innerText = this.getAttribute('data-punggung') ? '"' + this.getAttribute('data-punggung') + '"' : '';
            document.getElementById('detail_pbvsi_id').innerText = this.getAttribute('data-pbvsi-id');
            document.getElementById('detail_jk').innerText = this.getAttribute('data-jk');
            document.getElementById('detail_posisi').innerText = this.getAttribute('data-posisi');
            document.getElementById('detail_ttl').innerText = (this.getAttribute('data-tempat-lahir') || '-') + ', ' + this.getAttribute('data-tgl-lahir');
            document.getElementById('detail_usia').innerText = this.getAttribute('data-usia');
            document.getElementById('detail_pengprov').innerText = this.getAttribute('data-pengprov');
            document.getElementById('detail_nik').innerText = this.getAttribute('data-nik') || '-';
            document.getElementById('detail_nisn').innerText = this.getAttribute('data-nisn') || '-';

            const status = this.getAttribute('data-status');
            const statusEl = document.getElementById('detail_status');
            statusEl.innerText = status;
            statusEl.className = 'fw-bold ' + (status === 'VERIFIED' ? 'text-success' : (status === 'REJECTED' ? 'text-danger' : 'text-warning'));

            document.getElementById('detail_catatan').innerText = this.getAttribute('data-catatan') || 'Tidak ada catatan khusus';

            const modal = new bootstrap.Modal(document.getElementById('modalDetailPemain'));
            modal.show();
        });
    });

    // Delete Modal Handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            document.getElementById('hapus_nama_pemain').innerText = nama;
            document.getElementById('formHapusPemain').action = "{{ url('personel/pemain') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalHapusPemain'));
            modal.show();
        });
    });
});
</script>
@endpush
