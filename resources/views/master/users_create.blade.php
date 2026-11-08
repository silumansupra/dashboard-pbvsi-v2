@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru & Hak Akses - PBVSI')

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

{{-- Breadcrumb & Top Action --}}
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.users') }}">Master Pengguna & RBAC</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Tambah Pengguna Baru</span></li>
        </ol>
    </nav>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('master.users') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
        </a>
    </div>
</div>

<form action="{{ route('master.users.store') }}" method="POST" id="formCreateUser">
    @csrf

    @php
        $selectedRoles = old('roles', []);
        $selectedPerms = old('permissions', []);
    @endphp

    {{-- TAB NAVIGATION HEADER --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white p-2 border-bottom">
            <ul class="nav nav-pills nav-fill gap-2" id="userCreateTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="tab-credentials-btn" data-bs-toggle="pill" data-bs-target="#tab-credentials" type="button" role="tab" aria-controls="tab-credentials" aria-selected="true">
                        <i class="fa-solid fa-user-shield text-primary"></i>
                        <span>1. Akun & Kredensial</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="tab-roles-btn" data-bs-toggle="pill" data-bs-target="#tab-roles" type="button" role="tab" aria-controls="tab-roles" aria-selected="false">
                        <i class="fa-solid fa-user-lock text-info"></i>
                        <span>2. Peran & Hak Akses (Roles)</span>
                        <span class="badge bg-secondary rounded-pill" id="rolesBadgeCount">{{ count($selectedRoles) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="tab-permissions-btn" data-bs-toggle="pill" data-bs-target="#tab-permissions" type="button" role="tab" aria-controls="tab-permissions" aria-selected="false">
                        <i class="fa-solid fa-key text-warning"></i>
                        <span>3. Izin Khusus (Individual RBAC)</span>
                        <span class="badge bg-warning text-dark rounded-pill" id="permsBadgeCount">{{ count($selectedPerms) }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="userCreateTabsContent">
                
                {{-- TAB 1: AKUN & KREDENSIAL --}}
                <div class="tab-pane fade show active" id="tab-credentials" role="tabpanel" aria-labelledby="tab-credentials-btn">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="fa-solid fa-user-gear text-primary me-2"></i> Identitas Akun & Kredensial Login Baru
                            </h5>
                            <small class="text-muted">Isi data akun, email resmi, dan buat kata sandi untuk login pertama pengguna.</small>
                        </div>
                        <span class="badge bg-light text-dark border px-2 py-1">Langkah 1</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control text-uppercase @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: AHMAD HIDAYAT" required autofocus>
                            <small class="text-muted">Nama resmi sesuai identitas KTP (otomatis HURUF BESAR).</small>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-at"></i></span>
                                <input type="text" name="username" class="form-control font-monospace @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: ahmad.pbvsi" required>
                            </div>
                            <small class="text-muted">Digunakan untuk login ke sistem PBVSI.</small>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="ahmad@pbvsi.or.id" required>
                            </div>
                            <small class="text-muted">Email aktif pengguna untuk pemberitahuan akun.</small>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-brands fa-whatsapp text-success"></i></span>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            </div>
                            <small class="text-muted">Nomor WhatsApp aktif untuk koordinasi.</small>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="inputNewPassword" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('inputNewPassword', 'pwdNewIcon')">
                                    <i class="fa-regular fa-eye" id="pwdNewIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Kata sandi awal untuk diberikan kepada pengguna.</small>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Keaktifan Akun <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Langsung Bisa Login)</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif (Ditangguhkan Dulu)</option>
                            </select>
                            <small class="text-muted">Pilih apakah akun langsung diaktifkan.</small>
                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-primary" onclick="switchTab('tab-roles-btn')">
                            Lanjut ke Tab Peran & Hak Akses <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- TAB 2: PERAN & HAK AKSES (ROLES RBAC) --}}
                <div class="tab-pane fade" id="tab-roles" role="tabpanel" aria-labelledby="tab-roles-btn">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="fa-solid fa-user-lock text-info me-2"></i> Peran Pengguna (Roles RBAC) <span class="text-danger">*</span>
                            </h5>
                            <small class="text-muted">Setidaknya pilih 1 peran struktural/fungsional untuk pengguna baru ini.</small>
                        </div>
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="roleSearchInput" class="form-control" placeholder="Cari peran..." onkeyup="filterRoles()">
                        </div>
                    </div>

                    @error('roles')<div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>@enderror

                    <div class="row g-3" id="rolesGrid">
                        @foreach($roles as $role)
                        @php
                            $isChecked = is_array($selectedRoles) && in_array($role->id, $selectedRoles);
                        @endphp
                        <div class="col-md-6 col-lg-4 role-item-card" data-role-name="{{ strtolower($role->name . ' ' . $role->display_name . ' ' . $role->description) }}">
                            <div class="card h-100 border p-3 cursor-pointer role-card-box {{ $isChecked ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'bg-light' }}" onclick="toggleCheckbox('role_{{ $role->id }}')">
                                <div class="d-flex align-items-start gap-2">
                                    <input class="form-check-input mt-1 role-checkbox" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ $isChecked ? 'checked' : '' }} onclick="event.stopPropagation(); updateRoleCount();">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="role_{{ $role->id }}">
                                                {{ $role->display_name ?: strtoupper($role->name) }}
                                            </label>
                                            <span class="badge {{ $role->name === 'superadmin' ? 'bg-danger' : ($role->name === 'admin_kejurnas' ? 'bg-primary' : 'bg-secondary') }}" style="font-size: 0.65rem;">
                                                {{ strtoupper($role->name) }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            {{ $role->description ?: 'Peran pengguna terdaftar PBVSI.' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="switchTab('tab-credentials-btn')">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kredensial
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchTab('tab-permissions-btn')">
                            Lanjut ke Izin Khusus <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- TAB 3: IZIN KHUSUS (INDIVIDUAL GRANULAR RBAC) --}}
                <div class="tab-pane fade" id="tab-permissions" role="tabpanel" aria-labelledby="tab-permissions-btn">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 pb-2 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fa-solid fa-key text-warning me-2"></i> Hak Akses Khusus (Individual RBAC)
                                </h5>
                                <span class="badge bg-warning text-dark px-2 py-1">Granular</span>
                            </div>
                            <small class="text-muted">Izin personal perorangan yang diberikan kepada akun ini di luar batasan peran (opsional).</small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddPermission">
                                <i class="fa-solid fa-plus-circle me-1"></i> Tambah Izin Baru (CMS)
                            </button>
                        </div>
                    </div>

                    {{-- Toolbar Pencarian & Filter Cepat --}}
                    <div class="bg-light p-3 rounded-3 border mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 400px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="permSearchInput" class="form-control" placeholder="Cari izin (nama atau slug)..." onkeyup="filterPermissions()">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-sm" onclick="selectAllPermissions(true)">
                                <i class="fa-solid fa-check-double me-1"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-sm" onclick="selectAllPermissions(false)">
                                <i class="fa-solid fa-xmark me-1"></i> Kosongkan
                            </button>
                            <span class="badge bg-secondary px-2 py-2 small" id="permSelectedCounter">
                                0 Izin Terpilih
                            </span>
                        </div>
                    </div>

                    @error('permissions')<div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>@enderror

                    {{-- Permissions Grid --}}
                    <div class="row g-3" id="permissionsGrid">
                        @forelse($permissions as $perm)
                        @php
                            $isPermChecked = is_array($selectedPerms) && in_array($perm->id, $selectedPerms);
                        @endphp
                        <div class="col-md-6 col-lg-4 perm-item-card" data-perm-name="{{ strtolower($perm->name . ' ' . $perm->display_name . ' ' . $perm->description) }}">
                            <div class="card h-100 border p-3 cursor-pointer perm-card-box {{ $isPermChecked ? 'border-warning bg-warning bg-opacity-10 shadow-sm' : 'bg-light' }}" onclick="toggleCheckbox('perm_{{ $perm->id }}')">
                                <div class="d-flex align-items-start gap-2">
                                    <input class="form-check-input mt-1 perm-checkbox" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ $isPermChecked ? 'checked' : '' }} onclick="event.stopPropagation(); updatePermCount();">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
                                            <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="perm_{{ $perm->id }}">
                                                {{ $perm->display_name ?: $perm->name }}
                                            </label>
                                            <span class="badge bg-white text-dark border font-monospace" style="font-size: 0.68rem;">
                                                {{ $perm->name }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            {{ $perm->description ?: 'Izin operasional platform PBVSI.' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="fa-solid fa-key fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">Belum ada izin khusus yang terdaftar di sistem.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="switchTab('tab-roles-btn')">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Peran
                        </button>
                        <span class="text-muted small">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Pengguna akan langsung mendapatkan akses menu sesuai izin yang dipilih.
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- GLOBAL STICKY ACTIONS FOOTER --}}
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('master.users') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-xmark me-1"></i> Batalkan
            </a>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengguna Baru & Akses
                </button>
            </div>
        </div>
    </div>
</form>

{{-- MODAL TAMBAH IZIN BARU DINAMIS (CMS PLATFORM) --}}
<div class="modal fade" id="modalAddPermission" tabindex="-1" aria-labelledby="modalAddPermissionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-key text-warning"></i>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="modalAddPermissionLabel">Tambah Izin Baru (CMS RBAC)</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddPermissionAjax">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug Kunci Izin (Permission Key) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="text" name="name" id="newPermKey" class="form-control font-monospace" placeholder="contoh: report.export, roster.approve" required>
                        </div>
                        <small class="text-muted">Gunakan huruf kecil, angka, dan pemisah titik (.) atau garis bawah (_).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tampilan Izin <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="newPermDisplayName" class="form-control" placeholder="contoh: Export Laporan Kejuaraan" required>
                        <small class="text-muted">Nama yang mudah dibaca oleh admin.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Wewenang</label>
                        <textarea name="description" id="newPermDesc" class="form-control" rows="2" placeholder="contoh: Mengunduh data rekap kejuaraan ke format Excel/PDF."></textarea>
                    </div>

                    <div id="permModalError" class="alert alert-danger py-2 small" style="display: none;"></div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitNewPerm" class="btn btn-primary btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-plus-circle me-1"></i> Buat Izin & Tambahkan ke List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabButtonId) {
        const btn = document.getElementById(tabButtonId);
        if (btn) {
            const tab = new bootstrap.Tab(btn);
            tab.show();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function toggleCheckbox(checkboxId) {
        const chk = document.getElementById(checkboxId);
        if (chk) {
            chk.checked = !chk.checked;
            if (checkboxId.startsWith('role_')) {
                updateRoleCount();
            } else if (checkboxId.startsWith('perm_')) {
                updatePermCount();
            }
        }
    }

    function updateRoleCount() {
        const checkboxes = document.querySelectorAll('.role-checkbox');
        let count = 0;
        checkboxes.forEach(cb => {
            const card = cb.closest('.role-card-box');
            if (cb.checked) {
                count++;
                if (card) {
                    card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10', 'shadow-sm');
                    card.classList.remove('bg-light');
                }
            } else {
                if (card) {
                    card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10', 'shadow-sm');
                    card.classList.add('bg-light');
                }
            }
        });
        const badge = document.getElementById('rolesBadgeCount');
        if (badge) badge.textContent = count;
    }

    function updatePermCount() {
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        let count = 0;
        checkboxes.forEach(cb => {
            const card = cb.closest('.perm-card-box');
            if (cb.checked) {
                count++;
                if (card) {
                    card.classList.add('border-warning', 'bg-warning', 'bg-opacity-10', 'shadow-sm');
                    card.classList.remove('bg-light');
                }
            } else {
                if (card) {
                    card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10', 'shadow-sm');
                    card.classList.add('bg-light');
                }
            }
        });
        const badge = document.getElementById('permsBadgeCount');
        if (badge) badge.textContent = count;

        const counter = document.getElementById('permSelectedCounter');
        if (counter) counter.textContent = `${count} Izin Terpilih`;
    }

    function filterRoles() {
        const q = (document.getElementById('roleSearchInput').value || '').toLowerCase().trim();
        document.querySelectorAll('.role-item-card').forEach(item => {
            const text = item.getAttribute('data-role-name') || '';
            item.style.display = text.includes(q) ? '' : 'none';
        });
    }

    function filterPermissions() {
        const q = (document.getElementById('permSearchInput').value || '').toLowerCase().trim();
        document.querySelectorAll('.perm-item-card').forEach(item => {
            const text = item.getAttribute('data-perm-name') || '';
            item.style.display = text.includes(q) ? '' : 'none';
        });
    }

    function selectAllPermissions(status) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            const parent = cb.closest('.perm-item-card');
            if (parent && parent.style.display !== 'none') {
                cb.checked = status;
            }
        });
        updatePermCount();
    }

    document.getElementById('formAddPermissionAjax').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('btnSubmitNewPerm');
        const errBox = document.getElementById('permModalError');
        errBox.style.display = 'none';

        const originalBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';

        const formData = new FormData(this);

        fetch("{{ route('master.permissions.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                let msg = data.message || 'Gagal menyimpan izin.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            const perm = data.permission;

            const grid = document.getElementById('permissionsGrid');
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 perm-item-card';
            col.setAttribute('data-perm-name', `${perm.name} ${perm.display_name} ${perm.description || ''}`.toLowerCase());

            col.innerHTML = `
                <div class="card h-100 border border-warning bg-warning bg-opacity-10 shadow-sm p-3 cursor-pointer perm-card-box" onclick="toggleCheckbox('perm_${perm.id}')">
                    <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1 perm-checkbox" type="checkbox" name="permissions[]" value="${perm.id}" id="perm_${perm.id}" checked onclick="event.stopPropagation(); updatePermCount();">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
                                <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="perm_${perm.id}">
                                    ${perm.display_name}
                                </label>
                                <span class="badge bg-success text-white" style="font-size: 0.6rem;">BARU</span>
                            </div>
                            <span class="badge bg-white text-dark border font-monospace mt-1" style="font-size: 0.68rem;">
                                ${perm.name}
                            </span>
                            <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                ${perm.description || 'Izin operasional platform PBVSI.'}
                            </small>
                        </div>
                    </div>
                </div>
            `;

            grid.prepend(col);
            updatePermCount();

            document.getElementById('formAddPermissionAjax').reset();
            const modalEl = document.getElementById('modalAddPermission');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            switchTab('tab-permissions-btn');

            alert(`Sukses! Izin '${perm.display_name}' (${perm.name}) berhasil dibuat dan langsung tercentang untuk pengguna ini.`);
        })
        .catch(err => {
            errBox.innerHTML = err.message;
            errBox.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        updateRoleCount();
        updatePermCount();
    });
</script>
@endpush
