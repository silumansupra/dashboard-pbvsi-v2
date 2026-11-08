@extends('layouts.app')

@section('title', 'Official & Pelatih Tim - ' . $activeTeam->nama_tim)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Portal Tim</a></li>
            <li class="breadcrumb-item active" aria-current="page">Official & Pelatih</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- HEADER HERO -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-white">{{ $activeTeam->nama_tim }}</h3>
                    <span class="badge {{ $activeTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }} px-3 py-1 fs-6">
                        <i class="fa-solid {{ $activeTeam->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                        {{ $activeTeam->kategori_gender == 'PA' ? 'PUTRA (PA)' : 'PUTRI (PI)' }}
                    </span>
                </div>
                <div class="text-white-50 small">
                    Official & Tim Pendukung Kontingen &bull; Total: <strong>{{ $officials->count() }} Official</strong>
                </div>
            </div>

            <div class="col-auto">
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahOfficial">
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Official
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TABEL DAFTAR OFFICIAL -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fa-solid fa-user-tie text-primary me-2"></i>Daftar Pelatih & Official Kontingen ({{ $officials->count() }})
        </h5>
        <span class="badge bg-secondary-subtle text-secondary border">Maksimal 5-7 Official di Bench</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Nama Lengkap Official</th>
                        <th>Jabatan di Tim</th>
                        <th>Nomor Lisensi Kepelatihan</th>
                        <th class="text-center">Status PBVSI</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($officials as $index => $o)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $o->nama_lengkap }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle fw-semibold">
                                    {{ $o->label_jabatan }}
                                </span>
                            </td>
                            <td>
                                @if($o->nomor_lisensi)
                                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-id-card me-1 text-primary"></i> {{ $o->nomor_lisensi }}</span>
                                @else
                                    <span class="text-muted small">Tidak ada / Belum diinput</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($o->status_verifikasi == 'VERIFIED')
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                @elseif($o->status_verifikasi == 'REJECTED')
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-clock me-1"></i> Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('tim.official.destroy', $o->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus official ini dari tim?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Official">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-tie fa-3x mb-3 text-secondary"></i>
                                <div>Belum ada official atau pelatih yang didaftarkan pada tim ini.</div>
                                <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalTambahOfficial">
                                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Official Sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH OFFICIAL -->
<div class="modal fade" id="modalTambahOfficial" tabindex="-1" aria-labelledby="modalTambahOfficialLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tim.official.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahOfficialLabel">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>Tambah Official / Pelatih
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Official <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap beserta gelar (jika ada)" required value="{{ old('nama_lengkap') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan di Tim <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="HEAD_COACH">Pelatih Kepala (Head Coach)</option>
                            <option value="AST_COACH_1">Asisten Pelatih 1</option>
                            <option value="AST_COACH_2">Asisten Pelatih 2</option>
                            <option value="MANAGER">Manajer Tim</option>
                            <option value="AST_MANAGER">Asisten Manajer</option>
                            <option value="TRAINER">Pelatih Fisik / Trainer</option>
                            <option value="THERAPIST">Terapis / Fisioterapis</option>
                            <option value="MEDIS">Dokter / Tim Medis</option>
                            <option value="TRANSLATOR">Penerjemah</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Lisensi Pelatih (Opsional)</label>
                        <input type="text" name="nomor_lisensi" class="form-control" placeholder="Contoh: LIC-PBVSI-B-2024-001" value="{{ old('nomor_lisensi') }}">
                        <small class="text-muted">Untuk Pelatih Kepala & Asisten, wajib mencantumkan lisensi nasional / FIVB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Official
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
