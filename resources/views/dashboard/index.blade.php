@extends('layouts.app')

@section('title', 'Dashboard Utama PP PBVSI')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h1 class="h3 mb-1"><strong>Dashboard Utama</strong> PBVSI Platform</h1>
        <p class="text-muted">Pusat kendali master data, verifikasi organisasi, dan seluruh turnamen Bolavoli nasional.</p>
    </div>
</div>

<!-- STATISTIK MASTER CARDS -->
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted">Bank Pemain Nasional</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary bg-primary-subtle p-2 rounded">
                            <i class="align-middle fa-solid fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-2">{{ number_format($stats['total_pemain']) }}</h1>
                <div class="mb-0">
                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Terverifikasi</span>
                    <span class="text-muted">Se-Indonesia</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted">Pengurus Provinsi</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-success bg-success-subtle p-2 rounded">
                            <i class="align-middle fa-solid fa-map-location-dot fa-lg"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-2">{{ $stats['total_pengprov'] }}</h1>
                <div class="mb-0">
                    <span class="text-success"><i class="fa-solid fa-check-double"></i> 37 Provinsi</span>
                    <span class="text-muted">Terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted">Total Pengguna (SSO)</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-warning bg-warning-subtle p-2 rounded">
                            <i class="align-middle fa-solid fa-user-shield fa-lg"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-2">{{ number_format($stats['total_users']) }}</h1>
                <div class="mb-0">
                    <span class="text-warning"><i class="fa-solid fa-key"></i> 1 Kredensial</span>
                    <span class="text-muted">Untuk semua event</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0">
                        <h5 class="card-title text-muted">Turnamen Berjalan</h5>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-danger bg-danger-subtle p-2 rounded">
                            <i class="align-middle fa-solid fa-trophy fa-lg"></i>
                        </div>
                    </div>
                </div>
                <h1 class="mt-1 mb-2">{{ $stats['turnamen_aktif'] }}</h1>
                <div class="mb-0">
                    <span class="badge bg-danger">Musim 2026</span>
                    <span class="text-muted">Open Registrasi</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SINGLE PANEL KEJURNAS UMUR (U-16, U-17, U-18, U-19) -->
<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><strong>Panel Terpadu: Kejurnas Kelompok Umur</strong></h4>
                <small class="text-muted">Manajemen turnamen umur dalam satu pintu tanpa duplikasi database.</small>
            </div>
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Buka Edisi Kejurnas Baru
            </button>
        </div>
    </div>

    @foreach($kejurnasEdisi as $ed)
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100 shadow-sm border-0 border-top border-4
            @if($ed->turnamen->kelompok_umur == 'U16') border-info
            @elseif($ed->turnamen->kelompok_umur == 'U17') border-success
            @elseif($ed->turnamen->kelompok_umur == 'U18') border-warning
            @elseif($ed->turnamen->kelompok_umur == 'U19') border-danger
            @endif">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge
                        @if($ed->turnamen->kelompok_umur == 'U16') bg-info
                        @elseif($ed->turnamen->kelompok_umur == 'U17') bg-success
                        @elseif($ed->turnamen->kelompok_umur == 'U18') bg-warning text-dark
                        @elseif($ed->turnamen->kelompok_umur == 'U19') bg-danger
                        @endif">
                        {{ $ed->turnamen->kelompok_umur }} - TAHUN {{ $ed->tahun }}
                    </span>
                    <span class="badge {{ $ed->status_edisi == 'REGISTRASI' ? 'bg-primary' : ($ed->status_edisi == 'RUNNING' ? 'bg-success' : 'bg-secondary') }}">
                        {{ $ed->status_edisi }}
                    </span>
                </div>
                <h5 class="card-title fw-bold">{{ $ed->nama_edisi }}</h5>
                <p class="card-text text-muted small mb-3">
                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $ed->kota_tuan_rumah ?? 'Lokasi Terpusat' }}<br>
                    <i class="fa-solid fa-calendar me-1 text-primary"></i>
                    {{ $ed->tanggal_mulai ? date('d M Y', strtotime($ed->tanggal_mulai)) : 'Jadwal Reguler' }}
                    @if($ed->tanggal_selesai) s/d {{ date('d M Y', strtotime($ed->tanggal_selesai)) }} @endif
                </p>

                <div class="bg-light p-2 rounded mb-3 small">
                    <strong>Batas Kelahiran:</strong> {{ $ed->min_tanggal_lahir ?? '-' }} s/d {{ $ed->max_tanggal_lahir ?? '-' }}
                </div>

                <div class="d-grid">
                    <a href="{{ url('/kejurnas/' . strtolower($ed->turnamen->kelompok_umur) . '/' . $ed->tahun) }}" class="btn btn-outline-dark btn-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Masuk Sub-Dashboard {{ $ed->turnamen->kelompok_umur }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- TURNAMEN LAINNYA (LIVOLI & KAPOLRI CUP) -->
<div class="row mt-3">
    <div class="col-12">
        <h4 class="mb-2"><strong>Turnamen & Liga Lainnya</strong></h4>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Turnamen</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Liga Bolavoli Indonesia (Livoli) Divisi Utama</strong></td>
                                <td><span class="badge bg-secondary">Senior Indoor</span></td>
                                <td>2026</td>
                                <td><span class="badge bg-primary">Persiapan</span></td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary" disabled>Panel Livoli</button></td>
                            </tr>
                            <tr>
                                <td><strong>Sirkuit Nasional Bolavoli Pantai</strong></td>
                                <td><span class="badge bg-info">Pantai / Pasir</span></td>
                                <td>2026</td>
                                <td><span class="badge bg-primary">Persiapan</span></td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary" disabled>Panel Voli Pantai</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
