<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Atur Ulang Kata Sandi - Platform PBVSI">
    <meta name="author" content="PP PBVSI">
    <title>Atur Ulang Kata Sandi &bull; Platform PBVSI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 10%, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem 1rem;
            color: #334155;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            margin: auto;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08);
            padding: 2.25rem 2rem;
            position: relative;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 0 0 1px #e2e8f0;
            margin-bottom: 1.25rem;
            padding: 8px;
        }
        .brand-badge img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .brand-title {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            font-size: 1.4rem;
            margin-bottom: 0.25rem;
        }
        .brand-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }
        .input-group-modern {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        .input-group-modern:focus-within {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        .input-group-modern .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 0.875rem;
            padding-right: 0.5rem;
            font-size: 0.95rem;
        }
        .input-group-modern .form-control {
            background: transparent;
            border: none;
            font-size: 0.9rem;
            padding: 0.65rem 0.75rem;
            color: #0f172a;
            font-weight: 500;
        }
        .input-group-modern .form-control:focus {
            box-shadow: none;
            background: transparent;
        }
        .input-group-modern .btn-toggle {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 0 0.875rem;
            transition: color 0.15s ease;
        }
        .input-group-modern .btn-toggle:hover {
            color: #334155;
        }
        .btn-submit {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.925rem;
            padding: 0.75rem 1rem;
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
        .legal-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.785rem;
            transition: color 0.15s ease;
        }
        .legal-links a:hover {
            color: #cbd5e1;
            text-decoration: underline;
        }
        .footer-text {
            color: #64748b;
            font-size: 0.775rem;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- BRAND HEADER -->
            <div class="text-center">
                <div class="brand-badge">
                    <img src="{{ asset('assets/images/logo_pbvsi.png') }}"
                         alt="Logo PBVSI"
                         onerror="this.onerror=null; this.src='{{ asset('assets/images/logo_pbvsi_64px.png') }}';">
                </div>
                <h1 class="brand-title">Atur Ulang Kata Sandi</h1>
                <p class="brand-subtitle">Silakan buat kata sandi baru yang aman untuk akun Anda.</p>
            </div>

            <!-- NOTIFICATIONS -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-start mb-3 py-2 px-3 rounded-3" role="alert">
                <i class="fa-solid fa-circle-exclamation fa-lg me-2 mt-1 text-danger"></i>
                <div class="flex-grow-1">{{ $errors->first() }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- ACCOUNT BADGE -->
            <div class="p-2 px-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                <span class="small text-muted">Akun:</span>
                <span class="small fw-bold text-dark font-monospace">{{ $email }}</span>
            </div>

            <!-- RESET FORM -->
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label class="form-label" for="newPassword">Kata Sandi Baru</label>
                    <div class="input-group-modern d-flex align-items-center">
                        <span class="input-group-text">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="newPassword" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimal 6 karakter" required autofocus autocomplete="new-password">
                        <button class="btn-toggle" type="button" id="btnToggleNewPassword" aria-label="Tampilkan password">
                            <i class="fa-solid fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="confirmPassword">Konfirmasi Kata Sandi Baru</label>
                    <div class="input-group-modern d-flex align-items-center">
                        <span class="input-group-text">
                            <i class="fa-solid fa-shield-check"></i>
                        </span>
                        <input type="password" id="confirmPassword" name="password_confirmation"
                               class="form-control"
                               placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                        <button class="btn-toggle" type="button" id="btnToggleConfirmPassword" aria-label="Tampilkan konfirmasi password">
                            <i class="fa-solid fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <!-- LOADER STATUS (TAMPIL SAAT PROSES RESET) -->
                <div id="loadingStatus" class="p-2 px-3 mb-3 rounded-3 d-none align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 0.825rem; color: #1e40af;">
                    <div class="spinner-border spinner-border-sm text-primary flex-shrink-0" role="status" aria-hidden="true"></div>
                    <div>Sedang menyimpan kata sandi baru, mohon tunggu...</div>
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-submit w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                    <i class="fa-solid fa-floppy-disk" id="btnIcon"></i>
                    <span id="btnText">Simpan Kata Sandi Baru</span>
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="small text-decoration-none text-muted fw-semibold">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal & Kembali ke Login
                    </a>
                </div>
            </form>
        </div>

        <!-- FOOTER LINKS -->
        <div class="text-center mt-4">
            <div class="legal-links mb-2">
                <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a>
                <span class="text-muted mx-2">&bull;</span>
                <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a>
            </div>
            <p class="footer-text mb-0">
                &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia
            </p>
        </div>
    </div>

    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    <script>
        function setupToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (btn && input && icon) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = 'fa-solid fa-eye-slash';
                    } else {
                        input.type = 'password';
                        icon.className = 'fa-solid fa-eye';
                    }
                });
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            setupToggle('btnToggleNewPassword', 'newPassword', 'toggleIcon1');
            setupToggle('btnToggleConfirmPassword', 'confirmPassword', 'toggleIcon2');

            const form = document.querySelector('form');
            const btn = document.getElementById('btnSubmit');
            const btnIcon = document.getElementById('btnIcon');
            const btnText = document.getElementById('btnText');
            const loadingStatus = document.getElementById('loadingStatus');

            if (form && btn) {
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
                        btnText.textContent = 'Menyimpan...';
                    }
                    setTimeout(function() {
                        btn.disabled = true;
                        btn.style.opacity = '0.8';
                        btn.style.cursor = 'not-allowed';
                    }, 0);
                });
            }
        });
    </script>
</body>
</html>
