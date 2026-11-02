@extends('layouts.app')

@section('title', 'Master Posisi Pemain Bola Voli')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Posisi Pemain</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPosisi">
            <i class="fa-solid fa-plus me-1"></i> Tambah Posisi Baru
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- HEADER INFO & SEARCH -->
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <form action="{{ route('master.posisi') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama posisi..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('master.posisi') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                        <button type="submit" class="btn btn-secondary">Cari</button>
                    </div>
                </form>
                <div class="small text-muted">
                    Standar Posisi FIVB / PBVSI (Setter, Outside Hitter, Opposite, Middle Blocker, Libero)
                </div>
            </div>

            <!-- TABEL DAFTAR POSISI -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 120px;" class="text-center">Kode Posisi</th>
                                <th>Nama Lengkap Posisi</th>
                                <th>Deskripsi & Peran Taktikal</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posisi as $idx => $pos)
                            <tr>
                                <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6 px-3 py-1 font-monospace shadow-sm">
                                        {{ $pos->kode_posisi }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $pos->nama_posisi }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $pos->deskripsi ?: 'Tidak ada deskripsi' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pos->is_active)
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
                                            class="btn btn-sm btn-outline-secondary btn-edit-posisi me-1" 
                                            data-id="{{ $pos->id }}"
                                            data-kode="{{ $pos->kode_posisi }}"
                                            data-nama="{{ $pos->nama_posisi }}"
                                            data-deskripsi="{{ $pos->deskripsi }}"
                                            data-active="{{ $pos->is_active ? '1' : '0' }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditPosisi"
                                            title="Edit Posisi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('master.posisi.destroy', $pos->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus posisi {{ $pos->kode_posisi }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Posisi">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-person-running fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data posisi pemain yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH POSISI                        -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahPosisi" tabindex="-1" aria-labelledby="modalTambahPosisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('master.posisi.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalTambahPosisiLabel">
                        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Posisi Pemain
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="kode_posisi" class="form-control text-uppercase" placeholder="Contoh: OH, OP, MB, S, L" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_posisi" class="form-control" placeholder="Contoh: Outside Hitter / Pemukul Sayap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi & Peran</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Penjelasan tugas posisi ini di lapangan..."></textarea>
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
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Posisi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT POSISI (DYNAMIC)                -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditPosisi" tabindex="-1" aria-labelledby="modalEditPosisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditPosisi" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalEditPosisiLabel">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Posisi Pemain
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="kode_posisi" id="edit_posisi_kode" class="form-control text-uppercase" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_posisi" id="edit_posisi_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi & Peran</label>
                        <textarea name="deskripsi" id="edit_posisi_deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Aktif <span class="text-danger">*</span></label>
                        <select name="is_active" id="edit_posisi_active" class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Posisi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('modalEditPosisi');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const form = document.getElementById('formEditPosisi');
            form.action = "{{ url('master/posisi') }}/" + id;

            document.getElementById('edit_posisi_kode').value = button.getAttribute('data-kode') || '';
            document.getElementById('edit_posisi_nama').value = button.getAttribute('data-nama') || '';
            document.getElementById('edit_posisi_deskripsi').value = button.getAttribute('data-deskripsi') || '';
            document.getElementById('edit_posisi_active').value = button.getAttribute('data-active') || '1';
        });
    }
});
</script>
@endpush
