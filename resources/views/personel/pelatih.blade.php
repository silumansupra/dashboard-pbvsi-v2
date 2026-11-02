@extends('layouts.app')

@section('title', 'Data Pelatih Bola Voli PBVSI')

@section('content')
<!-- NOTIFIKASI -->
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
            <li class="breadcrumb-item text-secondary">Database Personel</li>
            <li class="breadcrumb-item active"><span class="badge bg-warning text-dark px-2 py-1">Data Pelatih</span></li>
        </ol>
    </nav>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPelatih">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Pelatih Baru
        </button>
    </div>
</div>

<!-- STATS SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Pelatih Terdata</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-2 rounded-3">
                        <i class="fa-solid fa-chalkboard-user fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Pelatih Aktif</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['aktif']) }}</div>
                    </div>
                    <div class="bg-success-subtle text-success p-2 rounded-3">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Lisensi Nasional A</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stats['nasional_a']) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="fa-solid fa-award fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-info border-4 mb-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Lisensi Nasional B / C</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($stats['nasional_b'] + $stats['nasional_c']) }}</div>
                    </div>
                    <div class="bg-info-subtle text-info p-2 rounded-3">
                        <i class="fa-solid fa-medal fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="card shadow-sm border-0">
    <!-- FILTER TOOLBAR -->
    <div class="card-header bg-white py-3 border-bottom">
        <form action="{{ route('personel.pelatih') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama pelatih, lisensi, user..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="pengprov_id" class="form-select form-select-sm">
                    <option value="">-- Semua Pengprov --</option>
                    @foreach($pengprovList as $p)
                        <option value="{{ $p->id }}" {{ request('pengprov_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pengprov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tingkat_pelatih" class="form-select form-select-sm">
                    <option value="">-- Tingkat Lisensi --</option>
                    <option value="Nasional A" {{ request('tingkat_pelatih') == 'Nasional A' ? 'selected' : '' }}>Nasional A</option>
                    <option value="Nasional B" {{ request('tingkat_pelatih') == 'Nasional B' ? 'selected' : '' }}>Nasional B</option>
                    <option value="Nasional C" {{ request('tingkat_pelatih') == 'Nasional C' ? 'selected' : '' }}>Nasional C</option>
                    <option value="FIVB" {{ request('tingkat_pelatih') == 'FIVB' ? 'selected' : '' }}>FIVB Internasional</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_aktif" class="form-select form-select-sm">
                    <option value="">-- Status Aktif --</option>
                    <option value="1" {{ request('status_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status_aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['search', 'tingkat_pelatih', 'pengprov_id', 'status_aktif']))
                    <a href="{{ route('personel.pelatih') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE BODY -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th style="min-width: 150px;">Nomor Lisensi</th>
                        <th style="min-width: 220px;">Nama Pelatih & Akun</th>
                        <th>Asal Pengprov</th>
                        <th>Tingkat Lisensi</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelatih as $index => $row)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $pelatih->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                <i class="fa-solid fa-certificate text-warning me-1"></i>{{ $row->nomor_lisensi_pelatih }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row->user?->name ?? 'Belum ada nama' }}</div>
                            <div class="small text-muted font-monospace">{{ $row->user?->username ?? '-' }}</div>
                        </td>
                        <td>
                            @if($row->pengprov)
                                <span class="small fw-semibold text-dark">{{ $row->pengprov->nama_pengprov }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary small">PP PBVSI Pusat</span>
                            @endif
                        </td>
                        <td>
                            @if(str_contains($row->tingkat_pelatih, 'A'))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">{{ $row->tingkat_pelatih }}</span>
                            @elseif(str_contains($row->tingkat_pelatih, 'B'))
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">{{ $row->tingkat_pelatih }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">{{ $row->tingkat_pelatih }}</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status_aktif)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-edit"
                                    data-id="{{ $row->id }}"
                                    data-nama="{{ $row->user?->name }}"
                                    data-email="{{ $row->user?->email }}"
                                    data-phone="{{ $row->user?->phone }}"
                                    data-lisensi="{{ $row->nomor_lisensi_pelatih }}"
                                    data-tingkat="{{ $row->tingkat_pelatih }}"
                                    data-pengprov-id="{{ $row->pengprov_id }}"
                                    data-status="{{ $row->status_aktif ? 1 : 0 }}"
                                    title="Edit Pelatih">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="{{ $row->id }}"
                                    data-nama="{{ $row->user?->name }}"
                                    title="Hapus Pelatih">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fa-solid fa-chalkboard-user fa-3x text-secondary mb-3 d-block opacity-50"></i>
                            <div class="fw-bold text-muted">Belum ada data pelatih yang sesuai dengan filter pencarian.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    @if($pelatih->hasPages())
    <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            Menampilkan <span class="fw-semibold">{{ $pelatih->firstItem() }}</span> s/d <span class="fw-semibold">{{ $pelatih->lastItem() }}</span> dari total <span class="fw-semibold">{{ number_format($pelatih->total()) }}</span> pelatih
        </div>
        <div>
            {{ $pelatih->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('modals')
<!-- 1. MODAL TAMBAH PELATIH -->
<div class="modal fade" id="modalTambahPelatih" tabindex="-1" aria-labelledby="modalTambahPelatihLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('personel.pelatih.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahPelatihLabel">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Tambah Pelatih Resmi PBVSI
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Pelatih <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Li Qiujiang">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="pelatih@pbvsi.or.id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control" placeholder="0812...">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Lisensi <span class="text-danger">*</span></label>
                            <select name="tingkat_pelatih" class="form-select" required>
                                <option value="Nasional A">Nasional A</option>
                                <option value="Nasional B" selected>Nasional B</option>
                                <option value="Nasional C">Nasional C</option>
                                <option value="FIVB Level 1">FIVB Level 1</option>
                                <option value="FIVB Level 2">FIVB Level 2</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Lisensi (Opsional)</label>
                            <input type="text" name="nomor_lisensi_pelatih" class="form-control" placeholder="Auto-generate jika kosong">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Asal Pengprov</label>
                        <select name="pengprov_id" class="form-select">
                            <option value="">-- Pengurus Pusat PBVSI --</option>
                            @foreach($pengprovList as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pengprov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="status_aktif" id="tambah_status_aktif" value="1" checked>
                        <label class="form-check-label fw-semibold" for="tambah_status_aktif">Status Pelatih Aktif</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pelatih</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT PELATIH -->
<div class="modal fade" id="modalEditPelatih" tabindex="-1" aria-labelledby="modalEditPelatihLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditPelatih" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditPelatihLabel">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data Pelatih
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Pelatih <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tingkat Lisensi <span class="text-danger">*</span></label>
                            <input type="text" name="tingkat_pelatih" id="edit_tingkat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Lisensi <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_lisensi_pelatih" id="edit_lisensi" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Asal Pengprov</label>
                        <select name="pengprov_id" id="edit_pengprov_id" class="form-select">
                            <option value="">-- Pengurus Pusat PBVSI --</option>
                            @foreach($pengprovList as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pengprov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="status_aktif" id="edit_status_aktif" value="1">
                        <label class="form-check-label fw-semibold" for="edit_status_aktif">Status Pelatih Aktif</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL HAPUS PELATIH -->
<div class="modal fade" id="modalHapusPelatih" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHapusPelatih" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Konfirmasi Hapus Pelatih
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fa-solid fa-trash-can fa-3x text-danger mb-3 d-block"></i>
                    <p class="mb-1">Apakah Anda yakin ingin menghapus pelatih berikut?</p>
                    <h5 class="fw-bold text-dark" id="hapus_nama_pelatih">-</h5>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit Modal Handler
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            document.getElementById('edit_nama').value = this.getAttribute('data-nama') || '';
            document.getElementById('edit_email').value = this.getAttribute('data-email') || '';
            document.getElementById('edit_phone').value = this.getAttribute('data-phone') || '';
            document.getElementById('edit_lisensi').value = this.getAttribute('data-lisensi') || '';
            document.getElementById('edit_tingkat').value = this.getAttribute('data-tingkat') || '';
            document.getElementById('edit_pengprov_id').value = this.getAttribute('data-pengprov-id') || '';
            document.getElementById('edit_status_aktif').checked = this.getAttribute('data-status') === '1';

            document.getElementById('formEditPelatih').action = "{{ url('personel/pelatih') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalEditPelatih'));
            modal.show();
        });
    });

    // Delete Modal Handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            document.getElementById('hapus_nama_pelatih').innerText = nama;
            document.getElementById('formHapusPelatih').action = "{{ url('personel/pelatih') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalHapusPelatih'));
            modal.show();
        });
    });
});
</script>
@endpush
