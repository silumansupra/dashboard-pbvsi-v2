@extends('layouts.app')

@section('title', 'Audit Log & Aktivitas - Platform PBVSI')

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#!">Keamanan & Kepatuhan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Audit Log Sistem</li>
        </ol>
    </nav>
</div>

<!-- STATS CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3">
                    <i class="fa-solid fa-database fa-xl"></i>
                </div>
                <div>
                    <h6 class="text-muted small fw-semibold mb-1">Total Catatan Log</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-3 bg-success-subtle text-success p-3 me-3">
                    <i class="fa-solid fa-calendar-day fa-xl"></i>
                </div>
                <div>
                    <h6 class="text-muted small fw-semibold mb-1">Aktivitas Hari Ini</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['today'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-3 bg-warning-subtle text-warning p-3 me-3">
                    <i class="fa-solid fa-shield-halved fa-xl"></i>
                </div>
                <div>
                    <h6 class="text-muted small fw-semibold mb-1">Log Autentikasi</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['auth'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-3 bg-info-subtle text-info p-3 me-3">
                    <i class="fa-solid fa-users-gear fa-xl"></i>
                </div>
                <div>
                    <h6 class="text-muted small fw-semibold mb-1">Pengguna Terlibat</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['distinct_users'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 fw-bold text-dark">
            <i class="fa-solid fa-filter text-primary me-2"></i>Filter Audit Log
        </h5>
        @if(array_filter($filters))
            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-rotate-left me-1"></i> Reset Filter
            </a>
        @endif
    </div>
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Kata Kunci Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Cari deskripsi, username, IP..." 
                           value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Modul Sistem</label>
                <select name="module" class="form-select">
                    <option value="all">Semua Modul</option>
                    @foreach($availableModules as $modKey => $modLabel)
                        <option value="{{ $modKey }}" {{ ($filters['module'] ?? '') === $modKey ? 'selected' : '' }}>
                            {{ $modLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AUDIT LOG TABLE -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i>Daftar Jejak Audit (Audit Trail)
            </h5>
            <small class="text-muted">Catatan permanen setiap operasi, otentikasi, dan perubahan data di Platform PBVSI</small>
        </div>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 border">
            Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 170px;">Waktu & Tanggal</th>
                    <th style="width: 200px;">Pengguna (Subjek)</th>
                    <th style="width: 160px;">Modul / Aksi</th>
                    <th>Deskripsi Aktivitas</th>
                    <th style="width: 140px;">IP Address</th>
                    <th style="width: 80px;" class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div class="fw-semibold text-dark small">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        @if($log->user_name)
                            <div class="fw-bold text-dark small text-truncate" style="max-width: 190px;">
                                {{ $log->user_name }}
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">
                                    {{ $log->user_username ?? '-' }}
                                </span>
                                @if($log->user_role)
                                    <span class="badge bg-info-subtle text-info text-capitalize" style="font-size: 0.68rem;">
                                        {{ $log->user_role }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="badge bg-secondary-subtle text-muted">Sistem / Tamu</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($log->module) {
                                'auth' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'team' => 'bg-success-subtle text-success border border-success-subtle',
                                'personel' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                'master' => 'bg-info-subtle text-info border border-info-subtle',
                                'profile' => 'bg-purple-subtle text-purple border',
                                default => 'bg-secondary-subtle text-secondary border'
                            };
                        @endphp
                        <div>
                            <span class="badge {{ $badgeClass }} text-uppercase mb-1" style="font-size: 0.72rem;">
                                {{ $log->module }}
                            </span>
                        </div>
                        <div class="text-muted small font-monospace" style="font-size: 0.75rem;">
                            {{ $log->action }}
                        </div>
                    </td>
                    <td>
                        <div class="text-dark small leading-relaxed">
                            {{ $log->description }}
                        </div>
                    </td>
                    <td>
                        <span class="font-monospace small text-muted">{{ $log->ip_address ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-detail" 
                                data-id="{{ $log->id }}" 
                                title="Lihat detail payload dan metadata">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted mb-2">
                            <i class="fa-solid fa-folder-open fa-3x text-secondary opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Belum ada catatan audit log yang cocok</h6>
                        <p class="small text-muted mb-0">Coba atur ulang filter pencarian tanggal atau modul sistem.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap">
        <small class="text-muted">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</small>
        <div>
            {{ $logs->links() }}
        </div>
    </div>
    @endif
</div>

<!-- MODAL DETAIL AUDIT LOG -->
<div class="modal fade" id="modalAuditDetail" tabindex="-1" aria-labelledby="modalAuditDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2">
                        <i class="fa-solid fa-file-shield fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalAuditDetailLabel">Detail Audit Log #<span id="detailLogId">-</span></h5>
                        <small class="text-muted" id="detailLogTime">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1 fw-semibold">Subjek Pengguna</small>
                            <h6 class="fw-bold text-dark mb-1" id="detailUserName">-</h6>
                            <div class="small text-muted font-monospace" id="detailUserUsername">-</div>
                            <span class="badge bg-secondary-subtle text-secondary mt-2" id="detailUserRole">-</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1 fw-semibold">Informasi Jaringan & Klien</small>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">IP Address:</span>
                                <span class="small font-monospace fw-bold text-dark" id="detailIpAddress">-</span>
                            </div>
                            <div class="small text-muted">User Agent:</div>
                            <div class="small font-monospace text-truncate text-muted mt-1" id="detailUserAgent" style="max-width: 100%;">-</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Deskripsi Lengkap Aktivitas</label>
                    <div class="p-3 bg-white rounded-3 border text-dark small" id="detailDescription">-</div>
                </div>

                <!-- OLD & NEW VALUES -->
                <div id="diffContainer" class="d-none">
                    <label class="form-label small fw-semibold text-muted">Perubahan Nilai (Diff)</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light py-2 small fw-semibold text-danger">
                                    <i class="fa-solid fa-minus me-1"></i> Nilai Sebelumnya (Old Values)
                                </div>
                                <div class="card-body p-2 bg-dark rounded-bottom">
                                    <pre class="text-light mb-0 small font-monospace" id="detailOldValues" style="max-height: 200px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light py-2 small fw-semibold text-success">
                                    <i class="fa-solid fa-plus me-1"></i> Nilai Baru (New Values)
                                </div>
                                <div class="card-body p-2 bg-dark rounded-bottom">
                                    <pre class="text-light mb-0 small font-monospace" id="detailNewValues" style="max-height: 200px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- METADATA -->
                <div id="metadataContainer" class="mt-3 d-none">
                    <label class="form-label small fw-semibold text-muted">Metadata Request Tambahan</label>
                    <div class="card border bg-dark">
                        <div class="card-body p-2">
                            <pre class="text-info mb-0 small font-monospace" id="detailMetadata" style="max-height: 180px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailButtons = document.querySelectorAll('.btn-detail');
    const modalEl = document.getElementById('modalAuditDetail');
    const modal = new bootstrap.Modal(modalEl);

    detailButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const logId = this.getAttribute('data-id');
            
            // Set loading state
            document.getElementById('detailLogId').innerText = logId;
            document.getElementById('detailLogTime').innerText = 'Memuat rincian...';
            document.getElementById('detailUserName').innerText = '...';
            document.getElementById('detailUserUsername').innerText = '...';
            document.getElementById('detailUserRole').innerText = '...';
            document.getElementById('detailIpAddress').innerText = '...';
            document.getElementById('detailUserAgent').innerText = '...';
            document.getElementById('detailDescription').innerText = 'Memuat data log...';
            document.getElementById('diffContainer').classList.add('d-none');
            document.getElementById('metadataContainer').classList.add('d-none');

            modal.show();

            fetch(`/audit-logs/${logId}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        const d = res.data;
                        document.getElementById('detailLogTime').innerText = `${d.created_at} (${d.created_at_human})`;
                        document.getElementById('detailUserName').innerText = d.user.name;
                        document.getElementById('detailUserUsername').innerText = d.user.username;
                        document.getElementById('detailUserRole').innerText = d.user.role;
                        document.getElementById('detailIpAddress').innerText = d.ip_address;
                        document.getElementById('detailUserAgent').innerText = d.user_agent;
                        document.getElementById('detailDescription').innerText = d.description;

                        if (d.old_values || d.new_values) {
                            document.getElementById('diffContainer').classList.remove('d-none');
                            document.getElementById('detailOldValues').innerText = d.old_values ? JSON.stringify(d.old_values, null, 2) : '-';
                            document.getElementById('detailNewValues').innerText = d.new_values ? JSON.stringify(d.new_values, null, 2) : '-';
                        }

                        if (d.metadata && Object.keys(d.metadata).length > 0) {
                            document.getElementById('metadataContainer').classList.remove('d-none');
                            document.getElementById('detailMetadata').innerText = JSON.stringify(d.metadata, null, 2);
                        }
                    }
                })
                .catch(err => {
                    document.getElementById('detailDescription').innerText = 'Gagal memuat rincian audit log: ' + err;
                });
        });
    });
});
</script>
@endsection
