@extends('layouts.app')

@section('title', 'Profil Akun Saya - ' . $user->name)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house me-1"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="#!">Pengaturan Akun</a></li>
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
    <!-- INFO KARTU USER -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="card-body">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2.2rem; font-weight: bold;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <span class="badge bg-primary px-3 py-1 mb-2">
                    {{ $role?->display_name ?? 'Anggota PBVSI' }}
                </span>
                <div class="text-muted small">Username: <code>{{ $user->username }}</code></div>
                <div class="text-muted small">Email: {{ $user->email }}</div>
                <hr class="my-3">
                <div class="small text-muted text-start">
                    <div><i class="fa-solid fa-check text-success me-2"></i>Status: <strong>Aktif</strong></div>
                    <div><i class="fa-solid fa-calendar me-2 text-info"></i>Terdaftar: {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM EDIT PROFIL & GANTI PASSWORD -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-user-gear text-primary me-2"></i>Perbarui Informasi Profil & Password
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('akun.saya.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ID Pengguna / Username</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly disabled>
                            <small class="text-muted">Username PBVSI bersifat permanen.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Peran / Role</label>
                            <input type="text" class="form-control bg-light" value="{{ $role?->display_name ?? 'Anggota' }}" readonly disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP Aktif</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-lock text-warning me-2"></i>Ubah Password Akun (Kosongkan jika tidak ingin mengganti)
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
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
