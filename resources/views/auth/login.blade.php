<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard Terpadu PBVSI Pusat">
    <meta name="author" content="PP PBVSI">
    <title>Login - Dashboard Terpadu PBVSI</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: #ffffff;
            overflow: hidden;
            max-width: 440px;
            width: 100%;
        }
        .brand-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 2.25rem 2rem;
            text-align: center;
            color: #ffffff;
        }
        .brand-logo-circle {
            width: 68px;
            height: 68px;
            background: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            color: #1e3a8a;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="login-card">
                    <!-- BRAND HEADER -->
                    <div class="brand-header">
                        <div class="brand-logo-circle">
                            <i class="fa-solid fa-volleyball"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-white">DASHBOARD PBVSI</h4>
                        <p class="small text-white-50 mb-0">Persatuan Bola Voli Seluruh Indonesia</p>
                    </div>

                    <!-- CARD BODY -->
                    <div class="p-4 p-md-5">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center mb-3" role="alert">
                            <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center mb-3" role="alert">
                            <i class="fa-solid fa-circle-exclamation fa-lg me-2 text-danger"></i>
                            <div>{{ $errors->first() }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold small">Username atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" name="login" class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror" 
                                           placeholder="Contoh: silumansupra atau #adm..." 
                                           value="{{ old('login') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold small">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" id="loginPassword" name="password" class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                                           placeholder="Masukkan password Anda" required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="btnTogglePassword">
                                        <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="rememberMe">
                                        Ingat saya
                                    </label>
                                </div>
                                <span class="text-muted small">v2.0 PBVSI</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard
                            </button>
                        </form>
                    </div>

                    <div class="bg-light py-3 px-4 text-center border-top">
                        <small class="text-muted">&copy; {{ date('Y') }} Pengurus Pusat PBVSI. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passInput = document.getElementById('loginPassword');
            const toggleBtn = document.getElementById('btnTogglePassword');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function() {
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                        toggleIcon.className = 'fa-solid fa-eye-slash';
                    } else {
                        passInput.type = 'password';
                        toggleIcon.className = 'fa-solid fa-eye';
                    }
                });
            }
        });
    </script>
</body>
</html>
