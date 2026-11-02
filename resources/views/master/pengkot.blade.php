@extends('layouts.app')

@section('title', 'Master Pengurus Kota / Kabupaten (Pengkot/Pengkab)')

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
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Pengkot / Pengkab</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengkot">
            <i class="fa-solid fa-plus me-1"></i> Tambah Pengkot/Pengkab
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- FILTER TOOLBAR -->
            <div class="card-header bg-white py-3 border-bottom">
                <form action="{{ route('master.pengkot') }}" method="GET" class="row g-2 align-items-center">
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
                            <input type="text" name="search" class="form-control" placeholder="Cari nama pengkot, ketum..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select name="status_aktif" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status_aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
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
                        @if(request('pengprov_id') || request('search') || request('status_aktif') || request('per_page'))
                            <a href="{{ route('master.pengkot') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR PENGKOT -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Pengurus & Wilayah</th>
                                <th>Pengprov Induk</th>
                                <th>Ketua & Sekretaris</th>
                                <th>Kontak HP / Telp</th>
                                <th class="text-center" style="width: 100px;">Klub Binaan</th>
                                <th class="text-center" style="width: 110px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengkot as $idx => $pk)
                            <tr>
                                <td class="text-center text-muted">{{ $pengkot->firstItem() + $idx }}</td>
                                <td>
                                    <strong class="text-dark">{{ $pk->nama_pengkot }}</strong><br>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-city me-1 text-secondary"></i>
                                        {{ $pk->kabupaten?->nama_kabupaten ?? 'Wilayah belum diatur' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $pk->pengprov?->nama_pengprov ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div><strong>Ketum:</strong> {{ $pk->ketum ?: '-' }}</div>
                                    <div class="small text-muted"><strong>Sekum:</strong> {{ $pk->sekum ?: '-' }}</div>
                                </td>
                                <td>
                                    @if($pk->nohp_ketum || $pk->nohp_sekum || $pk->no_telp)
                                        <div class="small"><i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $pk->nohp_ketum ?: ($pk->nohp_sekum ?: $pk->no_telp) }}</div>
                                    @endif
                                    @if($pk->email)
                                        <div class="small text-muted"><i class="fa-regular fa-envelope me-1"></i> {{ $pk->email }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        {{ $pk->klub_count }} Klub
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pk->status_aktif)
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
                                            class="btn btn-sm btn-outline-secondary btn-edit-pengkot me-1" 
                                            data-id="{{ $pk->id }}"
                                            data-pengprov-id="{{ $pk->pengprov_id }}"
                                            data-kabupaten-id="{{ $pk->kabupaten_id }}"
                                            data-nama="{{ $pk->nama_pengkot }}"
                                            data-ketum="{{ $pk->ketum }}"
                                            data-sekum="{{ $pk->sekum }}"
                                            data-nohp-ketum="{{ $pk->nohp_ketum }}"
                                            data-nohp-sekum="{{ $pk->nohp_sekum }}"
                                            data-email="{{ $pk->email }}"
                                            data-no-telp="{{ $pk->no_telp }}"
                                            data-alamat="{{ $pk->alamat }}"
                                            data-status="{{ $pk->status_aktif ? '1' : '0' }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditPengkot"
                                            title="Edit Data Pengkot/Pengkab">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('master.pengkot.destroy', $pk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $pk->nama_pengkot }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $pk->klub_count > 0 ? 'disabled title="Tidak bisa dihapus karena memiliki klub binaan"' : 'title="Hapus Data"' }}>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-city fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data Pengurus Kota/Kabupaten yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pengkot->hasPages() || $pengkot->total() > 0)
                <div class="p-3 border-top bg-light">
                    {{ $pengkot->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- ========================================== -->
<!-- MODAL TAMBAH PENGKOT / PENGKAB             -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambahPengkot" tabindex="-1" aria-labelledby="modalTambahPengkotLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('master.pengkot.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalTambahPengkotLabel">
                        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Pengurus Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengprov Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" id="tambah_pengkot_pengprov_id" class="form-select" required>
                                <option value="">-- Pilih Pengprov Induk --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}" {{ request('pengprov_id') == $pp->id ? 'selected' : '' }}>
                                        {{ $pp->nama_pengprov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Daerah Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten_id" id="tambah_pengkot_kabupaten_id" class="form-select" required>
                                <option value="">-- Pilih Pengprov terlebih dahulu --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Resmi Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengkot" id="tambah_nama_pengkot" class="form-control" placeholder="Contoh: PENGKOT PBVSI SURABAYA" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ketua Umum (Ketum)</label>
                            <input type="text" name="ketum" class="form-control" placeholder="Nama Ketum">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Umum (Sekum)</label>
                            <input type="text" name="sekum" class="form-control" placeholder="Nama Sekum">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Ketum</label>
                            <input type="text" name="nohp_ketum" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Sekum</label>
                            <input type="text" name="nohp_sekum" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="pengkot@pbvsi.or.id">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Telp Kantor</label>
                            <input type="text" name="no_telp" class="form-control" placeholder="(031) xxxxxxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Sekretariat</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat sekretariat..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengkot</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT PENGKOT / PENGKAB (DYNAMIC)     -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditPengkot" tabindex="-1" aria-labelledby="modalEditPengkotLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formEditPengkot" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white" id="modalEditPengkotLabel">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Pengurus Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengprov Induk <span class="text-danger">*</span></label>
                            <select name="pengprov_id" id="edit_pengkot_pengprov_id" class="form-select" required>
                                <option value="">-- Pilih Pengprov Induk --</option>
                                @foreach($pengprovList as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->nama_pengprov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Daerah Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten_id" id="edit_pengkot_kabupaten_id" class="form-select" required>
                                <option value="">-- Memuat daerah... --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Resmi Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengkot" id="edit_pengkot_nama" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ketua Umum (Ketum)</label>
                            <input type="text" name="ketum" id="edit_pengkot_ketum" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekretaris Umum (Sekum)</label>
                            <input type="text" name="sekum" id="edit_pengkot_sekum" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Ketum</label>
                            <input type="text" name="nohp_ketum" id="edit_pengkot_nohp_ketum" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WA Sekum</label>
                            <input type="text" name="nohp_sekum" id="edit_pengkot_nohp_sekum" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="edit_pengkot_email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Telp Kantor</label>
                            <input type="text" name="no_telp" id="edit_pengkot_no_telp" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status_aktif" id="edit_pengkot_status" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Sekretariat</label>
                            <textarea name="alamat" id="edit_pengkot_alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Pengkot</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function loadKabupaten(pengprovId, targetSelectId, selectedKabId = null) {
        const targetSelect = document.getElementById(targetSelectId);
        if (!pengprovId) {
            targetSelect.innerHTML = '<option value="">-- Pilih Pengprov terlebih dahulu --</option>';
            return;
        }

        targetSelect.innerHTML = '<option value="">Memuat daerah...</option>';

        fetch("{{ url('master/api/kabupaten-by-pengprov') }}/" + pengprovId)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Pilih Kota / Kabupaten --</option>';
                data.forEach(item => {
                    const isSelected = selectedKabId && item.id == selectedKabId ? 'selected' : '';
                    options += `<option value="${item.id}" ${isSelected}>${item.nama_kabupaten} (${item.tipe})</option>`;
                });
                targetSelect.innerHTML = options;
            })
            .catch(() => {
                targetSelect.innerHTML = '<option value="">Gagal memuat daerah</option>';
            });
    }

    // Modal Tambah: event listener saat pengprov berubah
    const tambahPengprovSelect = document.getElementById('tambah_pengkot_pengprov_id');
    if (tambahPengprovSelect) {
        tambahPengprovSelect.addEventListener('change', function() {
            loadKabupaten(this.value, 'tambah_pengkot_kabupaten_id');
        });

        // Trigger if already selected on page load
        if (tambahPengprovSelect.value) {
            loadKabupaten(tambahPengprovSelect.value, 'tambah_pengkot_kabupaten_id');
        }
    }

    // Auto set nama pengkot saat kabupaten dipilih pada modal tambah
    const tambahKabSelect = document.getElementById('tambah_pengkot_kabupaten_id');
    const tambahNamaInput = document.getElementById('tambah_nama_pengkot');
    if (tambahKabSelect && tambahNamaInput) {
        tambahKabSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex]?.text || '';
            if (selectedText.includes('(')) {
                const nama = selectedText.split('(')[0].trim();
                const tipe = selectedText.includes('(KOTA)') ? 'PENGKOT PBVSI ' : 'PENGKAB PBVSI ';
                const cleanedName = nama.replace(/^KOTA\s+/i, '').replace(/^KABUPATEN\s+/i, '').replace(/^KAB\.\s+/i, '');
                tambahNamaInput.value = (tipe + cleanedName).toUpperCase();
            }
        });
    }

    // Modal Edit: populate data
    const editModal = document.getElementById('modalEditPengkot');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const pengprovId = button.getAttribute('data-pengprov-id');
            const kabId = button.getAttribute('data-kabupaten-id');

            const form = document.getElementById('formEditPengkot');
            form.action = "{{ url('master/pengkot') }}/" + id;

            document.getElementById('edit_pengkot_pengprov_id').value = pengprovId || '';
            document.getElementById('edit_pengkot_nama').value = button.getAttribute('data-nama') || '';
            document.getElementById('edit_pengkot_ketum').value = button.getAttribute('data-ketum') || '';
            document.getElementById('edit_pengkot_sekum').value = button.getAttribute('data-sekum') || '';
            document.getElementById('edit_pengkot_nohp_ketum').value = button.getAttribute('data-nohp-ketum') || '';
            document.getElementById('edit_pengkot_nohp_sekum').value = button.getAttribute('data-nohp-sekum') || '';
            document.getElementById('edit_pengkot_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_pengkot_no_telp').value = button.getAttribute('data-no-telp') || '';
            document.getElementById('edit_pengkot_alamat').value = button.getAttribute('data-alamat') || '';
            document.getElementById('edit_pengkot_status').value = button.getAttribute('data-status') || '1';

            loadKabupaten(pengprovId, 'edit_pengkot_kabupaten_id', kabId);
        });

        // Event listener saat pengprov diubah di modal edit
        const editPengprovSelect = document.getElementById('edit_pengkot_pengprov_id');
        if (editPengprovSelect) {
            editPengprovSelect.addEventListener('change', function() {
                loadKabupaten(this.value, 'edit_pengkot_kabupaten_id');
            });
        }
    }
});
</script>
@endpush
