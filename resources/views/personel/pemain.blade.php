@extends('layouts.app')

@section('title', 'Bank Pemain Bola Voli PBVSI')

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
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPemain">
            <i class="fa-solid fa-user-plus me-1"></i> Registrasi Pemain Baru
        </button>
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
                                <div class="avatar-sm me-2 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                                    @if($row->foto_profil_path)
                                        <img src="{{ asset('storage/' . $row->foto_profil_path) }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr($row->nama_lengkap, 0, 2)) }}
                                    @endif
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
                                <button type="button" class="btn btn-outline-primary btn-sm btn-edit"
                                    data-id="{{ $row->id }}"
                                    data-nama="{{ $row->nama_lengkap }}"
                                    data-punggung="{{ $row->nama_punggung }}"
                                    data-jk="{{ $row->jenis_kelamin }}"
                                    data-nik="{{ $row->nik }}"
                                    data-nisn="{{ $row->nisn }}"
                                    data-tempat-lahir="{{ $row->tempat_lahir }}"
                                    data-tgl-lahir="{{ $row->tanggal_lahir ? $row->tanggal_lahir->format('Y-m-d') : '' }}"
                                    data-posisi="{{ $row->posisi_utama }}"
                                    data-pengprov-id="{{ $row->pengprov_id }}"
                                    data-klub-id="{{ $row->klub_id }}"
                                    data-kewarganegaraan="{{ $row->status_kewarganegaraan }}"
                                    data-status="{{ $row->status_verifikasi }}"
                                    data-catatan="{{ $row->catatan_verifikasi }}"
                                    title="Edit Pemain">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="{{ $row->id }}"
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
<!-- 1. MODAL TAMBAH PEMAIN -->
<div class="modal fade" id="modalTambahPemain" tabindex="-1" aria-labelledby="modalTambahPemainLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('personel.pemain.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahPemainLabel">
                        <i class="fa-solid fa-user-plus me-1"></i> Registrasi Pemain Baru ke Bank Pemain
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Farhan Halim">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Punggung Jersey</label>
                            <input type="text" name="nama_punggung" class="form-control" placeholder="Contoh: FARHAN">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="PA">Putra (PA)</option>
                                <option value="PI">Putri (PI)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIK (Nomor KTP)</label>
                            <input type="text" name="nik" class="form-control" maxlength="16" placeholder="16 digit NIK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NISN Pelajar</label>
                            <input type="text" name="nisn" class="form-control" placeholder="10 digit NISN">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten lahir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Posisi Utama</label>
                            <select name="posisi_utama" class="form-select">
                                <option value="">-- Pilih Posisi --</option>
                                @foreach($posisiList as $pos)
                                    <option value="{{ $pos->kode_posisi }}">{{ $pos->kode_posisi }} - {{ $pos->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Asal Pengprov</label>
                            <select name="pengprov_id" class="form-select">
                                <option value="">-- Tanpa Pengprov --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_pengprov }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kewarganegaraan</label>
                            <select name="status_kewarganegaraan" class="form-select">
                                <option value="WNI_DAERAH">WNI Daerah</option>
                                <option value="WNI_NASIONAL">WNI Nasional</option>
                                <option value="WNA_ASING">WNA Asing</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Verifikasi</label>
                            <select name="status_verifikasi" class="form-select">
                                <option value="PENDING">PENDING (Menunggu Berkas)</option>
                                <option value="VERIFIED">VERIFIED (Sah Terverifikasi)</option>
                                <option value="REJECTED">REJECTED (Ditolak)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catatan Verifikasi</label>
                            <input type="text" name="catatan_verifikasi" class="form-control" placeholder="Catatan keabsahan dokumen">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan ke Bank Pemain</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT PEMAIN DENGAN DYNAMIC ACTION -->
<div class="modal fade" id="modalEditPemain" tabindex="-1" aria-labelledby="modalEditPemainLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditPemain" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditPemainLabel">
                        <i class="fa-solid fa-user-pen me-1"></i> Perbarui Data Pemain
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama Punggung Jersey</label>
                            <input type="text" name="nama_punggung" id="edit_punggung" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="edit_jk" class="form-select" required>
                                <option value="PA">Putra (PA)</option>
                                <option value="PI">Putri (PI)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIK (Nomor KTP)</label>
                            <input type="text" name="nik" id="edit_nik" class="form-control" maxlength="16">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NISN Pelajar</label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tgl_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Posisi Utama</label>
                            <select name="posisi_utama" id="edit_posisi" class="form-select">
                                <option value="">-- Pilih Posisi --</option>
                                @foreach($posisiList as $pos)
                                    <option value="{{ $pos->kode_posisi }}">{{ $pos->kode_posisi }} - {{ $pos->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Asal Pengprov</label>
                            <select name="pengprov_id" id="edit_pengprov_id" class="form-select">
                                <option value="">-- Tanpa Pengprov --</option>
                                @foreach($pengprovList as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_pengprov }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kewarganegaraan</label>
                            <select name="status_kewarganegaraan" id="edit_kewarganegaraan" class="form-select">
                                <option value="WNI_DAERAH">WNI Daerah</option>
                                <option value="WNI_NASIONAL">WNI Nasional</option>
                                <option value="WNA_ASING">WNA Asing</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Verifikasi</label>
                            <select name="status_verifikasi" id="edit_status" class="form-select">
                                <option value="PENDING">PENDING</option>
                                <option value="VERIFIED">VERIFIED</option>
                                <option value="REJECTED">REJECTED</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catatan Verifikasi</label>
                            <input type="text" name="catatan_verifikasi" id="edit_catatan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL DETAIL LENGKAP PEMAIN -->
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

    // Edit Modal Handler
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            document.getElementById('edit_nama').value = this.getAttribute('data-nama');
            document.getElementById('edit_punggung').value = this.getAttribute('data-punggung');
            document.getElementById('edit_jk').value = this.getAttribute('data-jk');
            document.getElementById('edit_nik').value = this.getAttribute('data-nik');
            document.getElementById('edit_nisn').value = this.getAttribute('data-nisn');
            document.getElementById('edit_tempat_lahir').value = this.getAttribute('data-tempat-lahir');
            document.getElementById('edit_tgl_lahir').value = this.getAttribute('data-tgl-lahir');
            document.getElementById('edit_posisi').value = this.getAttribute('data-posisi');
            document.getElementById('edit_pengprov_id').value = this.getAttribute('data-pengprov-id');
            document.getElementById('edit_kewarganegaraan').value = this.getAttribute('data-kewarganegaraan');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
            document.getElementById('edit_catatan').value = this.getAttribute('data-catatan');

            document.getElementById('formEditPemain').action = "{{ url('personel/pemain') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalEditPemain'));
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
