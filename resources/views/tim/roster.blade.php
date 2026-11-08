@extends('layouts.app')

@section('title', 'Susunan Roster O-2 - ' . $activeTeam->nama_tim)

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Portal Tim</a></li>
            <li class="breadcrumb-item active" aria-current="page">Susunan Roster O-2</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Pelanggaran Regulasi:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- HEADER HERO -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center g-3">
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-white">{{ $activeTeam->nama_tim }}</h3>
                    <span class="badge {{ $activeTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }} px-3 py-1 fs-6">
                        <i class="fa-solid {{ $activeTeam->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                        {{ $activeTeam->kategori_gender == 'PA' ? 'PUTRA (PA)' : 'PUTRI (PI)' }}
                    </span>
                    <span class="badge bg-info px-2 py-1"><i class="fa-solid fa-clipboard-check me-1"></i> FORMULIR RESMI O-2 PBVSI</span>
                </div>
                <div class="text-white-50 small">
                    Penetapan Kapten Tim dan Pemain Bertahan (Libero) Sesuai Regulasi PBVSI & FIVB
                </div>
            </div>

            <div class="col-auto d-flex gap-2">
                <a href="{{ route('tim.roster.print_o2') }}" target="_blank" class="btn btn-warning px-3 py-2 fw-semibold shadow-sm text-dark">
                    <i class="fa-solid fa-print me-1"></i> Cetak Lembar O-2
                </a>
            </div>
        </div>
    </div>
</div>

<!-- REGULASI INFO BOX -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="small text-muted fw-bold mb-1">JUMLAH KUOTA SKUAD (O-2)</div>
                <div class="fs-4 fw-bold {{ $roster->count() >= 10 && $roster->count() <= 14 ? 'text-success' : 'text-danger' }}">
                    {{ $roster->count() }} <span class="fs-6 fw-normal text-muted">/ 10-14 Pemain</span>
                </div>
                <div class="small text-muted mt-1">Sesuai regulasi Kejurnas: Minimal 10 dan maksimal 14 pemain.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="small text-muted fw-bold mb-1">KAPTEN TIM (C)</div>
                <div class="fs-4 fw-bold text-dark">
                    @if($captain)
                        <span class="text-warning-emphasis">#{{ $captain->nomor_punggung }}</span> {{ $captain->pemain?->nama_lengkap ?? 'Kapten' }}
                    @else
                        <span class="text-danger fs-6 fw-bold">BELUM DITENTUKAN</span>
                    @endif
                </div>
                <div class="small text-muted mt-1">Wajib 1 Kapten. Kapten TIDAK BOLEH merangkap sebagai Libero.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="small text-muted fw-bold mb-1">PEMAIN LIBERO (L)</div>
                <div class="fs-4 fw-bold text-dark">
                    @if($liberos->count() > 0)
                        <span class="text-info">{{ $liberos->count() }} Libero</span>
                        <span class="fs-6 text-muted fw-normal">({{ $liberos->pluck('nomor_punggung')->map(fn($n) => "#$n")->join(', ') }})</span>
                    @else
                        <span class="text-muted fs-6">0 Libero (Opsional max 2)</span>
                    @endif
                </div>
                <div class="small text-muted mt-1">Maksimal 2 Libero. Wajib mengenakan jersey warna kontras.</div>
            </div>
        </div>
    </div>
</div>

<!-- FORM PENUGASAN O-2 -->
<form action="{{ route('tim.roster.update') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i>Penugasan Kapten & Libero Skuad O-2
            </h5>
            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penugasan O-2
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th>Nama Lengkap Pemain</th>
                            <th>Posisi Asli</th>
                            <th>TB / BB</th>
                            <th class="text-center" style="width: 140px;">
                                <i class="fa-solid fa-crown text-warning me-1"></i> Kapten (C)
                            </th>
                            <th class="text-center" style="width: 140px;">
                                <i class="fa-solid fa-shield text-info me-1"></i> Libero (L)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roster as $r)
                            @php $p = $r->pemain; @endphp
                            <tr class="{{ $r->is_captain ? 'table-warning' : ($r->is_libero ? 'table-info' : '') }}">
                                <td class="text-center">
                                    <span class="badge bg-dark fs-6 px-2 py-1">#{{ $r->nomor_punggung }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p ? $p->nama_lengkap : 'Pemain' }}</div>
                                    <div class="text-muted small">
                                        @if($p && $p->tanggal_lahir)
                                            {{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} tahun &bull; {{ $p->tempat_lahir }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $r->posisi_bermain ?: ($p ? $p->posisi_utama : 'PEMAIN') }}</span>
                                </td>
                                <td>
                                    <small>{{ $r->tinggi_cm ? $r->tinggi_cm . 'cm' : '-' }} / {{ $r->berat_kg ? $r->berat_kg . 'kg' : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input captain-radio" type="radio" name="captain_id" value="{{ $r->id }}" id="captain_{{ $r->id }}" {{ $r->is_captain ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input libero-checkbox" type="checkbox" name="libero_ids[]" value="{{ $r->id }}" id="libero_{{ $r->id }}" {{ $r->is_libero ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users fa-3x mb-3 text-secondary"></i>
                                    <div>Belum ada pemain di dalam tim ini. Tambahkan pemain terlebih dahulu pada menu Data Pemain.</div>
                                    <a href="{{ route('tim.pemain') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fa-solid fa-user-plus me-1"></i> Buka Data Pemain
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light p-3 text-end">
            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penugasan O-2
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const captainRadios = document.querySelectorAll('.captain-radio');
    const liberoCheckboxes = document.querySelectorAll('.libero-checkbox');

    // Mencegah kapten merangkap sebagai libero di sisi browser
    captainRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                const targetLibero = document.getElementById('libero_' + this.value);
                if (targetLibero && targetLibero.checked) {
                    targetLibero.checked = false;
                    alert('Kapten tim tidak boleh merangkap sebagai Libero! Centang Libero otomatis dilepas.');
                }
            }
        });
    });

    liberoCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                const targetCaptain = document.getElementById('captain_' + this.value);
                if (targetCaptain && targetCaptain.checked) {
                    targetCaptain.checked = false;
                    alert('Pemain Libero tidak boleh merangkap sebagai Kapten Tim! Penugasan Kapten otomatis dilepas.');
                }

                // Cek batas maksimal 2 libero
                const checkedLiberos = document.querySelectorAll('.libero-checkbox:checked');
                if (checkedLiberos.length > 2) {
                    this.checked = false;
                    alert('Maksimal 2 pemain yang boleh didaftarkan sebagai Libero sesuai regulasi PBVSI / FIVB.');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
