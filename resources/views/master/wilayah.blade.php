@extends('layouts.app')

@section('title', 'Master Wilayah (Provinsi & Kabupaten)')

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

<!-- BREADCRUMB & SUMMARY -->
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a>
            </li>
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Master Wilayah</span></li>
        </ol>
    </nav>
    <div class="d-none d-md-flex align-items-center text-muted small gap-3">
        <span><i class="fa-solid fa-map-location-dot text-primary me-1"></i> <strong>{{ $provinsi->count() }}</strong> Provinsi</span>
        <span>&bull;</span>
        <span><i class="fa-solid fa-city text-success me-1"></i> <strong>514</strong> Kota/Kabupaten Terdata</span>
    </div>
</div>

<div class="row">
    <!-- KOLOM KIRI: DAFTAR PROVINSI -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                <div>
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-map me-1 text-primary"></i> Provinsi
                    </h5>
                    <small class="text-muted">{{ $provinsi->count() }} wilayah terdaftar</small>
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProvinsi">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </button>
            </div>

            <!-- SEARCH PROVINSI -->
            <div class="p-3 bg-light border-bottom">
                <form action="{{ route('master.wilayah') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search_prov" class="form-control border-start-0" placeholder="Cari nama / kode provinsi..." value="{{ request('search_prov') }}">
                        @if(request('search_prov'))
                            <a href="{{ route('master.wilayah') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                        <button class="btn btn-secondary" type="submit">Cari</button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 620px; overflow-y: auto;">
                    @forelse($provinsi as $p)
                    <a href="{{ route('master.wilayah', array_merge(request()->query(), ['provinsi_id' => $p->id])) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 px-3 {{ $p->id == $selectedProvId ? 'active bg-primary text-white border-primary' : '' }}">
                        <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                            <span class="badge {{ $p->id == $selectedProvId ? 'bg-light text-primary' : 'bg-secondary' }} px-2 py-1">
                                {{ $p->kode_provinsi ?: '-' }}
                            </span>
                            <span class="fw-semibold text-truncate">{{ $p->nama_provinsi }}</span>
                            @if(!$p->is_active)
                                <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem;">Nonaktif</span>
                            @endif
                        </div>
                        <span class="badge {{ $p->id == $selectedProvId ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} rounded-pill">
                            {{ $p->kabupaten_count }} Kota/Kab
                        </span>
                    </a>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fa-solid fa-map-location-dot fa-2x mb-2 text-secondary"></i>
                        <p class="mb-0 small">Provinsi tidak ditemukan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: DETAIL PROVINSI & DAFTAR KOTA/KABUPATEN -->
    <div class="col-lg-8 mb-4">
        @if($activeProvinsi)
        <!-- HEADER DETAIL PROVINSI TERPILIH -->
        <div class="card shadow-sm border-0 mb-3 bg-white">
            <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-1">Kode: {{ $activeProvinsi->kode_provinsi ?: 'Belum diatur' }}</span>
                        <h4 class="mb-0 fw-bold text-dark">{{ $activeProvinsi->nama_provinsi }}</h4>
                        @if($activeProvinsi->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        <span><i class="fa-solid fa-city me-1"></i> {{ $activeProvinsi->kabupaten_count }} Kota & Kabupaten</span> &bull;
                        <span><i class="fa-solid fa-sitemap me-1"></i> Pengprov: {{ $activeProvinsi->pengprov_count > 0 ? 'Terdaftar di PBVSI' : 'Belum Terdaftar' }}</span>
                    </div>
                </div>

                <!-- AKSI PROVINSI & TAMBAH KABUPATEN -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary btn-edit-provinsi" 
                            data-id="{{ $activeProvinsi->id }}"
                            data-nama="{{ $activeProvinsi->nama_provinsi }}"
                            data-kode="{{ $activeProvinsi->kode_provinsi }}"
                            data-active="{{ $activeProvinsi->is_active ? '1' : '0' }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditProvinsi">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Provinsi
                    </button>
                    
                    <form action="{{ route('master.wilayah.provinsi.destroy', $activeProvinsi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Provinsi {{ $activeProvinsi->nama_provinsi }}? Semua data kota/kabupaten di bawahnya akan ikut terhapus!');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $activeProvinsi->pengprov_count > 0 ? 'disabled title="Tidak bisa dihapus karena memiliki Pengprov terdaftar"' : '' }}>
                            <i class="fa-solid fa-trash-can me-1"></i> Hapus
                        </button>
                    </form>

                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKabupaten">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Kota/Kab
                    </button>
                </div>
            </div>
        </div>

        <!-- DAFTAR KOTA / KABUPATEN -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.wilayah') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="provinsi_id" value="{{ $selectedProvId }}">
                    @if(request('search_prov'))
                        <input type="hidden" name="search_prov" value="{{ request('search_prov') }}">
                    @endif

                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search_kab" class="form-control" placeholder="Cari nama / kode kota & kab..." value="{{ request('search_kab') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <select name="tipe" class="form-select form-select-sm">
                            <option value="">Semua Tipe Wilayah</option>
                            <option value="KABUPATEN" {{ request('tipe') == 'KABUPATEN' ? 'selected' : '' }}>KABUPATEN</option>
                            <option value="KOTA" {{ request('tipe') == 'KOTA' ? 'selected' : '' }}>KOTA</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request('search_kab') || request('tipe'))
                            <a href="{{ route('master.wilayah', ['provinsi_id' => $selectedProvId]) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 120px;">Kode</th>
                                <th>Nama Kabupaten / Kota</th>
                                <th style="width: 140px;">Tipe</th>
                                <th class="text-end" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kabupaten as $idx => $kab)
                            <tr>
                                <td class="text-center text-muted">{{ $kabupaten->firstItem() + $idx }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $kab->kode_kabupaten ?: '-' }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $kab->nama_kabupaten }}</strong>
                                </td>
                                <td>
                                    @if($kab->tipe == 'KOTA')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                            <i class="fa-solid fa-building me-1"></i> KOTA
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <i class="fa-solid fa-tree me-1"></i> KABUPATEN
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-edit-kabupaten me-1" 
                                            data-id="{{ $kab->id }}"
                                            data-nama="{{ $kab->nama_kabupaten }}"
                                            data-tipe="{{ $kab->tipe }}"
                                            data-kode="{{ $kab->kode_kabupaten }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditKabupaten"
                                            title="Edit Kota/Kabupaten">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('master.wilayah.kabupaten.destroy', $kab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $kab->nama_kabupaten }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kota/Kabupaten">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-circle-info fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Kabupaten / Kota yang sesuai dengan kriteria filter.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kabupaten->hasPages())
                <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                    <div class="small text-muted">
                        Menampilkan {{ $kabupaten->firstItem() ?? 0 }} - {{ $kabupaten->lastItem() ?? 0 }} dari total {{ $kabupaten->total() }} daerah
                    </div>
                    <div>
                        {{ $kabupaten->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5 text-muted">
                <i class="fa-solid fa-map fa-3x mb-3 text-secondary"></i>
                <h5 class="fw-bold">Pilih Provinsi</h5>
                <p>Silakan pilih salah satu provinsi pada daftar di sebelah kiri untuk melihat dan mengelola data Kota/Kabupaten.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH PROVINSI                      -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahProvinsi" tabindex="-1" aria-labelledby="modalTambahProvinsiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('master.wilayah.provinsi.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahProvinsiLabel">
                        <i class="fa-solid fa-plus-circle text-primary me-1"></i> Tambah Provinsi Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Provinsi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_provinsi" class="form-control" placeholder="Contoh: JAWA TIMUR" required>
                        <small class="text-muted">Nama provinsi akan otomatis disimpan dalam huruf kapital.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Provinsi (Kemendagri / BPS)</label>
                        <input type="text" name="kode_provinsi" class="form-control" placeholder="Contoh: 35">
                        <small class="text-muted">Opsional. Kode resmi wilayah provinsi 2 digit.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Provinsi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT PROVINSI (DYNAMIC)              -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditProvinsi" tabindex="-1" aria-labelledby="modalEditProvinsiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditProvinsi" action="{{ $activeProvinsi ? route('master.wilayah.provinsi.update', $activeProvinsi->id) : '#' }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditProvinsiLabel">
                        <i class="fa-solid fa-pen-to-square text-primary me-1"></i> Edit Data Provinsi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Provinsi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_provinsi" id="edit_nama_provinsi" class="form-control" value="{{ $activeProvinsi?->nama_provinsi }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Provinsi</label>
                        <input type="text" name="kode_provinsi" id="edit_kode_provinsi" class="form-control" value="{{ $activeProvinsi?->kode_provinsi }}">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="edit_is_active" value="1" {{ $activeProvinsi?->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="edit_is_active">Status Wilayah Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Provinsi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH KABUPATEN / KOTA              -->
<!-- ========================================== -->
@if($activeProvinsi)
<div class="modal fade" id="modalTambahKabupaten" tabindex="-1" aria-labelledby="modalTambahKabupatenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('master.wilayah.kabupaten.store') }}" method="POST">
            @csrf
            <input type="hidden" name="provinsi_id" value="{{ $activeProvinsi->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahKabupatenLabel">
                        <i class="fa-solid fa-plus-circle text-primary me-1"></i> Tambah Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Provinsi Induk</label>
                        <input type="text" class="form-control bg-light" value="{{ $activeProvinsi->nama_provinsi }}" readonly>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select" required>
                                <option value="KABUPATEN" selected>KABUPATEN</option>
                                <option value="KOTA">KOTA</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Kode Daerah</label>
                            <input type="text" name="kode_kabupaten" class="form-control" placeholder="Contoh: 35.15">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kabupaten / Kota <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kabupaten" class="form-control" placeholder="Contoh: SIDOARJO atau KABUPATEN SIDOARJO" required>
                        <small class="text-muted">Awalan KOTA / KABUPATEN akan disesuaikan secara otomatis jika belum diketik.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kota/Kab</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- MODAL EDIT KABUPATEN / KOTA (DYNAMIC)      -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditKabupaten" tabindex="-1" aria-labelledby="modalEditKabupatenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditKabupaten" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditKabupatenLabel">
                        <i class="fa-solid fa-pen-to-square text-primary me-1"></i> Edit Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe" id="edit_tipe" class="form-select" required>
                                <option value="KABUPATEN">KABUPATEN</option>
                                <option value="KOTA">KOTA</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Kode Daerah</label>
                            <input type="text" name="kode_kabupaten" id="edit_kode_kabupaten" class="form-control" placeholder="Contoh: 35.15">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kabupaten / Kota <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kabupaten" id="edit_nama_kabupaten" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Handler Modal Edit Provinsi
    const editProvModal = document.getElementById('modalEditProvinsi');
    if (editProvModal) {
        editProvModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const kode = button.getAttribute('data-kode');
            const active = button.getAttribute('data-active');

            if (id) {
                const form = document.getElementById('formEditProvinsi');
                form.action = "{{ url('master/wilayah/provinsi') }}/" + id;
                if (nama) document.getElementById('edit_nama_provinsi').value = nama;
                if (kode !== null) document.getElementById('edit_kode_provinsi').value = kode || '';
                document.getElementById('edit_is_active').checked = (active === '1');
            }
        });
    }

    // 2. Handler Modal Edit Kabupaten
    const editKabModal = document.getElementById('modalEditKabupaten');
    if (editKabModal) {
        editKabModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const tipe = button.getAttribute('data-tipe');
            const kode = button.getAttribute('data-kode');

            const form = document.getElementById('formEditKabupaten');
            form.action = "{{ url('master/wilayah/kabupaten') }}/" + id;

            document.getElementById('edit_nama_kabupaten').value = nama || '';
            document.getElementById('edit_tipe').value = tipe || 'KABUPATEN';
            document.getElementById('edit_kode_kabupaten').value = kode || '';
        });
    }
});
</script>
@endpush
