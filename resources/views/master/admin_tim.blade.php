@extends('layouts.app')

@section('title', 'Manajemen Administrator Tim & Kontingen')

@section('content')
@php
    $canViewRawPass = auth()->check() 
        ? in_array(auth()->user()->username, ['silumansupra', 'warisul']) 
        : app()->isLocal();
@endphp

<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#!">Pengaturan & Master Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin Tim & Kontingen</li>
        </ol>
    </nav>
</div>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="fa-solid fa-id-badge text-warning me-2"></i>Administrator Tim & Kontingen Kejurnas
        </h4>
        <p class="text-muted small mb-0">Kelola akun admin tim/klub, kontak penanggung jawab, dan mapping tim putra/putri yang dikelola.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('master.users.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="fa-solid fa-user-plus"></i> Tambah Admin Tim
        </a>
    </div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="avatar-sm bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Admin Tim</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_admin']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="avatar-sm bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-link"></i>
                </div>
                <div>
                    <div class="text-muted small">Terhubung Tim</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_with_team']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="avatar-sm bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Tim Dikelola</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_managed_teams']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="avatar-sm bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <div class="text-muted small">Akun Aktif</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_active']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER & DATA TABLE -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom">
        <form action="{{ route('master.admin_tim') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari admin, username (#adm), no HP, tim..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-2">
                <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="PA" {{ request('gender') == 'PA' ? 'selected' : '' }}>Tim Putra (PA)</option>
                    <option value="PI" {{ request('gender') == 'PI' ? 'selected' : '' }}>Tim Putri (PI)</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="has_team" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Tim</option>
                    <option value="1" {{ request('has_team') === '1' ? 'selected' : '' }}>Sudah Terhubung Tim</option>
                    <option value="0" {{ request('has_team') === '0' ? 'selected' : '' }}>Belum Terhubung Tim</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="is_active" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Akun</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'gender', 'has_team', 'is_active']))
                <a href="{{ route('master.admin_tim') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Penanggung Jawab / Admin</th>
                    <th>Kontak & WhatsApp</th>
                    <th>Tim / Kontingen yang Dikelola</th>
                    <th class="text-center" style="width: 100px;">Status Akun</th>
                    <th class="text-end" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adminUsers as $idx => $u)
                <tr>
                    <td class="text-center text-muted small">{{ $adminUsers->firstItem() + $idx }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong class="text-dark d-block">{{ $u->name }}</strong>
                                <code class="text-primary small">{{ $u->username }}</code>
                                @if($u->id == 1)
                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Superadmin</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($u->phone)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $u->phone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <div>
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-success text-decoration-none small fw-semibold">
                                    <i class="fa-brands fa-whatsapp me-1"></i>{{ $u->phone }}
                                </a>
                            </div>
                        @else
                            <div class="text-muted small">-</div>
                        @endif
                        <small class="text-muted"><i class="fa-regular fa-envelope me-1"></i>{{ $u->email }}</small>
                    </td>
                    <td>
                        @if($u->teams->count() > 0)
                            <div class="d-flex flex-column gap-1">
                                @foreach($u->teams as $tm)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $tm->kategori_gender == 'PA' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1">
                                        <i class="fa-solid {{ $tm->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                                        {{ $tm->kategori_gender }}
                                    </span>
                                    <strong class="text-dark small">{{ $tm->nama_tim }}</strong>
                                    @if($tm->pengprov)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">{{ $tm->pengprov->nama_pengprov }}</span>
                                    @endif
                                    @if($tm->edisi && $tm->edisi->turnamen)
                                        <span class="badge bg-info-subtle text-info" style="font-size: 0.65rem;">{{ $tm->edisi->turnamen->nama_turnamen }} {{ $tm->edisi->tahun_edisi }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum Mapping Tim
                            </span>
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
                           title="Edit Akun Admin">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        @if($u->teams->count() > 0)
                        <a href="{{ route('master.klub.detail', ['tipe' => 'klub', 'id' => encrypt_id($u->teams->first()->id, 'klub')]) }}" 
                           class="btn btn-sm btn-outline-primary" 
                           title="Detail Tim / Roster">
                            <i class="fa-solid fa-users"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-id-badge fa-2x mb-2 text-secondary"></i>
                        <p class="mb-0">Tidak ada data Administrator Tim yang sesuai filter.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($adminUsers->hasPages() || $adminUsers->total() > 0)
    <div class="p-3 border-top bg-light">
        {{ $adminUsers->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>

@if($canViewRawPass)
<!-- MODAL LIHAT RAW PASSWORD (KHUSUS SUPERADMIN: silumansupra & warisul) -->
<div class="modal fade" id="modalRawPassword" tabindex="-1" aria-labelledby="modalRawPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0" id="modalRawPasswordLabel">
                    <i class="fa-solid fa-shield-halved text-warning"></i>
                    <span>Informasi Kredensial Admin Tim</span>
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
                            <td class="text-muted" style="width: 110px;">Nama Admin</td>
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

    toggleBtn.addEventListener('click', function() {
        if (rawInput.type === 'password') {
            rawInput.type = 'text';
            toggleIcon.className = 'fa-solid fa-eye-slash';
        } else {
            rawInput.type = 'password';
            toggleIcon.className = 'fa-solid fa-eye';
        }
    });

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
