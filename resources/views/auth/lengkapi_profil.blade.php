<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Lengkapi Profil Akun - Platform Resmi PP PBVSI">
    <meta name="author" content="PP PBVSI">
    <title>Lengkapi Profil Akun &bull; Platform PBVSI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        html {
            height: auto;
            min-height: 100%;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 1rem 0.75rem 1.5rem 0.75rem;
            min-height: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }
        @media (min-width: 768px) {
            body {
                padding: 2.25rem 1.25rem 2rem 1.25rem;
            }
        }
        .onboarding-wrapper {
            max-width: 760px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .onboarding-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(226, 232, 240, 0.8);
            padding: 1.5rem 1.15rem;
            position: relative;
            box-sizing: border-box;
        }
        @media (min-width: 576px) {
            .onboarding-card {
                padding: 2rem 1.75rem;
                border-radius: 18px;
            }
        }
        @media (min-width: 768px) {
            .onboarding-card {
                padding: 2.5rem 2.25rem;
                border-radius: 20px;
            }
        }
        .brand-header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .brand-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            margin-bottom: 0.75rem;
        }
        @media (min-width: 768px) {
            .brand-logo {
                width: 72px;
                height: 72px;
            }
            .brand-header {
                margin-bottom: 2rem;
                padding-bottom: 1.5rem;
            }
        }
        .step-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid #bfdbfe;
            margin-bottom: 0.75rem;
        }
        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        @media (min-width: 768px) {
            .form-section-title {
                font-size: 1rem;
            }
        }
        .form-section-title i {
            color: #2563eb;
        }
        .form-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }
        .form-control, .form-select {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        /* Avatar Upload Container */
        .avatar-upload-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
            padding: 1.25rem 1rem;
            background: #f8fafc;
            border-radius: 14px;
            border: 1.5px dashed #cbd5e1;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 576px) {
            .avatar-upload-box {
                flex-direction: row;
                text-align: left;
                gap: 1.5rem;
                padding: 1.25rem;
            }
        }
        .avatar-preview-wrapper {
            position: relative;
            width: 84px;
            height: 84px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 3px solid #ffffff;
            background: #e2e8f0;
            flex-shrink: 0;
        }
        .avatar-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Document Upload Dropzones */
        .doc-upload-card {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            padding: 1.15rem 0.85rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            min-height: 155px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 768px) {
            .doc-upload-card {
                min-height: 185px;
                padding: 1.25rem;
            }
        }
        .doc-upload-card:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }
        .doc-preview-img {
            max-height: 130px;
            max-width: 100%;
            border-radius: 8px;
            object-fit: contain;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        /* Consent Box */
        .consent-card {
            background: #f8fafc;
            border: 2px solid #bfdbfe;
            border-radius: 14px;
            padding: 1.25rem 1.15rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .consent-card:hover {
            background: #eff6ff;
            border-color: #2563eb;
        }
        .custom-consent-checkbox {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            border: 2.5px solid #2563eb !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            margin-top: 2px !important;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .custom-consent-checkbox:checked {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
        }
        .btn-submit {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.85rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.45);
        }
    </style>
</head>
<body>
    <div class="onboarding-wrapper">
        <div class="onboarding-card">
            <!-- BRAND HEADER -->
            <div class="brand-header">
                <img src="{{ asset('assets/images/logo_pbvsi.png') }}" alt="Logo PBVSI" class="brand-logo"
                     onerror="this.onerror=null; this.src='{{ asset('assets/images/logo_pbvsi_64px.png') }}';">
                <br>
                <span class="step-pill">
                    <i class="fa-solid fa-shield-check"></i> Verifikasi Akun & Kepatuhan Regulasi
                </span>
                <h2 class="fw-bold text-dark mb-1">Lengkapi Profil Pengguna</h2>
                <p class="text-muted small mb-0">
                    Sesuai Statuta PP PBVSI & Ketentuan Regulasi Keabsahan, mohon lengkapi data identitas resmi Anda sebelum mengakses platform.
                </p>
            </div>

            <!-- NOTIFICATIONS -->
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show small d-flex align-items-center mb-4 py-2 px-3 rounded-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-warning"></i>
                <div class="flex-grow-1">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center mb-4 py-2 px-3 rounded-3" role="alert">
                <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small mb-4 py-2 px-3 rounded-3" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-circle-xmark fa-lg me-2 text-danger"></i>
                    <strong class="text-danger">Terdapat beberapa data yang perlu diperbaiki:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- MAIN FORM -->
            <form id="profileForm" action="{{ route('profil.lengkapi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- BAGIAN 1: FOTO PROFIL -->
                <div class="mb-4">
                    <h5 class="form-section-title">
                        <i class="fa-solid fa-user-circle"></i> 1. Foto Profil Resmi
                    </h5>
                    <div class="avatar-upload-box">
                        <div class="avatar-preview-wrapper">
                            <img id="avatarPreview"
                                 src="{{ $user->avatar_url ?: asset('images/no-user.svg') }}"
                                 alt="Avatar" class="avatar-preview-img">
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label d-block">Unggah Pas Foto Profil</label>
                            <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/jpeg,image/png,image/webp">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold px-3 mb-2" onclick="document.getElementById('avatarInput').click()">
                                <i class="fa-solid fa-camera me-1"></i> Pilih Foto Profil
                            </button>
                            <div class="form-text small text-muted">
                                Format: JPG, PNG, atau WebP (Maks. 10MB). Foto wajah tegak, jelas, dan rapi.
                                @if(!empty($user->avatar_path))
                                <span class="badge bg-success-subtle text-success ms-1"><i class="fa-solid fa-check"></i> Foto Tersimpan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 2: DATA PRIBADI SESUAI KTP -->
                <div class="mb-4">
                    <h5 class="form-section-title">
                        <i class="fa-solid fa-id-card"></i> 2. Identitas Diri (Sesuai KTP)
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label" for="nameInput">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                            <input type="text" id="nameInput" name="name"
                                   class="form-control text-uppercase @error('name') is-invalid @enderror"
                                   placeholder="Contoh: AHMAD MAULANA"
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label" for="nikInput">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                            <input type="text" id="nikInput" name="nik" maxlength="16"
                                   class="form-control @error('nik') is-invalid @enderror"
                                   placeholder="16 Digit Angka NIK"
                                   value="{{ old('nik', $profile->nik) }}" required>
                            <div class="form-text small text-muted">16 digit angka sesuai KTP resmi.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="emailInput">Alamat Email Aktif <span class="text-danger">*</span></label>
                            <input type="email" id="emailInput" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@domain.com"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phoneInput">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                            <input type="text" id="phoneInput" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Contoh: 081234567890"
                                   value="{{ old('phone', $user->phone) }}" required>
                            <div class="form-text small text-muted">Untuk konfirmasi verifikasi dan notifikasi resmi.</div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 3: BERKAS KTP & SELFIE KTP -->
                <div class="mb-4">
                    <h5 class="form-section-title">
                        <i class="fa-solid fa-file-shield"></i> 3. Berkas Verifikasi KTP & Wajah
                    </h5>
                    <div class="row g-3">
                        <!-- UPLOAD KTP -->
                        <div class="col-md-6">
                            <label class="form-label">Foto KTP Asli <span class="text-danger">*</span></label>
                            <input type="file" id="ktpInput" name="ktp" class="d-none" accept="image/jpeg,image/png,image/webp">
                            <div class="doc-upload-card" onclick="document.getElementById('ktpInput').click()">
                                @if(!empty($profile->ktp_path))
                                <img id="ktpPreview" src="{{ $profile->ktp_url }}" class="doc-preview-img mb-2" alt="Preview KTP">
                                <div id="ktpPlaceholder" class="d-none">
                                    <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                                    <div class="fw-semibold small">Ganti Foto KTP</div>
                                </div>
                                <span class="badge bg-success-subtle text-success small mt-1">
                                    <i class="fa-solid fa-check"></i> KTP Sudah Terunggah
                                </span>
                                @else
                                <img id="ktpPreview" src="" class="doc-preview-img mb-2 d-none" alt="Preview KTP">
                                <div id="ktpPlaceholder">
                                    <i class="fa-solid fa-address-card fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold small text-dark">Klik untuk Unggah KTP</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Pastikan seluruh fisik KTP & teks NIK terbaca jelas.</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- UPLOAD SELFIE DENGAN KTP -->
                        <div class="col-md-6">
                            <label class="form-label">Foto Selfie Bersama KTP <span class="text-danger">*</span></label>
                            <input type="file" id="selfieInput" name="selfie_ktp" class="d-none" accept="image/jpeg,image/png,image/webp">
                            <div class="doc-upload-card" onclick="document.getElementById('selfieInput').click()">
                                @if(!empty($profile->selfie_ktp_path))
                                <img id="selfiePreview" src="{{ $profile->selfie_ktp_url }}" class="doc-preview-img mb-2" alt="Preview Selfie">
                                <div id="selfiePlaceholder" class="d-none">
                                    <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                                    <div class="fw-semibold small">Ganti Foto Selfie</div>
                                </div>
                                <span class="badge bg-success-subtle text-success small mt-1">
                                    <i class="fa-solid fa-check"></i> Selfie Sudah Terunggah
                                </span>
                                @else
                                <img id="selfiePreview" src="" class="doc-preview-img mb-2 d-none" alt="Preview Selfie">
                                <div id="selfiePlaceholder">
                                    <i class="fa-solid fa-user-check fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold small text-dark">Klik untuk Unggah Selfie KTP</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Wajah Anda & fisik kartu KTP terlihat jelas dalam satu frame.</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 4: PERSETUJUAN PERATURAN & KEBIJAKAN PRIVASI -->
                <div class="mb-4">
                    <div class="consent-card" id="consentCardWrapper">
                        <div class="d-flex align-items-start gap-3">
                            <input class="form-check-input custom-consent-checkbox" type="checkbox" id="agreeTerms" name="agree_terms" value="1" required
                                   {{ old('agree_terms', !empty($profile->terms_agreed_at)) ? 'checked' : '' }}>
                            <label class="form-check-label small text-dark mb-0 user-select-none" for="agreeTerms" style="cursor: pointer; line-height: 1.55;">
                                <span class="d-block fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                    <i class="fa-solid fa-file-signature text-primary me-1"></i> Pernyataan & Persetujuan Resmi <span class="text-danger">*</span>
                                </span>
                                Saya menyatakan dengan sesungguhnya bahwa seluruh data dan berkas yang saya berikan adalah benar sesuai kartu identitas resmi (KTP). Saya memberikan persetujuan pemrosesan data identitas untuk verifikasi keabsahan PBVSI, serta menyatakan tunduk dan patuh pada
                                <a href="javascript:void(0)" class="fw-bold text-decoration-underline text-primary" data-bs-toggle="modal" data-bs-target="#modalPeraturan">
                                    Peraturan & Ketentuan Platform PBVSI
                                </a>,
                                <a href="{{ route('legal.terms') }}" target="_blank" class="fw-semibold text-decoration-underline text-primary">Syarat & Ketentuan</a>, dan
                                <a href="{{ route('legal.privacy') }}" target="_blank" class="fw-semibold text-decoration-underline text-primary">Kebijakan Privasi</a>.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- LOADER STATUS (TAMPIL SAAT PROSES UPLOAD & KOMPRESI KE CDN) -->
                <div id="loadingStatus" class="p-3 mb-3 rounded-3 d-none align-items-center gap-3" style="background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af;">
                    <div class="spinner-border text-primary flex-shrink-0" role="status"></div>
                    <div>
                        <div class="fw-bold small">Sedang memproses & mengompres berkas ke CDN...</div>
                        <div class="small text-muted" style="font-size: 0.785rem;">Mohon jangan menutup halaman ini selama proses unggah berlangsung.</div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-2">
                    <a href="{{ route('logout.get') }}" class="small text-muted text-decoration-none order-2 order-sm-1 py-1">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Keluar / Logout Akun
                    </a>

                    <button type="submit" id="btnSubmit" class="btn btn-submit d-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto px-4 order-1 order-sm-2">
                        <i class="fa-solid fa-circle-check" id="btnIcon"></i>
                        <span id="btnText">Simpan & Buka Akses Dashboard</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- FOOTER LINKS -->
        <div class="text-center mt-3 pb-1">
            <div class="mb-1">
                <a href="{{ route('legal.privacy') }}" target="_blank" class="text-muted small text-decoration-none me-3">Kebijakan Privasi</a>
                <span class="text-muted small">&bull;</span>
                <a href="{{ route('legal.terms') }}" target="_blank" class="text-muted small text-decoration-none ms-3">Syarat & Ketentuan</a>
            </div>
            <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia (PP PBVSI)
            </p>
        </div>
    </div>

    <!-- MODAL PERATURAN PLATFORM PBVSI -->
    <div class="modal fade" id="modalPeraturan" tabindex="-1" aria-labelledby="modalPeraturanLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-primary fa-lg"></i>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalPeraturanLabel">Peraturan & Ketentuan Platform PBVSI</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4" style="font-size: 0.9rem; line-height: 1.6; color: #334155;">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3 rounded-3">
                        <i class="fa-solid fa-circle-info text-primary fa-lg"></i>
                        <div>Dokumen ini mengikat secara hukum bagi seluruh pengguna, atlet, pelatih, wasit, dan pengurus klub/daerah di lingkungan PP PBVSI.</div>
                    </div>

                    <h6 class="fw-bold text-dark mt-3"><i class="fa-solid fa-check text-primary me-2"></i>1. Integritas dan Keabsahan Data Identitas</h6>
                    <p class="text-muted small">
                        Setiap pengguna wajib memberikan data identitas asli yang sesuai dengan kartu tanda penduduk (KTP/KIA) yang sah dikeluarkan oleh instansi pemerintah berwenang. Pemalsuan dokumen, manipulasi umur (pencurian umur), atau penggunaan identitas orang lain merupakan pelanggaran berat terhadap Statuta PBVSI dan akan dikenakan sanksi larangan beraktivitas bolavoli hingga tuntutan hukum pidana.
                    </p>

                    <h6 class="fw-bold text-dark mt-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i>2. Perlindungan Data Pribadi (UU PDP No. 27/2022)</h6>
                    <p class="text-muted small">
                        PP PBVSI berkomitmen melindungi kerahasiaan berkas KTP dan foto identitas Anda. Data ini hanya digunakan untuk tujuan verifikasi keabsahan lisensi wasit/pelatih, legalitas roster atlet kejuaraan nasional/daerah, dan pencatatan database nasional bolavoli. Data Anda tidak akan dialihkan atau diperjualbelikan kepada pihak ketiga di luar ekosistem resmi PBVSI.
                    </p>

                    <h6 class="fw-bold text-dark mt-3"><i class="fa-solid fa-handshake-angle text-primary me-2"></i>3. Komitmen Sportivitas & Etika Bolavoli</h6>
                    <p class="text-muted small">
                        Pengguna sepakat menjunjung tinggi nilai sportivitas, kejujuran (fair play), serta menghormati seluruh keputusan perangkat pertandingan, komisi disiplin, dan pengurus PBVSI.
                    </p>

                    <h6 class="fw-bold text-dark mt-3"><i class="fa-solid fa-server text-primary me-2"></i>4. Penyimpanan Digital & Pembuktian Elektronik</h6>
                    <p class="text-muted small">
                        Persetujuan yang Anda berikan dicatat secara otomatis dalam sistem basis data digital PBVSI lengkap dengan cap waktu (*timestamp*) dan alamat IP perangkat Anda sebagai bukti persetujuan elektronik yang sah.
                    </p>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="background: #f8fafc;">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-4" id="btnModalSetuju" data-bs-dismiss="modal">
                        <i class="fa-solid fa-check me-1"></i> Saya Paham & Menyetujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper live preview gambar
            function setupImagePreview(inputId, previewId, placeholderId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = placeholderId ? document.getElementById(placeholderId) : null;

                if (input && preview) {
                    input.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.classList.remove('d-none');
                                if (placeholder) {
                                    placeholder.classList.add('d-none');
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }

            setupImagePreview('avatarInput', 'avatarPreview');
            setupImagePreview('ktpInput', 'ktpPreview', 'ktpPlaceholder');
            setupImagePreview('selfieInput', 'selfiePreview', 'selfiePlaceholder');

            // Checkbox & Consent Card Interactivity
            const checkboxAgree = document.getElementById('agreeTerms');
            const consentCardWrapper = document.getElementById('consentCardWrapper');
            const btnModalSetuju = document.getElementById('btnModalSetuju');

            if (consentCardWrapper && checkboxAgree) {
                consentCardWrapper.addEventListener('click', function(e) {
                    // Jangan toggle jika user mengklik link atau tombol modal
                    if (e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }
                    if (e.target !== checkboxAgree) {
                        checkboxAgree.checked = !checkboxAgree.checked;
                    }
                });
            }

            if (btnModalSetuju && checkboxAgree) {
                btnModalSetuju.addEventListener('click', function() {
                    checkboxAgree.checked = true;
                });
            }

            // Validasi format angka NIK
            const nikInput = document.getElementById('nikInput');
            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Loader saat submit form
            const form = document.getElementById('profileForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const btnIcon = document.getElementById('btnIcon');
            const btnText = document.getElementById('btnText');
            const loadingStatus = document.getElementById('loadingStatus');

            if (form && btnSubmit) {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        return;
                    }

                    if (loadingStatus) {
                        loadingStatus.classList.remove('d-none');
                        loadingStatus.classList.add('d-flex');
                    }

                    if (btnIcon) {
                        btnIcon.className = 'fa-solid fa-circle-notch fa-spin';
                    }
                    if (btnText) {
                        btnText.textContent = 'Menyimpan & Mengunggah...';
                    }

                    setTimeout(function() {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.85';
                        btnSubmit.style.cursor = 'not-allowed';
                    }, 0);
                });
            }
        });
    </script>
</body>
</html>
