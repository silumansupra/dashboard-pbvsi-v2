@extends('layouts.app')

@section('title', 'Riwayat & Roster Tim - ' . $namaTim)

@section('content')
<!-- BREADCRUMB & HEADER -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.klub') }}">Data Klub & Tim</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">{{ $namaTim }}</span></li>
        </ol>
    </nav>
    <div>
        <a href="{{ route('master.klub', ['tipe' => $tipe]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm bg-white">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Tim
        </a>
    </div>
</div>

<!-- HEADER CARD TIM / KLUB -->
<div class="card shadow-sm border-0 mb-4 overflow-hidden">
    <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-lg bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm overflow-hidden p-1" style="width: 70px; height: 70px; min-width: 70px;">
                    @if($isKlub && $tim->logo_url)
                        <img src="{{ $tim->logo_url }}" alt="{{ $namaTim }}" class="img-fluid object-fit-contain" style="max-height: 60px; max-width: 60px;" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-shield-halved fa-2x text-primary\'></i>';">
                    @elseif($isKlub)
                        <i class="fa-solid fa-shield-halved fa-2x"></i>
                    @else
                        <i class="fa-solid fa-flag fa-2x"></i>
                    @endif
                </div>
                <div>
                    <div class="d-flex gap-2 align-items-center flex-wrap mb-1">
                        <h2 class="h3 fw-bold mb-0 text-white">{{ $namaTim }}</h2>
                        @if($isKlub)
                            <span class="badge bg-info text-dark px-2 py-1 font-monospace">{{ $kodeTim }}</span>
                            <span class="badge bg-light text-primary px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>KLUB RESMI PBVSI</span>
                        @else
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-flag me-1"></i>KONTINGEN DAERAH (PENGPROV)</span>
                        @endif
                    </div>
                    <p class="mb-0 text-white-50 small">
                        <i class="fa-solid fa-location-dot me-1"></i> {{ $asalWilayah }}
                        @if($isKlub && $tim->pemilik_klub)
                            &bull; <i class="fa-solid fa-user-tie me-1 ms-2"></i> Pemilik: {{ $tim->pemilik_klub }}
                        @endif
                        @if(!$isKlub && $tim->ketum)
                            &bull; <i class="fa-solid fa-user-tie me-1 ms-2"></i> Ketum: {{ $tim->ketum }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="d-flex gap-2">
                <span class="badge bg-white-subtle text-white border border-white-50 px-3 py-2 fs-6">
                    <i class="fa-solid fa-trophy me-1 text-warning"></i> {{ $turnamenList->count() }} Edisi Turnamen
                </span>
            </div>
        </div>

        <div class="row g-3 mt-3 pt-3 border-top border-white-50 small">
            <div class="col-md-4">
                <span class="text-white-50">Total Kejuaraan Diikuti:</span>
                <div class="fw-bold fs-5 text-white">{{ $turnamenList->count() }} Kali Bertanding</div>
            </div>
            <div class="col-md-4">
                <span class="text-white-50">Total Roster Atlet Terdaftar:</span>
                <div class="fw-bold fs-5 text-white">{{ $totalPemainUnik }} Atlet Unik</div>
            </div>
            <div class="col-md-4">
                <span class="text-white-50">Total Official & Pelatih:</span>
                <div class="fw-bold fs-5 text-white">{{ $totalOfficialUnik }} Official Terdokumentasi</div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION RIWAYAT TURNAMEN -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat Partisipasi Turnamen & Kejuaraan
    </h5>
    <span class="badge bg-primary rounded-pill px-3 py-2">
        {{ $turnamenList->count() }} Partisipasi Tercatat
    </span>
</div>

@forelse($turnamenList as $index => $partisipasi)
@php
    $edisi = $partisipasi->edisi;
    $turnamen = $edisi?->turnamen;
    $rosterAtlet = $partisipasi->roster;
    $officials = $partisipasi->officials;
@endphp
<div class="card shadow-sm border-0 mb-4">
    <!-- HEADER PARTISIPASI TURNAMEN -->
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-3">
            @if($partisipasi->logo_url)
                <div class="avatar-md bg-white border rounded-3 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                    <img src="{{ $partisipasi->logo_url }}" alt="{{ $partisipasi->nama_tim }}" class="img-fluid object-fit-contain" style="max-height: 40px; max-width: 40px;" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-trophy fa-lg text-primary\'></i>';">
                </div>
            @else
                <div class="avatar-md bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center p-2" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-trophy fa-lg"></i>
                </div>
            @endif
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="fw-bold text-dark mb-0">
                        {{ $turnamen->nama_turnamen ?? ($partisipasi->nama_tim . ' Tournament') }}
                    </h5>
                    <span class="badge bg-primary px-2 py-1 fs-6">Tahun {{ $edisi->tahun ?? '-' }}</span>
                    @if($partisipasi->kategori_gender == 'PA')
                        <span class="badge bg-info-subtle text-info border px-2 py-1"><i class="fa-solid fa-mars me-1"></i>Putra (PA)</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border px-2 py-1"><i class="fa-solid fa-venus me-1"></i>Putri (PI)</span>
                    @endif
                </div>
                <div class="small text-muted mt-1">
                    <i class="fa-solid fa-tag me-1"></i> Terdaftar sebagai: <strong>{{ $partisipasi->nama_tim }}</strong>
                    @if($partisipasi->kode_tim) &bull; Kode Tim: <code>{{ $partisipasi->kode_tim }}</code> @endif
                    @if($partisipasi->warna_jersey_1) &bull; Jersey: <span class="badge bg-light text-dark border">{{ $partisipasi->warna_jersey_1 }}</span> @endif
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            @if($turnamen && $edisi)
                <a href="{{ route('kejuaraan.tahun', ['slug' => $turnamen->slug, 'tahun' => $edisi->tahun]) }}" 
                   class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" 
                   target="_blank"
                   title="Buka Panel Kejuaraan">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Panel Turnamen
                </a>
            @endif
        </div>
    </div>

    <div class="card-body p-4">
        <!-- 1. DAFTAR OFFICIAL & PELATIH -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="fa-solid fa-user-tie text-secondary me-1"></i> Official & Staf Kepelatihan ({{ $officials->count() }})
            </h6>

            @if($officials->isNotEmpty())
            <div class="row g-2">
                @foreach($officials as $off)
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                        <div class="avatar-sm bg-white text-secondary rounded-circle d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px; min-width: 36px;">
                            @if(str_contains($off->jabatan, 'COACH'))
                                <i class="fa-solid fa-whistle text-primary"></i>
                            @elseif(str_contains($off->jabatan, 'MANAGER'))
                                <i class="fa-solid fa-briefcase text-dark"></i>
                            @elseif(str_contains($off->jabatan, 'THERAPIST') || str_contains($off->jabatan, 'MEDIS'))
                                <i class="fa-solid fa-kit-medical text-danger"></i>
                            @else
                                <i class="fa-solid fa-user-tie text-secondary"></i>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate small" title="{{ $off->nama_lengkap }}">{{ $off->nama_lengkap }}</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                <span class="badge bg-secondary-subtle text-secondary px-1">{{ $off->label_jabatan }}</span>
                                @if($off->nomor_lisensi) &bull; Lic: {{ $off->nomor_lisensi }} @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-muted small fst-italic p-2 bg-light rounded border">
                <i class="fa-solid fa-info-circle me-1"></i> Tidak ada data staf official spesifik yang tercatat untuk edisi ini.
            </div>
            @endif
        </div>

        <!-- 2. DAFTAR ROSTER ATLET -->
        <div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-users text-primary me-1"></i> Roster Atlet ({{ $rosterAtlet->count() }} Pemain)
                </h6>
                <span class="small text-muted">Berdasarkan Formulir Pendaftaran Resmi PBVSI</span>
            </div>

            @if($rosterAtlet->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-sm">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3 text-center" style="width: 70px;">Jersey</th>
                            <th>Nama Atlet</th>
                            <th>ID Pemain PBVSI</th>
                            <th>Posisi</th>
                            <th>Fisik (TB / BB)</th>
                            <th>Jangkauan (Spike / Block)</th>
                            <th class="text-center" style="width: 100px;">Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rosterAtlet->sortBy('nomor_punggung') as $roster)
                        <tr>
                            <td class="ps-3 text-center">
                                <span class="badge bg-primary fs-6 px-2 py-1 font-monospace">{{ $roster->nomor_punggung }}</span>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $roster->pemain?->nama_lengkap ?? '-' }}</strong>
                                @if($roster->nama_jersey)
                                    <div class="small text-muted">Jersey: {{ $roster->nama_jersey }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $roster->pemain?->pbvsi_player_id ?? '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($roster->posisi_bermain) {
                                        'OH' => 'bg-primary-subtle text-primary border',
                                        'OP' => 'bg-success-subtle text-success border',
                                        'MB' => 'bg-warning-subtle text-warning border',
                                        'S'  => 'bg-info-subtle text-info border',
                                        'L'  => 'bg-danger-subtle text-danger border',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1">{{ $roster->posisi_bermain }}</span>
                            </td>
                            <td>
                                <span class="small text-dark">
                                    {{ $roster->tinggi_cm ? $roster->tinggi_cm . ' cm' : '-' }} / 
                                    {{ $roster->berat_kg ? $roster->berat_kg . ' kg' : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    {{ $roster->spike_reach_cm ? $roster->spike_reach_cm . ' cm' : '-' }} / 
                                    {{ $roster->block_reach_cm ? $roster->block_reach_cm . ' cm' : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($roster->is_libero || $roster->posisi_bermain === 'L')
                                    <span class="badge bg-danger-subtle text-danger border px-2 py-1">LIBERO</span>
                                @elseif($roster->is_captain)
                                    <span class="badge bg-warning-subtle text-warning border px-2 py-1">KAPTEN</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1">Pemain</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 bg-light rounded border text-muted">
                <i class="fa-solid fa-users-slash fa-2x mb-2 d-block opacity-50"></i>
                <div class="small">Belum ada data roster pemain tercatat pada edisi ini.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="card shadow-sm border-0 py-5 text-center">
    <div class="card-body">
        <i class="fa-solid fa-trophy fa-3x text-secondary mb-3 d-block opacity-50"></i>
        <h5 class="fw-bold text-dark">Belum Ada Riwayat Turnamen</h5>
        <p class="text-muted col-md-6 mx-auto small">
            Tim atau Klub ini belum tercatat mengikuti kejuaraan atau turnamen PBVSI resmi dalam sistem.
        </p>
    </div>
</div>
@endforelse

@endsection
