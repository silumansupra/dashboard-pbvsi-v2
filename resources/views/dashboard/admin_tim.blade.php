@extends('layouts.app')

@section('title', 'Dashboard Tim - ' . $activeTeam->nama_tim . ' (' . $activeTeam->kategori_gender . ')')

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $activeTeam->nama_tim }} ({{ $activeTeam->kategori_gender }})</li>
        </ol>
    </nav>
</div>

<!-- ALERT NOTIFIKASI -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- TEAM HERO BANNER -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center shadow overflow-hidden" style="width: 72px; height: 72px; font-size: 2rem; font-weight: bold;">
                    @if($activeTeam->logo_url)
                        <img src="{{ $activeTeam->logo_url }}" alt="{{ $activeTeam->nama_tim }}" class="rounded-circle w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/no-logo.svg') }}';">
                    @else
                        <img src="{{ asset('images/no-logo.svg') }}" alt="{{ $activeTeam->nama_tim }}" class="rounded-circle w-100 h-100 object-fit-cover p-2">
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-white">{{ $activeTeam->nama_tim }}</h3>
                    <span class="badge {{ $activeTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }} px-3 py-1 fs-6">
                        <i class="fa-solid {{ $activeTeam->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                        {{ $activeTeam->kategori_gender == 'PA' ? 'TIM PUTRA (PA)' : 'TIM PUTRI (PI)' }}
                    </span>
                    @if($activeTeam->status_verifikasi == 'VERIFIED')
                        <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> VERIFIED PBVSI</span>
                    @elseif($activeTeam->status_verifikasi == 'SUBMITTED')
                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-clock me-1"></i> MENUNGGU VERIFIKASI</span>
                    @else
                        <span class="badge bg-secondary px-2 py-1"><i class="fa-solid fa-file-pen me-1"></i> DRAFT</span>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                    @if($activeTeam->edisi && $activeTeam->edisi->turnamen)
                        <div><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $activeTeam->edisi->turnamen->nama_turnamen }} ({{ $activeTeam->edisi->tahun_edisi }})</div>
                    @endif
                    @if($activeTeam->pengprov)
                        <div><i class="fa-solid fa-location-dot text-info me-1"></i> {{ $activeTeam->pengprov->nama_pengprov }}</div>
                    @endif
                    <div><i class="fa-solid fa-user-tie text-success me-1"></i> Admin: <strong>{{ $user->name }}</strong></div>
                </div>
            </div>

            <!-- TEAM SWITCHER JIKA MULTI-TIM -->
            @if($teams->count() > 1)
            <div class="col-12 col-md-auto text-md-end">
                <div class="small text-white-50 mb-1"><i class="fa-solid fa-repeat me-1"></i> Ganti Tim Aktif:</div>
                <div class="btn-group shadow-sm" role="group">
                    @foreach($teams as $t)
                        <a href="{{ route('tim.switch', $t->id) }}" 
                           class="btn btn-sm {{ $t->id == $activeTeam->id ? ($t->kategori_gender == 'PA' ? 'btn-primary' : 'btn-danger') : 'btn-outline-light' }} px-3 py-2 fw-semibold">
                            <i class="fa-solid {{ $t->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                            {{ $t->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- STAT METRICS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Roster Pemain (O-2)</span>
                    <span class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-users"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark mb-1">{{ $roster->count() }} <span class="fs-6 text-muted fw-normal">/ 14</span></div>
                <div class="progress" style="height: 5px;">
                    @php $pct = min(100, ($roster->count() / 14) * 100); @endphp
                    <div class="progress-bar {{ $roster->count() >= 10 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $pct }}%"></div>
                </div>
                <div class="small mt-2 {{ $roster->count() >= 10 ? 'text-success' : 'text-danger' }}">
                    <i class="fa-solid {{ $roster->count() >= 10 ? 'fa-check' : 'fa-triangle-exclamation' }} me-1"></i>
                    {{ $roster->count() >= 10 ? 'Memenuhi kuota (10-14)' : 'Minimal 10 pemain O-2' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Kapten Tim</span>
                    <span class="avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-copyright"></i>
                    </span>
                </div>
                @if($captain)
                    <div class="fs-5 fw-bold text-dark text-truncate mb-0" title="{{ $captain->pemain->nama_lengkap }}">{{ $captain->pemain->nama_lengkap }}</div>
                    <div class="text-muted small">Jersey #{{ $captain->nomor_punggung }} ({{ $captain->posisi_bermain }})</div>
                    <div class="small mt-2 text-success"><i class="fa-solid fa-circle-check me-1"></i> Kapten Terpilih</div>
                @else
                    <div class="fs-6 fw-bold text-danger mb-0">Belum Ditentukan</div>
                    <div class="text-muted small">-</div>
                    <div class="small mt-2 text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Wajib pilih 1 kapten</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Pemain Libero</span>
                    <span class="avatar-sm bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-shield-cat"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark mb-1">{{ $liberos->count() }} <span class="fs-6 text-muted fw-normal">/ max 2</span></div>
                <div class="small text-muted text-truncate">
                    @forelse($liberos as $lib)
                        <span class="badge bg-danger-subtle text-danger me-1">#{{ $lib->nomor_punggung }} {{ $lib->pemain->nama_lengkap }}</span>
                    @empty
                        <span class="text-muted small">Belum ada libero</span>
                    @endforelse
                </div>
                <div class="small mt-2 text-info"><i class="fa-solid fa-info-circle me-1"></i> Maks. 2 libero O-2</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Official & Pelatih</span>
                    <span class="avatar-sm bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark mb-1">{{ $officials->count() }} <span class="fs-6 text-muted fw-normal">Orang</span></div>
                <div class="small text-muted text-truncate">
                    @php $hc = $officials->where('jabatan', 'HEAD_COACH')->first(); @endphp
                    Head Coach: <strong>{{ $hc ? $hc->nama_lengkap : 'Belum didaftarkan' }}</strong>
                </div>
                <div class="small mt-2 text-success"><i class="fa-solid fa-id-card me-1"></i> Manajer, Pelatih, Medis</div>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('tim.profil') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100 hover-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    <strong class="d-block">Profil & Jersey</strong>
                    <small class="text-muted">Kostum 1, 2, 3 & Logo</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('tim.pemain') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100 hover-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <strong class="d-block">Data Atlet Tim</strong>
                    <small class="text-muted">Kelola Atlet & NIK</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('tim.official') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100 hover-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <strong class="d-block">Official & Pelatih</strong>
                    <small class="text-muted">Lisensi & Staff Tim</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('tim.roster') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100 hover-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div>
                    <strong class="d-block">Formulir Roster O-2</strong>
                    <small class="text-muted">Susunan & Cetak PDF</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- PREVIEW TABLES: ROSTER & OFFICIAL -->
<div class="row g-4">
    <!-- TABEL ROSTER O-2 -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users text-primary me-2"></i>Susunan Skuad Roster O-2 ({{ $roster->count() }})</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('tim.roster') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Roster
                    </a>
                    <a href="{{ route('tim.roster.print_o2') }}" target="_blank" class="btn btn-sm btn-danger">
                        <i class="fa-solid fa-print me-1"></i> Cetak O-2
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 60px;">Jersey</th>
                            <th>Nama Atlet</th>
                            <th>Posisi</th>
                            <th>Peran Khusus</th>
                            <th class="text-center">TB / BB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roster as $idx => $r)
                        <tr>
                            <td class="text-center text-muted small">{{ $idx + 1 }}</td>
                            <td class="text-center">
                                <span class="badge bg-dark rounded-circle px-2 py-1 fs-6">{{ $r->nomor_punggung }}</span>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $r->pemain->nama_lengkap }}</strong>
                                <small class="text-muted">NIK: {{ $r->pemain->nik ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $r->posisi_bermain }}</span>
                            </td>
                            <td>
                                @if($r->is_captain)
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-copyright me-1"></i> KAPTEN</span>
                                @elseif($r->is_libero)
                                    <span class="badge bg-danger"><i class="fa-solid fa-shield-cat me-1"></i> LIBERO</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center small">
                                {{ $r->pemain->tinggi_badan ? $r->pemain->tinggi_badan . ' cm' : '-' }} /
                                {{ $r->pemain->berat_badan ? $r->pemain->berat_badan . ' kg' : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-users-slash fa-2x mb-2 text-secondary"></i>
                                <p class="mb-0">Belum ada atlet yang dimasukkan ke susunan roster O-2.</p>
                                <a href="{{ route('tim.roster') }}" class="btn btn-sm btn-primary mt-2">Susun Roster Sekarang</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABEL OFFICIAL TIM -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-tie text-success me-2"></i>Official Tim ({{ $officials->count() }})</h6>
                <a href="{{ route('tim.official') }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($officials as $off)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="badge bg-success-subtle text-success small mb-1">{{ str_replace('_', ' ', $off->jabatan) }}</span>
                            <strong class="d-block text-dark">{{ $off->nama_lengkap }}</strong>
                            <small class="text-muted">Lisensi: {{ $off->nomor_lisensi ?: '-' }}</small>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted">
                        <i class="fa-solid fa-user-xmark fa-2x mb-2 text-secondary"></i>
                        <p class="mb-0">Belum ada data official.</p>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
