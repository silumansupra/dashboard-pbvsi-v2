@extends('layouts.app')

@section('title', 'Data Pemain & Atlet - ' . $activeTeam->nama_tim)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Portal Tim</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Pemain</li>
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
                    Roster Skuad Kejurnas PBVSI &bull; Kuota: <strong>{{ $roster->count() }} / 14 Pemain</strong>
                    @if($roster->count() >= 10 && $roster->count() <= 14)
                        <span class="badge bg-success ms-2"><i class="fa-solid fa-check me-1"></i> Kuota Memenuhi Syarat (10-14)</span>
                    @else
                        <span class="badge bg-warning text-dark ms-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Minimal 10 Pemain</span>
                    @endif
                </div>
            </div>

            <div class="col-auto">
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPemain" {{ $roster->count() >= 14 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Pemain
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TABEL DAFTAR PEMAIN -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fa-solid fa-users text-primary me-2"></i>Daftar Roster Pemain Terdaftar ({{ $roster->count() }})
        </h5>
        <a href="{{ route('tim.roster') }}" class="btn btn-sm btn-outline-primary fw-semibold">
            <i class="fa-solid fa-clipboard-list me-1"></i> Kelola Kapten & Libero
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 70px;">No</th>
                        <th style="width: 70px;">Foto</th>
                        <th>Nama Pemain & Identitas</th>
                        <th>Posisi</th>
                        <th>Tanggal Lahir / Usia</th>
                        <th>TB / BB</th>
                        <th class="text-center">Peran Tim</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roster as $r)
                        @php
                            $pemain = $r->pemain;
                            $usia = $pemain && $pemain->tanggal_lahir ? \Carbon\Carbon::parse($pemain->tanggal_lahir)->age : null;
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-dark fs-6 px-2 py-1">#{{ $r->nomor_punggung }}</span>
                            </td>
                            <td>
                                @if($pemain && $pemain->foto_url)
                                    <img src="{{ $pemain->foto_url }}" alt="{{ $pemain->nama_lengkap }}" class="rounded-circle object-fit-cover shadow-sm" width="44" height="44" onerror="this.onerror=null; this.src='{{ asset('images/no-user.svg') }}';">
                                @else
                                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pemain ? $pemain->nama_lengkap : 'Pemain #' . $r->nomor_punggung }}</div>
                                <div class="text-muted small">
                                    @if($pemain && $pemain->nik && !str_starts_with($pemain->nik, '00000000'))
                                        <span>NIK: {{ $pemain->nik }}</span>
                                    @else
                                        <span class="badge bg-light text-secondary border">Usia Dini / NISN: {{ $pemain?->nisn ?? '-' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                    {{ $r->posisi_bermain ?: ($pemain ? $pemain->posisi_utama : 'PEMAIN') }}
                                </span>
                            </td>
                            <td>
                                @if($pemain && $pemain->tanggal_lahir)
                                    <div>{{ \Carbon\Carbon::parse($pemain->tanggal_lahir)->translatedFormat('d M Y') }}</div>
                                    <small class="text-muted">{{ $usia }} tahun ({{ $pemain->tempat_lahir ?: '-' }})</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $r->tinggi_cm ? $r->tinggi_cm . ' cm' : '-' }}</div>
                                <small class="text-muted">{{ $r->berat_kg ? $r->berat_kg . ' kg' : '-' }}</small>
                            </td>
                            <td class="text-center">
                                @if($r->is_captain)
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-crown me-1"></i> KAPTEN (C)</span>
                                @elseif($r->is_libero)
                                    <span class="badge bg-info text-dark px-2 py-1"><i class="fa-solid fa-shield me-1"></i> LIBERO (L)</span>
                                @else
                                    <span class="text-muted small">Pemain Inti/Cadangan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($r->status_verifikasi == 'VERIFIED')
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                @elseif($r->status_verifikasi == 'REJECTED')
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-clock me-1"></i> Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('tim.pemain.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemain ini dari roster tim?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari Roster">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users fa-3x mb-3 text-secondary"></i>
                                <div>Belum ada pemain yang didaftarkan ke roster tim ini.</div>
                                <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalTambahPemain">
                                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Pemain Sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PEMAIN -->
<div class="modal fade" id="modalTambahPemain" tabindex="-1" aria-labelledby="modalTambahPemainLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tim.pemain.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahPemainLabel">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>Tambah Pemain ke Roster Tim
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info small d-flex align-items-center mb-3">
                        <i class="fa-solid fa-circle-info fa-lg me-2"></i>
                        <div>
                            <strong>Catatan Regulasi PBVSI Usia Dini:</strong> Jika atlet belum memiliki KTP / NIK, NIK dapat dikosongkan atau diisi <code>00000000000</code>. Sistem akan memverifikasi silang berdasarkan Nama Lengkap dan Tanggal Lahir.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Pemain <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama sesuai Akta / Ijazah" required value="{{ old('nama_lengkap') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nomor Punggung (1-99) <span class="text-danger">*</span></label>
                            <input type="number" name="nomor_punggung" class="form-control" min="1" max="99" placeholder="Contoh: 7" required value="{{ old('nomor_punggung') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Posisi Bermain <span class="text-danger">*</span></label>
                            <select name="posisi_bermain" class="form-select" required>
                                <option value="">-- Pilih Posisi --</option>
                                <option value="OUTSIDE_HITTER">Outside Hitter (Open Spiker)</option>
                                <option value="OPPOSITE">Opposite (All Round)</option>
                                <option value="MIDDLE_BLOCKER">Middle Blocker (Quicker)</option>
                                <option value="SETTER">Setter (Tosser)</option>
                                <option value="LIBERO">Libero (Defensive Specialist)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" class="form-control" required value="{{ old('tanggal_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota / Kabupaten Lahir" value="{{ old('tempat_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK (KTP / KK)</label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit NIK (Boleh kosong utk usia dini)" value="{{ old('nik') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NISN (Sekolah)</label>
                            <input type="text" name="nisn" class="form-control" placeholder="Nomor Induk Siswa Nasional" value="{{ old('nisn') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_cm" class="form-control" min="120" max="230" placeholder="Contoh: 185" value="{{ old('tinggi_cm') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Badan (kg)</label>
                            <input type="number" name="berat_kg" class="form-control" min="30" max="150" placeholder="Contoh: 75" value="{{ old('berat_kg') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Daftarkan ke Roster
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
