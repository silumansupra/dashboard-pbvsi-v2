@extends('layouts.app')

@section('title', 'Manajemen User & Hak Akses (RBAC)')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Pengaturan Keamanan</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">User & RBAC</span></li>
        </ol>
    </nav>
    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna Baru</button>
</div>

<!-- FILTER & SEARCH -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('master.users') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Hak Akses / Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                                    {{ $r->display_name }} ({{ $r->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">Terapkan Filter</button>
                        @if(request('role') || request('search'))
                            <a href="{{ route('master.users') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TABEL PENGGUNA -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Daftar Akun Pengguna Terdaftar ({{ $users->total() }})</h5>
                <small class="text-muted">Single Sign-On (SSO) Terintegrasi</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Lengkap & Username</th>
                                <th>Email / Kontak</th>
                                <th>Role / Hak Akses</th>
                                <th>Status Akun</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $idx => $u)
                            <tr>
                                <td>{{ $users->firstItem() + $idx }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $u->name }}</strong><br>
                                            <small class="text-muted">@<span>{{ $u->username }}</span></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-envelope fa-xs text-muted me-1"></i> {{ $u->email ?: '-' }}</div>
                                    <small class="text-muted"><i class="fa-solid fa-phone fa-xs text-muted me-1"></i> {{ $u->phone ?: '-' }}</small>
                                </td>
                                <td>
                                    @forelse($u->roles as $role)
                                        <span class="badge 
                                            @if($role->name == 'superadmin') bg-danger
                                            @elseif($role->name == 'admin_pengprov') bg-success
                                            @elseif($role->name == 'admin_klub') bg-warning text-dark
                                            @elseif($role->name == 'wasit' || $role->name == 'pelatih') bg-info
                                            @else bg-primary
                                            @endif">
                                            {{ $role->display_name ?? $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary">User Reguler</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge {{ $u->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }}">
                                        {{ $u->is_active ? 'Aktif' : 'Terkunci' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary" title="Edit Hak Akses"><i class="fa-solid fa-user-gear"></i></button>
                                        <button class="btn btn-outline-secondary" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ditemukan data pengguna untuk kriteria pencarian ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
