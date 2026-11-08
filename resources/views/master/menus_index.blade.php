@extends('layouts.app')

@section('title', 'Manajemen Menu & Navigasi Dinamis - PBVSI')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.users') }}">Akses & RBAC</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Manajemen Menu Navigasi</span></li>
        </ol>
    </nav>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddMenu">
            <i class="fa-solid fa-plus me-1"></i> Tambah Menu Baru
        </button>
    </div>
</div>

{{-- Notification Banner for High-Performance Caching --}}
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 py-3">
    <div class="d-flex align-items-center">
        <div class="rounded-circle bg-info bg-opacity-25 text-info p-2 me-3">
            <i class="fa-solid fa-bolt fa-lg"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0 text-dark">High-Performance Dynamic Navigation Caching Aktif</h6>
            <small class="text-muted">Navigasi menu di-cache secara otomatis untuk kecepatan maksimal (0 query ke database). Setiap perubahan status, urutan, atau hak akses akan memperbarui cache secara instan di semua user.</small>
        </div>
    </div>
    <span class="badge bg-info text-dark px-3 py-2">
        <i class="fa-solid fa-check-double me-1"></i> Auto-Invalidated
    </span>
</div>

{{-- Main Card --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-bars-staggered text-primary me-2"></i> Hirarki Menu & Submenu Navigasi
        </h5>
        <div class="text-muted small">
            Total Menu: <strong>{{ $menus->count() }} Menu Utama</strong>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;" class="text-center">Urutan</th>
                    <th style="width: 60px;" class="text-center">Ikon</th>
                    <th>Judul Menu / Submenu</th>
                    <th>Route / URL Target</th>
                    <th>Hak Akses (Permission)</th>
                    <th class="text-center" style="width: 120px;">Status Aktif</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $root)
                {{-- ROOT MENU ROW --}}
                <tr class="table-group-divider {{ !$root->is_active ? 'opacity-75 bg-light' : '' }}" id="menu-row-{{ $root->id }}">
                    <td class="text-center">
                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold">{{ $root->order }}</span>
                    </td>
                    <td class="text-center">
                        <div class="rounded bg-light text-primary d-inline-flex align-items-center justify-content-center border" style="width: 36px; height: 36px;">
                            <i class="{{ $root->icon ?: 'fa-solid fa-circle-dot' }} fa-lg"></i>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark fs-6">
                            {{ $root->title }}
                            @if($root->badge_text)
                                <span class="badge {{ $root->badge_class ?: 'bg-secondary' }} ms-1" style="font-size: 0.65rem;">
                                    {{ $root->badge_text }}
                                </span>
                            @endif
                        </div>
                        @if($root->children->isNotEmpty())
                            <small class="text-muted"><i class="fa-solid fa-turn-up fa-rotate-90 me-1"></i> {{ $root->children->count() }} Submenu terkait</small>
                        @endif
                    </td>
                    <td>
                        @if($root->route_name)
                            <code class="text-primary">{{ $root->route_name }}</code>
                        @elseif($root->url && $root->url !== '#!')
                            <span class="text-muted font-monospace small">{{ $root->url }}</span>
                        @else
                            <span class="text-muted small"><em>(Menu Header / Induk)</em></span>
                        @endif
                    </td>
                    <td>
                        @if($root->permission_key)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">
                                <i class="fa-solid fa-lock me-1"></i>{{ $root->permission_key }}
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="fa-solid fa-lock-open me-1"></i>Publik (Semua Role)
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="toggle-{{ $root->id }}" {{ $root->is_active ? 'checked' : '' }} onchange="toggleMenuStatus({{ $root->id }})">
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="openAddSubmenuModal({{ $root->id }}, '{{ addslashes($root->title) }}')" title="Tambah Submenu">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="openEditMenuModal({{ $root->id }})" title="Edit Menu">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteMenu({{ $root->id }}, '{{ addslashes($root->title) }}')" title="Hapus Menu">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- SUBMENU ROWS --}}
                @foreach($root->children as $child)
                <tr class="bg-light bg-opacity-50 {{ !$child->is_active ? 'opacity-75' : '' }}" id="menu-row-{{ $child->id }}">
                    <td class="text-center text-muted small">
                        {{ $root->order }}.{{ $child->order }}
                    </td>
                    <td class="text-center">
                        <div class="text-secondary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="{{ $child->icon ?: 'fa-regular fa-circle' }}"></i>
                        </div>
                    </td>
                    <td>
                        <div class="ps-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-arrow-turn-up fa-rotate-90 text-muted opacity-50"></i>
                            <span class="fw-semibold text-dark">{{ $child->title }}</span>
                            @if($child->badge_text)
                                <span class="badge {{ $child->badge_class ?: 'bg-secondary' }}" style="font-size: 0.65rem;">
                                    {{ $child->badge_text }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($child->route_name)
                            <code class="text-secondary">{{ $child->route_name }}</code>
                        @elseif($child->url)
                            <span class="text-muted font-monospace small">{{ $child->url }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($child->permission_key)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">
                                <i class="fa-solid fa-lock me-1"></i>{{ $child->permission_key }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted border">
                                Mengikuti Induk
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="toggle-{{ $child->id }}" {{ $child->is_active ? 'checked' : '' }} onchange="toggleMenuStatus({{ $child->id }})">
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="openEditMenuModal({{ $child->id }})" title="Edit Submenu">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteMenu({{ $child->id }}, '{{ addslashes($child->title) }}')" title="Hapus Submenu">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach

                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fa-solid fa-bars-staggered fa-3x text-secondary opacity-50 mb-3"></i>
                            <h6 class="fw-bold">Belum ada menu yang didaftarkan</h6>
                            <p class="small text-muted mb-0">Klik tombol "Tambah Menu Baru" di atas untuk menambahkan item navigasi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH MENU --}}
<div class="modal fade" id="modalAddMenu" tabindex="-1" aria-labelledby="modalAddMenuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalAddMenuLabel">
                    <i class="fa-solid fa-plus-circle me-1"></i> Tambah Menu / Submenu Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master.menus.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Menu Induk (Parent Menu)</label>
                            <select name="parent_id" id="addParentId" class="form-select">
                                <option value="">-- Menu Utama (Root / Header) --</option>
                                @foreach($parentOptions as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kosongkan jika ini adalah menu utama.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Judul Menu <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Verifikasi KYC & KTP" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Route Laravel</label>
                            <input type="text" name="route_name" class="form-control font-monospace" placeholder="Contoh: master.kyc.index">
                            <small class="text-muted">Nama route dari routes/web.php (diprioritaskan jika ada).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">URL Kustom (Jika Tanpa Route)</label>
                            <input type="text" name="url" class="form-control font-monospace" placeholder="Contoh: /kejuaraan/kejurnas-u16">
                            <small class="text-muted">Gunakan '#!' jika hanya berfungsi sebagai pembuka dropdown.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas Ikon (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-icons"></i></span>
                                <input type="text" name="icon" class="form-control" placeholder="Contoh: fa-solid fa-id-card-clip">
                            </div>
                            <small class="text-muted">Lihat katalog FontAwesome 6 (e.g. fa-solid fa-trophy).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hak Akses Dibutuhkan (Permission)</label>
                            <select name="permission_key" class="form-select">
                                <option value="">-- Terbuka Untuk Semua Pengguna --</option>
                                @foreach($permissions as $perm)
                                    <option value="{{ $perm->name }}">{{ $perm->name }} ({{ $perm->display_name ?: $perm->name }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya user dengan izin ini (atau Superadmin) yang bisa melihat menu ini.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Urutan Tampilan</label>
                            <input type="number" name="order" class="form-control" placeholder="Otomatis" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teks Badge (Opsional)</label>
                            <input type="text" name="badge_text" class="form-control" placeholder="Contoh: KYC, BARU">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warna Badge</label>
                            <select name="badge_class" class="form-select">
                                <option value="bg-primary">Biru (Primary)</option>
                                <option value="bg-danger">Merah (Danger)</option>
                                <option value="bg-warning text-dark">Kuning (Warning)</option>
                                <option value="bg-success">Hijau (Success)</option>
                                <option value="bg-info text-dark">Cyan (Info)</option>
                                <option value="bg-secondary">Abu-abu (Secondary)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Tautan</label>
                            <select name="target" class="form-select">
                                <option value="_self">Buka di Tab yang Sama (_self)</option>
                                <option value="_blank">Buka di Tab Baru (_blank)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Awal</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Langsung Aktif</option>
                                <option value="0">Nonaktifkan Dulu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT MENU --}}
<div class="modal fade" id="modalEditMenu" tabindex="-1" aria-labelledby="modalEditMenuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="modalEditMenuLabel">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Menu Navigasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMenuForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Menu Induk (Parent Menu)</label>
                            <select name="parent_id" id="editParentId" class="form-select">
                                <option value="">-- Menu Utama (Root / Header) --</option>
                                @foreach($parentOptions as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Judul Menu <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Route Laravel</label>
                            <input type="text" name="route_name" id="editRouteName" class="form-control font-monospace">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">URL Kustom (Jika Tanpa Route)</label>
                            <input type="text" name="url" id="editUrl" class="form-control font-monospace">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas Ikon (FontAwesome)</label>
                            <input type="text" name="icon" id="editIcon" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hak Akses Dibutuhkan (Permission)</label>
                            <select name="permission_key" id="editPermissionKey" class="form-select">
                                <option value="">-- Terbuka Untuk Semua Pengguna --</option>
                                @foreach($permissions as $perm)
                                    <option value="{{ $perm->name }}">{{ $perm->name }} ({{ $perm->display_name ?: $perm->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Urutan Tampilan <span class="text-danger">*</span></label>
                            <input type="number" name="order" id="editOrder" class="form-control" required min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teks Badge</label>
                            <input type="text" name="badge_text" id="editBadgeText" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warna Badge</label>
                            <select name="badge_class" id="editBadgeClass" class="form-select">
                                <option value="bg-primary">Biru (Primary)</option>
                                <option value="bg-danger">Merah (Danger)</option>
                                <option value="bg-warning text-dark">Kuning (Warning)</option>
                                <option value="bg-success">Hijau (Success)</option>
                                <option value="bg-info text-dark">Cyan (Info)</option>
                                <option value="bg-secondary">Abu-abu (Secondary)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Tautan</label>
                            <select name="target" id="editTarget" class="form-select">
                                <option value="_self">Buka di Tab yang Sama (_self)</option>
                                <option value="_blank">Buka di Tab Baru (_blank)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Menu</label>
                            <select name="is_active" id="editIsActive" class="form-select">
                                <option value="1">Aktif Ditampilkan</option>
                                <option value="0">Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- FORM DELETE HIDDEN --}}
<form id="deleteMenuForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    // Open Add Submenu preselected
    function openAddSubmenuModal(parentId, parentTitle) {
        document.getElementById('addParentId').value = parentId;
        const modal = new bootstrap.Modal(document.getElementById('modalAddMenu'));
        modal.show();
    }

    // Open Edit Modal with fetched JSON
    function openEditMenuModal(menuId) {
        fetch(`/master/menus/${menuId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editMenuForm').action = `/master/menus/${menuId}`;
                document.getElementById('editParentId').value = data.parent_id || '';
                document.getElementById('editTitle').value = data.title || '';
                document.getElementById('editRouteName').value = data.route_name || '';
                document.getElementById('editUrl').value = data.url || '';
                document.getElementById('editIcon').value = data.icon || '';
                document.getElementById('editPermissionKey').value = data.permission_key || '';
                document.getElementById('editOrder').value = data.order ?? 0;
                document.getElementById('editBadgeText').value = data.badge_text || '';
                document.getElementById('editBadgeClass').value = data.badge_class || 'bg-primary';
                document.getElementById('editTarget').value = data.target || '_self';
                document.getElementById('editIsActive').value = data.is_active ? '1' : '0';

                const modal = new bootstrap.Modal(document.getElementById('modalEditMenu'));
                modal.show();
            })
            .catch(err => {
                alert('Gagal mengambil data menu: ' + err.message);
            });
    }

    // Confirm Delete
    function confirmDeleteMenu(menuId, title) {
        if (confirm(`Apakah Anda yakin ingin menghapus menu '${title}'? Seluruh submenu di bawahnya juga akan ikut terhapus.`)) {
            const form = document.getElementById('deleteMenuForm');
            form.action = `/master/menus/${menuId}`;
            form.submit();
        }
    }

    // Instant AJAX Toggle Status
    function toggleMenuStatus(menuId) {
        const toggle = document.getElementById(`toggle-${menuId}`);
        const originalState = toggle.checked;

        fetch(`/master/menus/${menuId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`menu-row-${menuId}`);
                if (data.is_active) {
                    row.classList.remove('opacity-75');
                } else {
                    row.classList.add('opacity-75');
                }
            } else {
                toggle.checked = !originalState;
                alert('Gagal mengubah status menu.');
            }
        })
        .catch(err => {
            toggle.checked = !originalState;
            alert('Kesalahan jaringan: ' + err.message);
        });
    }
</script>
@endpush
