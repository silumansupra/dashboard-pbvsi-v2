@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses (User RBAC)')

@section('content')
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
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna Baru
        </button>
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
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-edit-user me-1" 
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-username="{{ $u->username }}"
                                            data-email="{{ $u->email }}"
                                            data-phone="{{ $u->phone }}"
                                            data-active="{{ $u->is_active ? '1' : '0' }}"
                                            data-roles="{{ $u->roles->pluck('id')->join(',') }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditUser"
                                            title="Edit User">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($u->id != 1)
                                    <form action="{{ route('master.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');">
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
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH USER                          -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('master.users.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalTambahUserLabel">
                        <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna & Hak Akses
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap pengguna" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="username_login (tanpa spasi)" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="user@pbvsi.or.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Handphone / WhatsApp</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-select" required>
                                <option value="1" selected>Aktif (Bisa Login)</option>
                                <option value="0">Nonaktif (Blokir)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilih Peran / Hak Akses (RBAC) <span class="text-danger">*</span></label>
                            <div class="row g-2 p-3 bg-light rounded border">
                                @foreach($roles as $r)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}" id="role_add_{{ $r->id }}">
                                        <label class="form-check-label" for="role_add_{{ $r->id }}">
                                            <strong>{{ $r->display_name ?: ucfirst(str_replace('_', ' ', $r->name)) }}</strong>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengguna</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT USER (DYNAMIC)                  -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formEditUser" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalEditUserLabel">
                        <i class="fa-solid fa-user-pen me-1"></i> Edit Data Pengguna & Hak Akses
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_user_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_user_username" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_user_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Handphone / WhatsApp</label>
                            <input type="text" name="phone" id="edit_user_phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika password tidak diubah" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                            <select name="is_active" id="edit_user_active" class="form-select" required>
                                <option value="1">Aktif (Bisa Login)</option>
                                <option value="0">Nonaktif (Blokir)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilih Peran / Hak Akses (RBAC) <span class="text-danger">*</span></label>
                            <div class="row g-2 p-3 bg-light rounded border">
                                @foreach($roles as $r)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input edit-role-checkbox" type="checkbox" name="roles[]" value="{{ $r->id }}" id="role_edit_{{ $r->id }}">
                                        <label class="form-check-label" for="role_edit_{{ $r->id }}">
                                            <strong>{{ $r->display_name ?: ucfirst(str_replace('_', ' ', $r->name)) }}</strong>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Pengguna</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('modalEditUser');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const form = document.getElementById('formEditUser');
            form.action = "{{ url('master/users') }}/" + id;

            document.getElementById('edit_user_name').value = button.getAttribute('data-name') || '';
            document.getElementById('edit_user_username').value = button.getAttribute('data-username') || '';
            document.getElementById('edit_user_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_user_phone').value = button.getAttribute('data-phone') || '';
            document.getElementById('edit_user_active').value = button.getAttribute('data-active') || '1';

            // Reset all role checkboxes first
            const roleCheckboxes = document.querySelectorAll('.edit-role-checkbox');
            roleCheckboxes.forEach(cb => cb.checked = false);

            // Check roles assigned to this user
            const userRolesStr = button.getAttribute('data-roles') || '';
            if (userRolesStr) {
                const roleIds = userRolesStr.split(',');
                roleIds.forEach(roleId => {
                    const cb = document.getElementById('role_edit_' + roleId.trim());
                    if (cb) cb.checked = true;
                });
            }
        });
    }
});
</script>
@endpush
