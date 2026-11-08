@extends('layouts.app')

@section('title', 'Portal ' . ($role?->display_name ?? 'Anggota') . ' - PBVSI Platform')

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house me-1"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="#!">Portal Anggota</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $role?->display_name ?? 'Anggota Terdaftar' }}</li>
        </ol>
    </nav>
</div>

<!-- ALERT JIKA ADA PESAN DITOLAK -->
@if($errors->has('access_denied'))
<div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
    <i class="fa-solid fa-shield-halved fa-xl me-3 text-warning"></i>
    <div>
        <strong>Akses Dibatasi:</strong> {{ $errors->first('access_denied') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- USER HERO BANNER -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center shadow" style="width: 72px; height: 72px; font-size: 2rem;">
                    <i class="fa-solid fa-user-check text-primary"></i>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-white">{{ $user->name }}</h3>
                    <span class="badge bg-primary px-3 py-1 fs-6">
                        <i class="fa-solid fa-id-badge me-1"></i>
                        {{ strtoupper($role?->display_name ?? 'ANGGOTA PBVSI') }}
                    </span>
                    <span class="badge bg-success px-2 py-1">
                        <i class="fa-solid fa-circle-check me-1"></i> AKUN AKTIF RESMI
                    </span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                    <div><i class="fa-solid fa-fingerprint text-info me-1"></i> ID PBVSI: <strong>{{ $user->username }}</strong></div>
                    <div><i class="fa-solid fa-envelope text-warning me-1"></i> {{ $user->email }}</div>
                    <div><i class="fa-solid fa-building-flag text-success me-1"></i> Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia</div>
                </div>
            </div>
            <div class="col-md-auto text-md-end">
                <a href="{{ route('akun.saya') }}" class="btn btn-outline-light btn-sm px-3 py-2 fw-semibold">
                    <i class="fa-solid fa-user-gear me-1"></i> Pengaturan Akun
                </a>
            </div>
        </div>
    </div>
</div>

<!-- CENTRAL NOTICE: PORTAL DALAM TAHAP PENGEMBANGAN -->
<div class="card border-0 shadow-sm mb-4 text-center p-4 p-md-5">
    <div class="card-body max-w-750 mx-auto">
        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-4 shadow-sm" style="width: 90px; height: 90px;">
            <i class="fa-solid fa-compass-drafting fa-3x"></i>
        </div>
        <h3 class="fw-bold text-dark mb-2">Portal Khusus {{ $role?->display_name ?? 'Anggota' }} Sedang Dalam Pengembangan</h3>
        <p class="text-muted lead fs-6 mb-4">
            Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (<strong>PP PBVSI</strong>) sedang mempersiapkan portal mandiri khusus untuk <strong>{{ $role?->display_name ?? 'Anggota' }}</strong>. 
            Modul pengelolaan data pusat (Master Data, Pengaturan Wilayah, dan RBAC) saat ini dikunci dan dibatasi khusus untuk Administrator PP PBVSI.
        </p>

        <div class="p-3 bg-light rounded-3 text-start mb-4 border">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-rocket text-primary me-2"></i>Fitur Portal yang Sedang Disiapkan untuk Anda:</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-id-card fa-lg text-primary mt-1"></i>
                        <div>
                            <strong class="d-block text-dark small">KTA & Paspor Digital PBVSI</strong>
                            <span class="text-muted small">Kartu identitas resmi dengan QR Code verifikasi nasional.</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-trophy fa-lg text-warning mt-1"></i>
                        <div>
                            <strong class="d-block text-dark small">Riwayat Pertandingan & Karir</strong>
                            <span class="text-muted small">Rekam jejak resmi partisipasi Kejurnas, Livoli, dan turnamen resmi.</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-certificate fa-lg text-success mt-1"></i>
                        <div>
                            <strong class="d-block text-dark small">Lisensi & Sertifikasi Digital</strong>
                            <span class="text-muted small">Validasi lisensi kepelatihan atau perwasitan berskala nasional & FIVB.</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-pen-to-square fa-lg text-info mt-1"></i>
                        <div>
                            <strong class="d-block text-dark small">Pembaruan Berkas Mandiri</strong>
                            <span class="text-muted small">Layanan permohonan koreksi biodata dan dokumen secara mandiri.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('akun.saya') }}" class="btn btn-primary px-4 fw-semibold shadow-sm">
                <i class="fa-solid fa-user-pen me-1"></i> Kelola Profil Akun Saya
            </a>
            <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary px-3 fw-semibold">
                <i class="fa-solid fa-shield-halved me-1"></i> Kebijakan Privasi
            </a>
            <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary px-3 fw-semibold">
                <i class="fa-solid fa-file-contract me-1"></i> Syarat & Ketentuan
            </a>
        </div>
    </div>
</div>

<!-- INFORMASI PROFIL SAAT INI -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-circle-user text-primary me-2"></i>Data Akun Pengguna Terdaftar
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 160px;">ID Pengguna / Username</td>
                        <td><code>{{ $user->username }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Lengkap</td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Peran / Role</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $role?->display_name ?? 'Anggota' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat Email</td>
                        <td class="text-dark">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nomor WhatsApp / HP</td>
                        <td class="text-dark">{{ $user->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Akun</td>
                        <td>
                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Aktif Terverifikasi</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-headset text-info me-2"></i>Bantuan & Layanan PP PBVSI
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small leading-relaxed">
                    Jika Anda memiliki pertanyaan mengenai keabsahan data, jadwal penugasan pertandingan, atau kebutuhan administrasi kompetisi resmi, silakan menghubungi narahubung Sekretariat PP PBVSI:
                </p>
                <div class="p-3 bg-light rounded-3 small border">
                    <div class="fw-bold text-dark mb-1">PENGURUS PUSAT PERSATUAN BOLA VOLI SELURUH INDONESIA</div>
                    <div class="text-muted mb-1"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Padepokan Voli Jenderal Polisi Kunarto, Sentul, Bogor, Jawa Barat.</div>
                    <div class="text-muted mb-1"><i class="fa-solid fa-envelope me-1 text-primary"></i> Email: <a href="mailto:sekretariat@pbvsi.or.id" class="text-decoration-none">sekretariat@pbvsi.or.id</a></div>
                    <div class="text-muted"><i class="fa-solid fa-globe me-1 text-success"></i> Website: <a href="https://pbvsi.or.id" target="_blank" class="text-decoration-none">pbvsi.or.id</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
