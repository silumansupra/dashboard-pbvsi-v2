@extends('layouts.app')

@section('title', 'Master Tim & Klub Bola Voli')

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
            <li class="breadcrumb-item text-secondary">Organisasi</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Data Klub & Tim</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKlub">
            <i class="fa-solid fa-plus me-1"></i> Tambah Klub Baru
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.klub') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="pengprov_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Pengprov Induk</option>
                            @foreach($pengprovList as $pp)
                                <option value="{{ $pp->id }}" {{ request('pengprov_id') == $pp->id ? 'selected' : '' }}>
                                    {{ $pp->nama_pengprov }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama klub, kode, pemilik..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select name="status_verifikasi" class="form-select form-select-sm">
                            <option value="">Semua Verifikasi</option>
                            <option value="VERIFIED" {{ request('status_verifikasi') == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                            <option value="PENDING" {{ request('status_verifikasi') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                            <option value="REJECTED" {{ request('status_verifikasi') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
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
                        @if(request('pengprov_id') || request('search') || request('status_verifikasi') || request('per_page'))
                            <a href="{{ route('master.klub') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR KLUB -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 140px;">Kode Klub</th>
                                <th>Nama Tim / Klub</th>
                                <th>Pengprov / Pengkot Asal</th>
                                <th>Pemilik & Sekretaris</th>
                                <th class="text-center" style="width: 130px;">Verifikasi</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klub as $idx => $k)
                            <tr>
                                <td class="text-center text-muted">{{ $klub->firstItem() + $idx }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ $k->kode_klub }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $k->nama_klub }}</strong><br>
                                            <small class="text-muted">{{ $k->alamat ? Str::limit($k->alamat, 35) : 'Alamat belum diatur' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong>{{ $k->pengprov?->nama_pengprov ?? '-' }}</strong></div>
                                    <small class="text-muted">{{ $k->pengkot?->nama_pengkot ?? 'Pengkot/Pengkab belum ditentukan' }}</small>
                                </td>
                                <td>
                                    <div><strong>Pemilik:</strong> {{ $k->pemilik_klub ?: '-' }}</div>
                                    <div class="small text-muted"><strong>Sekretaris:</strong> {{ $k->sekretaris_klub ?: '-' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($k->status_verifikasi === 'VERIFIED')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> TERVERIFIKASI
                                        </span>
                                    @elseif($k->status_verifikasi === 'REJECTED')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-edit-klub me-1" 
                                            data-id="{{ $k->id }}"
                                            data-kode="{{ $k->kode_klub }}"
                                            data-nama="{{ $k->nama_klub }}"
                                            data-pengprov-id="{{ $k->pengprov_id }}"
                                            data-pengkot-id="{{ $k->pengkot_id }}"
                                            data-pemilik="{{ $k->pemilik_klub }}"
                                            data-sekretaris="{{ $k->sekretaris_klub }}"
                                            data-alamat="{{ $k->alamat }}"
                                            data-verifikasi="{{ $k->status_verifikasi }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditKlub"
                                            title="Edit Data Klub">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('master.klub.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus klub {{ $k->nama_klub }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Klub">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-shield-halved fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Klub yang ditemukan sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($klub->hasPages() || $klub->total() > 0)
                <div class="p-3 border-top bg-light">
                    {{ $klub->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH KLUB                          -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahKlub" tabindex="-1" aria-labelledby="modalTambahKlubLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('master.klub.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalTambahKlubLabel">
                        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Tim / Klub Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Klub / Tim <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klub" class="form-control" placeholder="Contoh: BINA REMAJA VOLLEYBALL CLUB" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Unik Klub</label>
                            <input type="text" name="kode_klub" class="form-control text-uppercase" placeholder="Otomatis jika dikosongkan (e.g. KLB-XXXXXX)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengprov Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" class="form-select" required>
                                <option value="">-- Pilih Pengprov Asal --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}" {{ request('pengprov_id') == $pp->id ? 'selected' : '' }}>
                                        {{ $pp->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                            <select name="status_verifikasi" class="form-select" required>
                                <option value="VERIFIED" selected>VERIFIED (Terverifikasi)</option>
                                <option value="PENDING">PENDING (Menunggu Verifikasi)</option>
                                <option value="REJECTED">REJECTED (Ditolak)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pemilik / Manajer Klub</label>
                            <input type="text" name="pemilik_klub" class="form-control" placeholder="Nama pemilik klub">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Tim</label>
                            <input type="text" name="sekretaris_klub" class="form-control" placeholder="Nama sekretaris tim">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Basecamp / Lapangan Klub</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat basecamp atau GOR klub..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Klub</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT KLUB (DYNAMIC)                  -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditKlub" tabindex="-1" aria-labelledby="modalEditKlubLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formEditKlub" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalEditKlubLabel">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data Tim / Klub
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Klub / Tim <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klub" id="edit_klub_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Unik Klub</label>
                            <input type="text" name="kode_klub" id="edit_klub_kode" class="form-control text-uppercase" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengprov Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" id="edit_klub_pengprov_id" class="form-select" required>
                                <option value="">-- Pilih Pengprov Asal --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->nama_pengprov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                            <select name="status_verifikasi" id="edit_klub_verifikasi" class="form-select" required>
                                <option value="VERIFIED">VERIFIED (Terverifikasi)</option>
                                <option value="PENDING">PENDING (Menunggu Verifikasi)</option>
                                <option value="REJECTED">REJECTED (Ditolak)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pemilik / Manajer Klub</label>
                            <input type="text" name="pemilik_klub" id="edit_klub_pemilik" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Tim</label>
                            <input type="text" name="sekretaris_klub" id="edit_klub_sekretaris" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Basecamp / Lapangan Klub</label>
                            <textarea name="alamat" id="edit_klub_alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Klub</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('modalEditKlub');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const form = document.getElementById('formEditKlub');
            form.action = "{{ url('master/klub') }}/" + id;

            document.getElementById('edit_klub_nama').value = button.getAttribute('data-nama') || '';
            document.getElementById('edit_klub_kode').value = button.getAttribute('data-kode') || '';
            document.getElementById('edit_klub_pengprov_id').value = button.getAttribute('data-pengprov-id') || '';
            document.getElementById('edit_klub_verifikasi').value = button.getAttribute('data-verifikasi') || 'VERIFIED';
            document.getElementById('edit_klub_pemilik').value = button.getAttribute('data-pemilik') || '';
            document.getElementById('edit_klub_sekretaris').value = button.getAttribute('data-sekretaris') || '';
            document.getElementById('edit_klub_alamat').value = button.getAttribute('data-alamat') || '';
        });
    }
});
</script>
@endpush
