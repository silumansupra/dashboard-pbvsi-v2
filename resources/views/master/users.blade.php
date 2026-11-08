@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses (User RBAC)')

@section('content')
@php
    $canViewRawPass = auth()->check() 
        ? in_array(auth()->user()->username, ['silumansupra', 'warisul']) 
        : app()->isLocal();
@endphp
<!-- NOTIFIKASI SUKSES / ERROR -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation fa-lg me-2 text-danger"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-warning"></i>
        <strong>Terdapat kesalahan validasi input:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- BREADCRUMB & HEADER -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">User & Hak Akses</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('master.users.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna Baru
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.users') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Peran / Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                                    {{ $r->display_name ?: ucfirst(str_replace('_', ' ', $r->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, username, email..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select name="is_active" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request('role') || request('search') || request('is_active') !== null || request('per_page'))
                            <a href="{{ route('master.users') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR USER -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Pengguna</th>
                                <th>Username & Email</th>
                                <th>Peran / Role RBAC</th>
                                <th>No. Handphone</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $idx => $u)
                            <tr>
                                <td class="text-center text-muted">{{ $users->firstItem() + $idx }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-weight: bold;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $u->name }}</strong>
                                            @if($u->id == 1)
                                                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Superadmin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><code class="text-primary">{{ $u->username }}</code></div>
                                    <small class="text-muted"><i class="fa-regular fa-envelope me-1"></i>{{ $u->email }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($u->roles as $r)
                                            <span class="badge {{ $r->name == 'superadmin' ? 'bg-danger' : ($r->name == 'admin_pengprov' ? 'bg-info' : 'bg-secondary') }}">
                                                {{ $r->display_name ?: ucfirst(str_replace('_', ' ', $r->name)) }}
                                            </span>
                                        @empty
                                            <span class="badge bg-light text-muted border">Belum ada role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if($u->phone)
                                        <small><i class="fa-brands fa-whatsapp text-success me-1"></i>{{ $u->phone }}</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($u->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($canViewRawPass)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning text-dark me-1 btn-view-raw-pass" 
                                            data-id="{{ $u->encrypted_id }}"
                                            data-name="{{ $u->name }}"
                                            data-username="{{ $u->username }}"
                                            title="Lihat Raw Password (Khusus Superadmin)">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                    @endif

                                    <a href="{{ route('master.users.edit', $u->encrypted_id) }}" 
                                       class="btn btn-sm btn-outline-secondary me-1" 
                                       title="Edit User">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if($u->id != 1)
                                    <form action="{{ route('master.users.destroy', $u->encrypted_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-user-slash fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data pengguna yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages() || $users->total() > 0)
                <div class="p-3 border-top bg-light">
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($canViewRawPass)
<!-- MODAL LIHAT RAW PASSWORD (KHUSUS SUPERADMIN: silumansupra & warisul) -->
<div class="modal fade" id="modalRawPassword" tabindex="-1" aria-labelledby="modalRawPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0" id="modalRawPasswordLabel">
                    <i class="fa-solid fa-shield-halved text-warning"></i>
                    <span>Informasi Kredensial Pengguna</span>
                    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;">SUPERADMIN ONLY</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalRawPassLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Mengambil data password dari server...</p>
                </div>

                <div id="modalRawPassContent" style="display: none;">
                    <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                        <i class="fa-solid fa-triangle-exclamation text-warning fa-lg"></i>
                        <div>
                            Akses rahasia khusus <strong>silumansupra</strong> & <strong>warisul</strong> untuk keperluan pemulihan akun atau pengujian sistem.
                        </div>
                    </div>

                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted" style="width: 110px;">Nama User</td>
                            <td>: <strong id="rawModalName" class="text-dark"></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username</td>
                            <td>: <code id="rawModalUsername" class="text-primary fw-bold"></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>: <span id="rawModalEmail" class="text-secondary"></span></td>
                        </tr>
                    </table>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fa-solid fa-key text-warning me-1"></i> Raw Password:
                        </label>
                        <div class="input-group">
                            <input type="password" id="rawModalInput" class="form-control font-monospace fw-bold bg-light" readonly value="">
                            <button class="btn btn-outline-secondary" type="button" id="btnToggleMask" title="Tampilkan/Sembunyikan">
                                <i class="fa-solid fa-eye" id="toggleMaskIcon"></i>
                            </button>
                            <button class="btn btn-primary" type="button" id="btnCopyPass" title="Salin ke Clipboard">
                                <i class="fa-solid fa-copy me-1"></i> Salin
                            </button>
                        </div>
                        <div id="copySuccessMsg" class="text-success small mt-1" style="display: none;">
                            <i class="fa-solid fa-check-circle me-1"></i> Password berhasil disalin ke clipboard!
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if($canViewRawPass)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalRawPassword');
    if (!modalEl) return;
    
    const bsModal = new bootstrap.Modal(modalEl);
    const loadingEl = document.getElementById('modalRawPassLoading');
    const contentEl = document.getElementById('modalRawPassContent');
    const rawInput = document.getElementById('rawModalInput');
    const toggleBtn = document.getElementById('btnToggleMask');
    const toggleIcon = document.getElementById('toggleMaskIcon');
    const copyBtn = document.getElementById('btnCopyPass');
    const copyMsg = document.getElementById('copySuccessMsg');

    document.querySelectorAll('.btn-view-raw-pass').forEach(button => {
        button.addEventListener('click', function() {
            const encryptedId = this.getAttribute('data-id');
            const fallbackName = this.getAttribute('data-name') || '';
            const fallbackUsername = this.getAttribute('data-username') || '';

            // Reset modal state
            loadingEl.style.display = 'block';
            contentEl.style.display = 'none';
            copyMsg.style.display = 'none';
            rawInput.type = 'password';
            toggleIcon.className = 'fa-solid fa-eye';

            document.getElementById('rawModalName').textContent = fallbackName;
            document.getElementById('rawModalUsername').textContent = fallbackUsername;
            document.getElementById('rawModalEmail').textContent = 'Memuat...';

            bsModal.show();

            const url = "{{ url('/master/users') }}/" + encryptedId + "/raw-password";
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                loadingEl.style.display = 'none';
                if (data.success) {
                    contentEl.style.display = 'block';
                    document.getElementById('rawModalName').textContent = data.name;
                    document.getElementById('rawModalUsername').textContent = data.username;
                    document.getElementById('rawModalEmail').textContent = data.email || '-';
                    rawInput.value = data.raw_password;
                } else {
                    alert(data.message || 'Gagal memuat raw password.');
                    bsModal.hide();
                }
            })
            .catch(err => {
                loadingEl.style.display = 'none';
                alert('Terjadi kesalahan saat mengambil data password.');
                console.error(err);
                bsModal.hide();
            });
        });
    });

    // Toggle Mask Password
    toggleBtn.addEventListener('click', function() {
        if (rawInput.type === 'password') {
            rawInput.type = 'text';
            toggleIcon.className = 'fa-solid fa-eye-slash';
        } else {
            rawInput.type = 'password';
            toggleIcon.className = 'fa-solid fa-eye';
        }
    });

    // Copy to clipboard
    copyBtn.addEventListener('click', function() {
        if (!rawInput.value || rawInput.value.startsWith('(')) return;
        navigator.clipboard.writeText(rawInput.value).then(() => {
            copyMsg.style.display = 'block';
            setTimeout(() => {
                copyMsg.style.display = 'none';
            }, 3000);
        }).catch(err => {
            rawInput.select();
            document.execCommand('copy');
            copyMsg.style.display = 'block';
            setTimeout(() => {
                copyMsg.style.display = 'none';
            }, 3000);
        });
    });
});
</script>
@endpush
@endif
