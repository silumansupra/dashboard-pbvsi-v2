@extends('layouts.app')

@section('title', 'Master Jabatan & Peran PBVSI')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Master Jabatan</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJabatan">
            <i class="fa-solid fa-plus me-1"></i> Tambah Jabatan Baru
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.jabatan') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama jabatan..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="is_official_tim" class="form-select form-select-sm">
                            <option value="">Semua Kategori Penugasan</option>
                            <option value="1" {{ request('is_official_tim') === '1' ? 'selected' : '' }}>Official & Pelatih Tim</option>
                            <option value="0" {{ request('is_official_tim') === '0' ? 'selected' : '' }}>Pengurus PBVSI / Panitia</option>
                        </select>
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
                        @if(request('search') || request('is_official_tim') !== null || request('is_active') !== null || request('per_page'))
                            <a href="{{ route('master.jabatan') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR JABATAN -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Jabatan</th>
                                <th>Kategori Penugasan</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jabatan as $idx => $j)
                            <tr>
                                <td class="text-center text-muted">{{ $jabatan->firstItem() + $idx }}</td>
                                <td>
                                    <strong class="text-dark">{{ $j->nama_jabatan }}</strong>
                                </td>
                                <td>
                                    @if($j->is_official_tim)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <i class="fa-solid fa-user-tie me-1"></i> Official Tim / Pelatih
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <i class="fa-solid fa-building me-1"></i> Pengurus PBVSI / Panitia
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($j->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-check me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-edit-jabatan me-1" 
                                            data-id="{{ $j->id }}"
                                            data-nama="{{ $j->nama_jabatan }}"
                                            data-official="{{ $j->is_official_tim ? '1' : '0' }}"
                                            data-active="{{ $j->is_active ? '1' : '0' }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditJabatan"
                                            title="Edit Jabatan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('master.jabatan.destroy', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan {{ $j->nama_jabatan }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Jabatan">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-id-badge fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data jabatan yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($jabatan->hasPages() || $jabatan->total() > 0)
                <div class="p-3 border-top bg-light">
                    {{ $jabatan->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH JABATAN                       -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahJabatan" tabindex="-1" aria-labelledby="modalTambahJabatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('master.jabatan.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalTambahJabatanLabel">
                        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Jabatan Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jabatan" class="form-control" placeholder="Contoh: HEAD COACH / PELATIH KEPALA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Penugasan <span class="text-danger">*</span></label>
                        <select name="is_official_tim" class="form-select" required>
                            <option value="1" selected>Official Tim & Pelatih</option>
                            <option value="0">Pengurus PBVSI / Panitia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Aktif <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Jabatan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT JABATAN (DYNAMIC)               -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditJabatan" tabindex="-1" aria-labelledby="modalEditJabatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditJabatan" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalEditJabatanLabel">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Jabatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jabatan" id="edit_jabatan_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Penugasan <span class="text-danger">*</span></label>
                        <select name="is_official_tim" id="edit_jabatan_official" class="form-select" required>
                            <option value="1">Official Tim & Pelatih</option>
                            <option value="0">Pengurus PBVSI / Panitia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Aktif <span class="text-danger">*</span></label>
                        <select name="is_active" id="edit_jabatan_active" class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Jabatan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('modalEditJabatan');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const form = document.getElementById('formEditJabatan');
            form.action = "{{ url('master/jabatan') }}/" + id;

            document.getElementById('edit_jabatan_nama').value = button.getAttribute('data-nama') || '';
            document.getElementById('edit_jabatan_official').value = button.getAttribute('data-official') || '1';
            document.getElementById('edit_jabatan_active').value = button.getAttribute('data-active') || '1';
        });
    }
});
</script>
@endpush
