@extends('layouts.app')

@section('title', 'Detail Tim: ' . $team->nama_tim . ' - ' . $edisi->nama_edisi)

@section('content')
<!-- NOTIFIKASI -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- BREADCRUMB -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}">
                    <i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}">
                    {{ $turnamen->nama_turnamen }} ({{ $tahun }})
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <span class="badge bg-primary px-2 py-1">{{ $team->nama_tim }}</span>
            </li>
        </ol>
    </nav>

    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Tim
        </a>
        @if(!$team->trashed())
        <a href="{{ route('kejuaraan.tim.edit', ['slug' => $slug, 'tahun' => $tahun, 'id' => $team->encrypted_id]) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-shuffle me-1"></i> Atur Pul & Status
        </a>
        @endif
    </div>
</div>

<!-- HEADER CARD TIM -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($team->logo_url)
                    <div class="rounded border p-2 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                        <img src="{{ $team->logo_url }}" alt="{{ $team->nama_tim }}" class="img-fluid object-fit-contain" style="max-height: 60px; max-width: 60px;">
                    </div>
                @else
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                        <i class="fa-solid fa-shield-halved fa-2x"></i>
                    </div>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0">{{ $team->nama_tim }}</h4>
                        <span class="badge bg-light text-dark border font-monospace">{{ $team->kode_tim ?: '-' }}</span>
                        @if($team->kategori_gender == 'PA')
                            <span class="badge bg-info-subtle text-info border"><i class="fa-solid fa-mars me-1"></i>Putra (PA)</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border"><i class="fa-solid fa-venus me-1"></i>Putri (PI)</span>
                        @endif
                        @if($team->pool_grup)
                            <span class="badge bg-secondary-subtle text-dark border fw-bold">Pul {{ $team->pool_grup }}</span>
                        @endif
                        @if($team->trashed())
                            <span class="badge bg-danger">Terhapus (Soft Delete)</span>
                        @endif
                    </div>
                    <div class="small text-muted">
                        @if($team->pengprov)
                            <i class="fa-solid fa-flag text-secondary me-1"></i> Kontingen: <strong>{{ $team->pengprov->nama_pengprov }}</strong>
                        @elseif($team->klub)
                            <i class="fa-solid fa-shield-halved text-secondary me-1"></i> Klub: <strong>{{ $team->klub->nama_klub }}</strong> ({{ $team->klub->pengprov?->nama_pengprov ?: '-' }})
                        @endif
                        <span class="mx-2">&bull;</span>
                        <i class="fa-solid fa-trophy text-warning me-1"></i> {{ $edisi->nama_edisi }}
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="text-md-end">
                    <div class="small text-muted">Status Partisipasi:</div>
                    <span class="badge {{ $team->is_active ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-1">
                        {{ $team->is_active ? 'Tim Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ROW INFO JERSEY & DOKUMEN TIM -->
        <div class="row g-3 mt-3 pt-3 border-top">
            <div class="col-md-7">
                <div class="small fw-bold text-muted text-uppercase mb-2">
                    <i class="fa-solid fa-shirt me-1 text-primary"></i> Seragam Jersey Pertandingan Bolavoli:
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($team->warna_jersey_1)
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <strong>Jersey 1:</strong> {{ $team->warna_jersey_1 }}
                        </span>
                    @endif
                    @if($team->warna_jersey_2)
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <strong>Jersey 2:</strong> {{ $team->warna_jersey_2 }}
                        </span>
                    @endif
                    @if($team->warna_jersey_3)
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <strong>Jersey 3:</strong> {{ $team->warna_jersey_3 }}
                        </span>
                    @endif
                    @if($team->warna_jersey_libero_1)
                        <span class="badge bg-warning-subtle text-dark border px-3 py-2">
                            <strong>Libero 1:</strong> {{ $team->warna_jersey_libero_1 }}
                        </span>
                    @endif
                    @if($team->warna_jersey_libero_2)
                        <span class="badge bg-warning-subtle text-dark border px-3 py-2">
                            <strong>Libero 2:</strong> {{ $team->warna_jersey_libero_2 }}
                        </span>
                    @endif
                    @if(!$team->warna_jersey_1 && !$team->warna_jersey_2)
                        <span class="text-muted small">Warna seragam jersey belum diisi.</span>
                    @endif
                </div>
            </div>

            <div class="col-md-5">
                <div class="small fw-bold text-muted text-uppercase mb-2">
                    <i class="fa-solid fa-users-gear me-1 text-primary"></i> Official Tim Terdaftar:
                </div>
                @forelse($team->officials as $off)
                    <div class="small mb-1">
                        <strong>{{ $off->label_jabatan }}:</strong> {{ $off->nama_lengkap }}
                    </div>
                @empty
                    <span class="small text-muted">Belum ada data official tim yang didaftarkan.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TABEL ROSTER ATLET -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-users text-primary me-2"></i> Roster Atlet Terdaftar ({{ $team->roster->count() }} Pemain)
            </h5>
            <small class="text-muted">Kuota maksimal edisi: {{ $edisi->max_kuota_pemain ?? 14 }} atlet</small>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3 text-center" style="width: 70px;">Jersey</th>
                        <th>Nama Pemain Bolavoli</th>
                        <th class="text-center" style="width: 130px;">Posisi</th>
                        <th class="text-center" style="width: 140px;">Fisik (TB/BB)</th>
                        <th class="text-center" style="width: 150px;">Spike / Block</th>
                        <th class="text-center" style="width: 120px;">Peran</th>
                        <th class="text-center pe-3" style="width: 130px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($team->roster as $r)
                    <tr>
                        <td class="ps-3 text-center">
                            <span class="badge bg-primary fs-6 px-3 py-1 font-monospace">
                                {{ $r->nomor_punggung }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $r->pemain?->nama_lengkap ?: '-' }}</div>
                            <div class="small text-muted">
                                ID PBVSI: {{ $r->pemain?->pbvsi_player_id ?: '-' }}
                                @if($r->nama_jersey)
                                    &bull; Nama Punggung: <strong>{{ $r->nama_jersey }}</strong>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-dark border px-2 py-1">
                                {{ $r->posisi_bermain }}
                            </span>
                        </td>
                        <td class="text-center small">
                            {{ $r->tinggi_cm ? $r->tinggi_cm . ' cm' : '-' }} / {{ $r->berat_kg ? $r->berat_kg . ' kg' : '-' }}
                        </td>
                        <td class="text-center small text-muted">
                            {{ $r->spike_reach_cm ? $r->spike_reach_cm . ' cm' : '-' }} / {{ $r->block_reach_cm ? $r->block_reach_cm . ' cm' : '-' }}
                        </td>
                        <td class="text-center">
                            @if($r->is_captain)
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>Kapten</span>
                            @elseif($r->is_libero)
                                <span class="badge bg-info-subtle text-info border">Libero</span>
                            @else
                                <span class="text-muted small">Pemain</span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            @if($r->status_verifikasi === 'VERIFIED')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fa-solid fa-check me-1"></i> Terverifikasi
                                </span>
                            @elseif($r->status_verifikasi === 'REJECTED')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fa-solid fa-xmark me-1"></i> Ditolak
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="fa-solid fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users-slash fa-2x text-secondary opacity-50 mb-2 d-block"></i>
                            <div class="fw-bold">Belum ada roster atlet yang didaftarkan untuk tim ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
