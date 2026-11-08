@extends('layouts.app')

@section('title', 'Verifikasi KYC & Dokumen KTP - PBVSI')

@push('styles')
<style>
    /* KYC Review Modal Fullscreen & Responsive Sizing */
    #modalReviewKyc .modal-dialog {
        max-width: 1400px !important;
        width: 96% !important;
        margin: 1rem auto;
    }
    .kyc-img-box {
        position: relative;
        background: #0f172a;
        border-radius: 8px;
        overflow: hidden;
        height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #334155;
    }
    .kyc-img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.2s ease;
        cursor: zoom-in;
    }
    @media (max-width: 991.98px) {
        .kyc-img-box {
            height: 280px;
        }
    }
    .kyc-img-box:hover img {
        transform: scale(1.02);
    }
    .kyc-img-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        z-index: 2;
    }
    .kyc-zoom-btn {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        cursor: pointer;
        transition: all 0.2s;
        z-index: 2;
        text-decoration: none;
    }
    .kyc-zoom-btn:hover {
        background: #2563eb;
        color: #fff;
        transform: translateY(-1px);
    }
    .user-profile-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
    }
    .quick-reason-chip {
        cursor: pointer;
        transition: all 0.15s;
        font-size: 0.75rem;
    }
    .quick-reason-chip:hover {
        background-color: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #dc2626 !important;
    }
</style>
@endpush

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('master.users') }}">Akses & Pengguna</a></li>
            <li class="breadcrumb-item active"><span class="badge bg-danger px-2 py-1">Verifikasi KYC & KTP</span></li>
        </ol>
    </nav>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Data Realtime PBVSI
        </span>
    </div>
</div>

{{-- Top Statistics Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('master.kyc.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $status === '' ? 'border-primary border-2 shadow' : '' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Pengajuan</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($counts['total']) }}</div>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="fa-solid fa-id-card fa-lg"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('master.kyc.index', ['status' => 'PENDING']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $status === 'PENDING' ? 'border-warning border-2 shadow' : '' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Menunggu Verifikasi</div>
                        <div class="fs-4 fw-bold text-warning mt-1">{{ number_format($counts['pending']) }}</div>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                        <i class="fa-solid fa-hourglass-half fa-lg"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('master.kyc.index', ['status' => 'VERIFIED']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $status === 'VERIFIED' ? 'border-success border-2 shadow' : '' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Terverifikasi (Valid)</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($counts['verified']) }}</div>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('master.kyc.index', ['status' => 'REJECTED']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $status === 'REJECTED' ? 'border-danger border-2 shadow' : '' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Ditolak (Revisi)</div>
                        <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($counts['rejected']) }}</div>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3">
                        <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Main Card --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-id-card-clip text-primary me-2"></i> Daftar Pengajuan KYC KTP Pengguna
            </h5>
            @if($status)
                <span class="badge bg-secondary">{{ $status }}</span>
            @endif
        </div>

        {{-- Filter & Search Form --}}
        <form method="GET" action="{{ route('master.kyc.index') }}" class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="input-group input-group-sm" style="min-width: 280px;">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama, NIK, Username, No WA..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('master.kyc.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Pengguna / Akun</th>
                    <th>NIK & Persetujuan Ketentuan</th>
                    <th>Berkas KTP & Selfie</th>
                    <th>Status Verifikasi</th>
                    <th>Tanggal Pengajuan</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profiles as $index => $item)
                @php
                    $u = $item->user;
                @endphp
                <tr>
                    <td class="text-center text-muted small">
                        {{ $profiles->firstItem() + $index }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($u->avatar_path)
                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="rounded-circle object-fit-cover shadow-sm border" width="40" height="40" onerror="this.src='{{ asset('assets/images/default-avatar.svg') }}'">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary border" style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                <div class="small text-muted">
                                    <span class="badge bg-light text-dark border me-1">{{ $u->username }}</span>
                                    @if($u->phone)
                                        <i class="fa-brands fa-whatsapp text-success me-1"></i>{{ $u->phone }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold font-monospace text-dark">
                            <i class="fa-regular fa-id-card text-muted me-1"></i>{{ $item->nik ?? '-' }}
                        </div>
                        <div class="small text-muted mt-1">
                            @if($item->terms_agreed_at)
                                <span class="text-success" title="IP: {{ $item->terms_agreed_ip }}">
                                    <i class="fa-solid fa-shield-check me-1"></i>Setuju Ketentuan: {{ $item->terms_agreed_at->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Belum setuju persetujuan</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Thumbnail KTP --}}
                            @if($item->ktp_path)
                                <a href="javascript:void(0)" onclick="openReviewModal({{ $item->id }})" class="position-relative text-decoration-none">
                                    <img src="{{ $item->ktp_url }}" alt="KTP {{ $u->name }}" class="rounded border shadow-sm object-fit-cover" width="60" height="42">
                                    <span class="position-absolute bottom-0 start-0 bg-dark bg-opacity-75 text-white px-1 py-0 rounded-bottom" style="font-size: 0.6rem;">KTP</span>
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">KTP Kosong</span>
                            @endif

                            {{-- Thumbnail Selfie KTP --}}
                            @if($item->selfie_ktp_path)
                                <a href="javascript:void(0)" onclick="openReviewModal({{ $item->id }})" class="position-relative text-decoration-none">
                                    <img src="{{ $item->selfie_ktp_url }}" alt="Selfie KTP {{ $u->name }}" class="rounded border shadow-sm object-fit-cover" width="60" height="42">
                                    <span class="position-absolute bottom-0 start-0 bg-dark bg-opacity-75 text-white px-1 py-0 rounded-bottom" style="font-size: 0.6rem;">Selfie</span>
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Selfie Kosong</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($item->verification_status === 'VERIFIED')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                <i class="fa-solid fa-check-double me-1"></i> TERVERIFIKASI
                            </span>
                            @if($item->verifiedByUser)
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                    Oleh: <strong>{{ $item->verifiedByUser->name }}</strong>
                                </div>
                            @endif
                        @elseif($item->verification_status === 'REJECTED')
                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 rounded-pill">
                                <i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK
                            </span>
                            @if($item->rejection_reason)
                                <div class="small text-danger mt-1 text-truncate" style="max-width: 180px;" title="{{ $item->rejection_reason }}">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $item->rejection_reason }}
                                </div>
                            @endif
                        @elseif($item->verification_status === 'PENDING')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1 rounded-pill">
                                <i class="fa-solid fa-hourglass-half me-1"></i> MENUNGGU REVIEW
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                <i class="fa-regular fa-clock me-1"></i> BELUM UPLOAD
                            </span>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="openReviewModal({{ $item->id }})">
                            <i class="fa-solid fa-magnifying-glass-plus me-1"></i> Review
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fa-solid fa-id-card fa-3x text-secondary opacity-50 mb-3"></i>
                            <h6 class="fw-bold">Tidak ada pengajuan verifikasi KYC</h6>
                            <p class="small text-muted mb-0">Belum ada user yang mengajukan atau tidak ditemukan hasil pencarian yang cocok.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($profiles->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $profiles->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>

{{-- MODAL REVIEW DOKUMEN KYC LENGKAP --}}
<div class="modal fade" id="modalReviewKyc" tabindex="-1" aria-labelledby="modalReviewKycLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-20 text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-id-card-clip fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalReviewKycLabel">Verifikasi Berkas Identitas (KYC)</h5>
                        <small class="text-white-50" id="reviewSubtitle">Validasi keaslian KTP & Foto Wajah Pengguna</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="headerStatusBadge" class="badge bg-secondary px-3 py-2">MEMUAT...</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light bg-opacity-50">
                {{-- Loader --}}
                <div id="modalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Memuat data...</span>
                    </div>
                    <p class="text-muted fw-semibold mt-3 mb-0">Mengambil data berkas dan kredensial pengguna...</p>
                </div>

                {{-- Content --}}
                <div id="modalContent" style="display: none;">
                    {{-- Alert jika sudah terverifikasi atau ditolak --}}
                    <div id="statusAlertContainer" class="mb-3"></div>

                    {{-- User Profile Strip Header --}}
                    <div class="user-profile-strip mb-4 shadow-sm bg-white">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <img id="userAvatarImg" src="{{ asset('assets/images/no-user.svg') }}" 
                                     alt="Avatar" class="rounded-circle border border-2 border-primary-subtle object-fit-cover shadow-sm"
                                     style="width: 60px; height: 60px;">
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h5 class="fw-bold text-dark mb-0" id="userName">-</h5>
                                    <span class="badge bg-light text-secondary border font-monospace" id="userUsername">@username</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="userRoles">Role</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-3 mt-1 small text-muted">
                                    <div><i class="fa-solid fa-address-card text-primary me-1"></i> NIK: <strong id="userNik" class="text-dark font-monospace fs-6">-</strong></div>
                                    <div class="border-start ps-3"><i class="fa-solid fa-envelope text-secondary me-1"></i> <span id="userEmail">-</span></div>
                                    <div class="border-start ps-3"><i class="fa-brands fa-whatsapp text-success me-1"></i> <span id="userPhone">-</span></div>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <span class="badge bg-light text-muted border px-2 py-1 small d-block mb-1">Pengajuan:</span>
                                <span class="fw-semibold text-dark small" id="userSubmitted">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- SISI KIRI: Foto KTP & Selfie KTP (col-lg-8) --}}
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-images text-primary"></i>
                                        <span class="fw-bold text-dark">Perbandingan Berkas Identitas KTP & Swafoto</span>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary small">
                                        <i class="fa-solid fa-magnifying-glass me-1"></i> Klik foto / zoom untuk melihat resolusi penuh
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        {{-- 1. KTP Fisik --}}
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold small text-dark"><i class="fa-regular fa-id-card text-primary me-1"></i> 1. Dokumen Fisik KTP</span>
                                                <a id="ktpNewTabLink" href="#" target="_blank" class="small text-decoration-none text-muted">
                                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Tab Baru
                                                </a>
                                            </div>
                                            <div class="kyc-img-box shadow-sm position-relative" id="ktpBox">
                                                <span class="kyc-img-badge"><i class="fa-solid fa-id-card me-1"></i> e-KTP ASLI</span>
                                                <img id="ktpImage" src="" alt="Foto KTP" onclick="previewImageInLightbox(this.src, 'Dokumen Fisik e-KTP')">
                                                <div id="ktpEmpty" class="text-white-50 text-center p-4" style="display: none;">
                                                    <i class="fa-regular fa-image fa-3x mb-2 opacity-50"></i>
                                                    <div>Belum ada unggahan foto KTP</div>
                                                </div>
                                                <button type="button" class="kyc-zoom-btn" id="ktpZoomBtn" onclick="previewImageInLightbox(document.getElementById('ktpImage').src, 'Dokumen Fisik e-KTP')">
                                                    <i class="fa-solid fa-magnifying-glass-plus me-1"></i> Perbesar
                                                </button>
                                            </div>
                                            <div class="bg-light rounded p-2 mt-2 border small text-muted">
                                                <i class="fa-solid fa-circle-info text-info me-1"></i> Periksa NIK 16-digit, nama, tempat & tgl lahir, dan masa berlaku KTP.
                                            </div>
                                        </div>

                                        {{-- 2. Swafoto / Selfie --}}
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold small text-dark"><i class="fa-solid fa-camera text-primary me-1"></i> 2. Swafoto Memegang KTP</span>
                                                <a id="selfieNewTabLink" href="#" target="_blank" class="small text-decoration-none text-muted">
                                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Tab Baru
                                                </a>
                                            </div>
                                            <div class="kyc-img-box shadow-sm position-relative" id="selfieBox">
                                                <span class="kyc-img-badge"><i class="fa-solid fa-user-check me-1"></i> SWAFOTO KTP</span>
                                                <img id="selfieImage" src="" alt="Selfie KTP" onclick="previewImageInLightbox(this.src, 'Swafoto Memegang KTP')">
                                                <div id="selfieEmpty" class="text-white-50 text-center p-4" style="display: none;">
                                                    <i class="fa-regular fa-image fa-3x mb-2 opacity-50"></i>
                                                    <div>Belum ada unggahan swafoto KTP</div>
                                                </div>
                                                <button type="button" class="kyc-zoom-btn" id="selfieZoomBtn" onclick="previewImageInLightbox(document.getElementById('selfieImage').src, 'Swafoto Memegang KTP')">
                                                    <i class="fa-solid fa-magnifying-glass-plus me-1"></i> Perbesar
                                                </button>
                                            </div>
                                            <div class="bg-light rounded p-2 mt-2 border small text-muted">
                                                <i class="fa-solid fa-circle-info text-info me-1"></i> Pastikan wajah pemegang identik dengan foto di KTP dan memegang KTP fisik asli.
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Checklist Standar PBVSI --}}
                                    <div class="card border border-primary-subtle bg-primary bg-opacity-10 rounded-3 p-3 mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold small text-dark">
                                                <i class="fa-solid fa-list-check text-primary me-1"></i> Standar Pengecekan Keaslian Berkas PBVSI:
                                            </span>
                                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.72rem;" onclick="checkAllCheckboxes()">
                                                <i class="fa-solid fa-check-double me-1"></i> Centang Semua
                                            </button>
                                        </div>
                                        <div class="row g-2 small text-dark">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input kyc-check" type="checkbox" id="chkNik">
                                                    <label class="form-check-label user-select-none" for="chkNik">Nomor NIK 16 digit terbaca jelas & sesuai</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input kyc-check" type="checkbox" id="chkName">
                                                    <label class="form-check-label user-select-none" for="chkName">Nama Lengkap sesuai kartu identitas KTP</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input kyc-check" type="checkbox" id="chkFace">
                                                    <label class="form-check-label user-select-none" for="chkFace">Wajah di selfie identik dengan foto di KTP</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input kyc-check" type="checkbox" id="chkOriginal">
                                                    <label class="form-check-label user-select-none" for="chkOriginal">Dokumen fisik asli (bukan editan / fotokopi)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SISI KANAN: Data Demografi & Keputusan Tindakan (col-lg-4) --}}
                        <div class="col-lg-4">
                            <div class="d-flex flex-column gap-3">
                                {{-- Card Informasi Demografi --}}
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-header bg-white py-2 px-3 border-bottom">
                                        <span class="fw-bold small text-dark"><i class="fa-solid fa-address-book text-primary me-1"></i> Informasi Profil & Demografi</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="d-flex flex-column gap-2 small">
                                            <div class="d-flex justify-content-between pb-1 border-bottom">
                                                <span class="text-muted">Tempat, Tgl Lahir</span>
                                                <span class="fw-semibold text-dark text-end" id="userTtl">-</span>
                                            </div>
                                            <div class="d-flex justify-content-between pb-1 border-bottom">
                                                <span class="text-muted">Jenis Kelamin</span>
                                                <span class="fw-semibold text-dark text-end" id="userGender">-</span>
                                            </div>
                                            <div class="d-flex justify-content-between pb-1 border-bottom">
                                                <span class="text-muted">Wilayah / Domisili</span>
                                                <span class="fw-semibold text-dark text-end" id="userWilayah">-</span>
                                            </div>
                                            <div class="pb-1 border-bottom">
                                                <span class="text-muted d-block mb-1">Alamat Lengkap</span>
                                                <div class="fw-semibold text-dark bg-light p-2 rounded" id="userAlamat" style="max-height: 80px; overflow-y: auto;">-</div>
                                            </div>
                                            <div class="d-flex justify-content-between pt-1">
                                                <span class="text-muted">Syarat & Ketentuan</span>
                                                <span class="fw-semibold text-end" id="userTerms">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Panel Keputusan --}}
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-header bg-white py-2 px-3 border-bottom">
                                        <span class="fw-bold small text-dark"><i class="fa-solid fa-gavel text-warning me-1"></i> Keputusan Tindakan Verifikator</span>
                                    </div>
                                    <div class="card-body p-3">
                                        {{-- Tombol Setujui --}}
                                        <form id="approveForm" method="POST" action="" class="mb-2" onsubmit="return confirm('Apakah Anda yakin dokumen dan data pengguna ini VALID dan ingin MENYETUJUI verifikasi ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                                <i class="fa-solid fa-circle-check fa-lg"></i>
                                                <span>Setujui & Verifikasi Berkas</span>
                                            </button>
                                        </form>

                                        {{-- Tombol Buka Form Tolak --}}
                                        <button type="button" class="btn btn-outline-danger w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="toggleRejectBox()">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            <span>Tolak Berkas (Minta Revisi)</span>
                                        </button>

                                        {{-- Form Penolakan (Hidden by default) --}}
                                        <div id="rejectBox" class="mt-3 p-3 border border-danger-subtle bg-danger bg-opacity-10 rounded-3" style="display: none;">
                                            <form id="rejectForm" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan verifikasi ini? Pengguna akan menerima alasan penolakan ini.');">
                                                @csrf
                                                <label class="form-label fw-bold text-danger small mb-1">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Alasan Penolakan Berkas:
                                                </label>
                                                
                                                {{-- Quick Reason Chips --}}
                                                <div class="mb-2">
                                                    <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">Klik alasan cepat:</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge bg-white text-danger border border-danger-subtle quick-reason-chip" onclick="setRejectionReason('Foto KTP buram, tidak fokus, atau NIK tidak terbaca dengan jelas.')">Foto KTP Buram</span>
                                                        <span class="badge bg-white text-danger border border-danger-subtle quick-reason-chip" onclick="setRejectionReason('Foto selfie tidak memegang dokumen fisik e-KTP.')">Selfie Tanpa KTP</span>
                                                        <span class="badge bg-white text-danger border border-danger-subtle quick-reason-chip" onclick="setRejectionReason('Nama lengkap atau NIK pada akun tidak sesuai dengan dokumen fisik e-KTP yang diunggah.')">Nama/NIK Tidak Sesuai</span>
                                                        <span class="badge bg-white text-danger border border-danger-subtle quick-reason-chip" onclick="setRejectionReason('Dokumen KTP yang diunggah berupa fotokopi atau hasil tangkapan layar, bukan foto fisik asli.')">Bukan KTP Asli</span>
                                                    </div>
                                                </div>

                                                <textarea name="rejection_reason" id="rejectionReasonInput" class="form-control form-control-sm border-danger mb-2" rows="3" placeholder="Tuliskan alasan penolakan secara jelas agar pengguna dapat memperbaiki unggahannya..." required></textarea>
                                                
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold flex-grow-1 shadow-sm">
                                                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim Penolakan
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleRejectBox()">
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between">
                <span class="text-muted small">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Modul Verifikasi Keabsahan Identitas PBVSI Terintegrasi
                </span>
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL LIGHTBOX RESOLUSI PENUH --}}
<div class="modal fade" id="modalImageLightbox" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 92vw; margin: 1rem auto;">
        <div class="modal-content bg-dark text-white border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-black py-2 px-3 border-secondary">
                <h6 class="modal-title fw-bold text-white" id="lightboxTitle">Resolusi Penuh</h6>
                <div class="d-flex align-items-center gap-2">
                    <a id="lightboxExternalLink" href="#" target="_blank" class="btn btn-sm btn-outline-light py-1 px-2" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Asli
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-2 d-flex align-items-center justify-content-center bg-black" style="min-height: 70vh;">
                <img id="lightboxImage" src="" alt="Full Resolution" class="img-fluid rounded" style="max-height: 82vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentProfileId = null;

    function checkAllCheckboxes() {
        const checks = document.querySelectorAll('.kyc-check');
        const allChecked = Array.from(checks).every(c => c.checked);
        checks.forEach(c => c.checked = !allChecked);
    }

    function setRejectionReason(text) {
        const input = document.getElementById('rejectionReasonInput');
        input.value = text;
        input.focus();
    }

    function previewImageInLightbox(src, title) {
        if (!src || src.includes('no-user') || src === window.location.href + '#') return;
        document.getElementById('lightboxImage').src = src;
        document.getElementById('lightboxTitle').textContent = title || 'Pratinjau Resolusi Penuh';
        document.getElementById('lightboxExternalLink').href = src;
        const lightboxModal = new bootstrap.Modal(document.getElementById('modalImageLightbox'));
        lightboxModal.show();
    }

    function openReviewModal(profileId) {
        currentProfileId = profileId;
        const modalEl = document.getElementById('modalReviewKyc');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Reset state
        document.getElementById('modalLoader').style.display = 'block';
        document.getElementById('modalContent').style.display = 'none';
        document.getElementById('rejectBox').style.display = 'none';
        document.getElementById('rejectionReasonInput').value = '';

        // Reset checkboxes
        document.querySelectorAll('.kyc-check').forEach(c => c.checked = false);

        // Set form actions
        document.getElementById('approveForm').action = `/master/kyc/${profileId}/approve`;
        document.getElementById('rejectForm').action = `/master/kyc/${profileId}/reject`;

        // Fetch detail
        fetch(`/master/kyc/${profileId}`)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data verifikasi');
                return res.json();
            })
            .then(data => {
                // Header status badge
                const headerBadge = document.getElementById('headerStatusBadge');
                if (data.verification_status === 'VERIFIED') {
                    headerBadge.className = 'badge bg-success px-3 py-2';
                    headerBadge.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> TERVERIFIKASI';
                } else if (data.verification_status === 'REJECTED') {
                    headerBadge.className = 'badge bg-danger px-3 py-2';
                    headerBadge.innerHTML = '<i class="fa-solid fa-times-circle me-1"></i> DITOLAK';
                } else if (data.verification_status === 'PENDING') {
                    headerBadge.className = 'badge bg-warning text-dark px-3 py-2';
                    headerBadge.innerHTML = '<i class="fa-solid fa-hourglass-half me-1"></i> MENUNGGU REVIEW';
                } else {
                    headerBadge.className = 'badge bg-secondary px-3 py-2';
                    headerBadge.innerHTML = '<i class="fa-regular fa-clock me-1"></i> BELUM UPLOAD';
                }

                // User Info
                document.getElementById('userName').textContent = data.name || '-';
                document.getElementById('userUsername').textContent = '@' + (data.username || '-');
                document.getElementById('userNik').textContent = data.nik || '-';
                document.getElementById('userRoles').textContent = data.roles || 'User';
                document.getElementById('userEmail').textContent = data.email || '-';
                document.getElementById('userPhone').textContent = data.phone || '-';
                document.getElementById('userTtl').textContent = (data.tempat_lahir ? data.tempat_lahir + ', ' : '') + (data.tanggal_lahir || '-');
                document.getElementById('userGender').textContent = data.jenis_kelamin || '-';
                document.getElementById('userWilayah').textContent = data.wilayah || '-';
                document.getElementById('userAlamat').textContent = data.alamat || 'Alamat belum diisi lengkap.';
                document.getElementById('userTerms').innerHTML = data.terms_agreed_at ? `<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check me-1"></i> ${data.terms_agreed_at}</span>` : '<span class="badge bg-secondary-subtle text-secondary">Belum disetujui</span>';
                document.getElementById('userSubmitted').textContent = data.submitted_at || '-';

                // User Avatar
                const avatarImg = document.getElementById('userAvatarImg');
                if (data.avatar_url) {
                    avatarImg.src = data.avatar_url;
                } else {
                    avatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name || 'User')}&background=0D8ABC&color=fff&bold=true`;
                }

                // Foto KTP
                const ktpImage = document.getElementById('ktpImage');
                const ktpEmpty = document.getElementById('ktpEmpty');
                const ktpZoomBtn = document.getElementById('ktpZoomBtn');
                const ktpNewTab = document.getElementById('ktpNewTabLink');
                if (data.ktp_url) {
                    ktpImage.src = data.ktp_url;
                    ktpImage.style.display = 'block';
                    ktpEmpty.style.display = 'none';
                    ktpZoomBtn.style.display = 'inline-block';
                    ktpNewTab.href = data.ktp_url;
                    ktpNewTab.style.display = 'inline-block';
                } else {
                    ktpImage.src = '';
                    ktpImage.style.display = 'none';
                    ktpEmpty.style.display = 'block';
                    ktpZoomBtn.style.display = 'none';
                    ktpNewTab.href = '#';
                    ktpNewTab.style.display = 'none';
                }

                // Foto Selfie
                const selfieImage = document.getElementById('selfieImage');
                const selfieEmpty = document.getElementById('selfieEmpty');
                const selfieZoomBtn = document.getElementById('selfieZoomBtn');
                const selfieNewTab = document.getElementById('selfieNewTabLink');
                if (data.selfie_ktp_url) {
                    selfieImage.src = data.selfie_ktp_url;
                    selfieImage.style.display = 'block';
                    selfieEmpty.style.display = 'none';
                    selfieZoomBtn.style.display = 'inline-block';
                    selfieNewTab.href = data.selfie_ktp_url;
                    selfieNewTab.style.display = 'inline-block';
                } else {
                    selfieImage.src = '';
                    selfieImage.style.display = 'none';
                    selfieEmpty.style.display = 'block';
                    selfieZoomBtn.style.display = 'none';
                    selfieNewTab.href = '#';
                    selfieNewTab.style.display = 'none';
                }

                // Status Alert
                const alertContainer = document.getElementById('statusAlertContainer');
                if (data.verification_status === 'VERIFIED') {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success d-flex align-items-center mb-0 py-2 px-3 shadow-sm">
                            <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
                            <div>
                                <strong>Akun ini telah TERVERIFIKASI pada ${data.verified_at || '-'}</strong>
                                ${data.verified_by_name ? `oleh <strong>${data.verified_by_name}</strong>.` : '.'}
                            </div>
                        </div>
                    `;
                } else if (data.verification_status === 'REJECTED') {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center mb-0 py-2 px-3 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-danger"></i>
                            <div>
                                <strong>Akun ini DITOLAK pada ${data.verified_at || '-'}.</strong>
                                <div class="mt-1 small">Catatan penolakan sebelumnya: <em>"${data.rejection_reason || 'Tidak ada keterangan'}"</em></div>
                            </div>
                        </div>
                    `;
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-warning d-flex align-items-center mb-0 py-2 px-3 shadow-sm">
                            <i class="fa-solid fa-hourglass-half fa-lg me-2 text-warning"></i>
                            <div>Pengajuan verifikasi baru. Harap periksa kesesuaian berkas fisik e-KTP dan foto swafoto pemegang akun sebelum memutuskan.</div>
                        </div>
                    `;
                }

                document.getElementById('modalLoader').style.display = 'none';
                document.getElementById('modalContent').style.display = 'block';
            })
            .catch(err => {
                alert('Terjadi kesalahan saat memuat data: ' + err.message);
                modal.hide();
            });
    }

    function toggleRejectBox() {
        const box = document.getElementById('rejectBox');
        if (box.style.display === 'none' || box.style.display === '') {
            box.style.display = 'block';
            document.getElementById('rejectionReasonInput').focus();
        } else {
            box.style.display = 'none';
        }
    }
</script>
@endpush
