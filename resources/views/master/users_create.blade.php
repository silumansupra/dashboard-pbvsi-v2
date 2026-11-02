@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - PBVSI')

@section('content')
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-danger"></i>
        <strong>Terdapat kesalahan pengisian formulir:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.users') }}">Master Pengguna</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Pengguna</span></li>
        </ol>
    </nav>
    <a href="{{ route('master.users') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
    </a>
</div>

<form action="{{ route('master.users.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-shield text-primary me-2"></i> Akun & Kredensial Pengguna
                    </h5>
                    <small class="text-muted">Lengkapi identitas user dan buat kredensial untuk akses dashboard PBVSI.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control text-uppercase @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: AHMAD HIDAYAT" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: ahmad.pbvsi" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="ahmad@pbvsi.or.id" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- ROLES RBAC -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-lock text-primary me-2"></i> Peran & Hak Akses <span class="text-danger">*</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-semibold small text-muted">Pilih satu atau lebih hak akses:</label>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($roles as $role)
                        <div class="form-check p-2 bg-light border rounded">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ is_array(old('roles')) && in_array($role->id, old('roles')) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark cursor-pointer" for="role_{{ $role->id }}">
                                {{ strtoupper($role->name) }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('roles')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Bisa Login)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif (Ditangguhkan)</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengguna
                        </button>
                        <a href="{{ route('master.users') }}" class="btn btn-outline-secondary py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
