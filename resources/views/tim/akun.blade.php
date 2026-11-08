@extends('layouts.app')

@section('title', 'Profil Akun Saya - ' . $user->name)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Pengaturan Akun</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
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

<div class="row g-4">
    <!-- INFO AKUN & TIM KELOLAAN -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            <div class="card-body p-0">
                <div class="position-relative d-inline-block mb-3">
                    @if(!empty($user->avatar_path))
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm border border-3 border-primary-subtle" style="width: 92px; height: 92px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 92px; height: 92px; font-size: 2.3rem; font-weight: bold;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                    @endif
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <span class="badge bg-primary px-3 py-1 mb-2">Administrator Tim / Kontingen</span>
                <div class="text-muted small">Username: <code>{{ $user->username }}</code></div>
                <div class="text-muted small">Email: {{ $user->email }}</div>

                <!-- STATUS VERIFIKASI AKUN & KYC -->
                <hr class="my-3">
                <div class="text-start">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-semibold">Status Akun:</span>
                        @php
                            $status = $profile?->verification_status ?? 'UNVERIFIED';
                        @endphp
                        @if($status === 'VERIFIED')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi
                            </span>
                        @elseif($status === 'PENDING')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="fa-solid fa-clock me-1"></i> Menunggu Verifikasi
                            </span>
                        @elseif($status === 'REJECTED')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Perlu Perbaikan
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                <i class="fa-solid fa-circle-question me-1"></i> Belum Lengkap
                            </span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">NIK KTP:</span>
                        <span class="fw-bold text-dark">
                            {{ $profile?->nik ? substr($profile->nik, 0, 6) . '******' . substr($profile->nik, -4) : 'Belum Terisi' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">Berkas KTP:</span>
                        @if(!empty($profile?->ktp_path))
                            <span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Terunggah</span>
                        @else
                            <span class="text-danger fw-semibold"><i class="fa-solid fa-xmark me-1"></i> Belum Diunggah</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 small">
                        <span class="text-muted">Selfie KTP:</span>
                        @if(!empty($profile?->selfie_ktp_path))
                            <span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Terunggah</span>
                        @else
                            <span class="text-danger fw-semibold"><i class="fa-solid fa-xmark me-1"></i> Belum Diunggah</span>
                        @endif
                    </div>

                    <a href="{{ route('profil.lengkapi') }}" class="btn btn-sm btn-outline-primary w-100 fw-semibold">
                        <i class="fa-solid fa-id-card me-1"></i> Kelola Berkas Identitas (KYC)
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-shield-halved text-primary me-2"></i>Tim Kelolaan Anda ({{ $teams->count() }})
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($teams as $t)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-bold text-dark">{{ $t->nama_tim }}</div>
                            <small class="text-muted">{{ $t->edisi?->turnamen?->nama_turnamen }} ({{ $t->edisi?->tahun_edisi }})</small>
                        </div>
                        <span class="badge {{ $t->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }}">
                            {{ $t->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                        </span>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted small">Belum ada tim yang terhubung.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- FORM EDIT PROFIL & GANTI PASSWORD -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-user-gear text-primary me-2"></i>Perbarui Data Profil Akun & Password
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tim.akun.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly disabled>
                            <small class="text-muted">Username resmi PBVSI bersifat permanen.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Admin <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP Aktif</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ganti Foto Profil Akun (Opsional)</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Format: JPG, PNG, atau WebP (Maks. 10MB). Otomatis dikompres & disimpan ke Bunny CDN.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-lock text-warning me-2"></i>Ubah Password (Kosongkan jika tidak ingin mengganti)
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
