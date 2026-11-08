<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dokumen Resmi Legalitas, Regulasi & Kebijakan Platform Persatuan Bola Voli Seluruh Indonesia">
    <meta name="author" content="PP PBVSI">
    <title>@yield('title', 'Legalitas & Regulasi') - Platform PBVSI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .legal-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .legal-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #0f172a;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }
        .legal-brand:hover {
            color: #1d4ed8;
        }
        .legal-brand img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }
        .legal-nav-tabs .nav-link {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .legal-nav-tabs .nav-link:hover {
            color: #1d4ed8;
            background: #f1f5f9;
        }
        .legal-nav-tabs .nav-link.active {
            color: #1d4ed8;
            background: #eff6ff;
            font-weight: 700;
        }
        .breadcrumb-custom {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        .breadcrumb-custom .breadcrumb {
            margin-bottom: 0;
            padding: 0;
            background: transparent;
            display: flex;
            align-items: center;
        }
        .breadcrumb-custom .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            color: #64748b;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .breadcrumb-custom .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: color 0.15s ease;
        }
        .breadcrumb-custom .breadcrumb-item a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
        .breadcrumb-custom .breadcrumb-item.active {
            color: #0f172a;
            font-weight: 600;
        }
        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #94a3b8;
            font-size: 0.85rem;
            line-height: 1;
            padding: 0 0.6rem;
        }
        .legal-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            margin-top: auto;
            padding: 2rem 0;
        }
        @media print {
            .legal-navbar,
            .legal-footer,
            .breadcrumb-custom,
            .sticky-top,
            .btn,
            button {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .container, .container-xl {
                max-width: 100% !important;
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            .col-lg-9 {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <!-- TOP NAVBAR (STANDALONE / NO SIDEBAR) -->
    <header class="legal-navbar">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}" class="legal-brand">
                        <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" 
                             alt="Logo PBVSI"
                             onerror="this.onerror=null; this.src='{{ asset('assets/images/logo_pbvsi.png') }}';">
                        <div class="d-flex flex-column">
                            <span>PLATFORM PBVSI</span>
                            <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.685rem; width: fit-content;">Dokumen Regulasi Resmi</span>
                        </div>
                    </a>
                </div>

                <!-- LEGAL NAVIGATION TABS -->
                <nav class="legal-nav-tabs d-none d-md-flex align-items-center gap-1">
                    <a href="{{ route('legal.privacy') }}" class="nav-link {{ request()->routeIs('legal.privacy') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-halved"></i> Kebijakan Privasi
                    </a>
                    <a href="{{ route('legal.terms') }}" class="nav-link {{ request()->routeIs('legal.terms') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-contract"></i> Syarat & Ketentuan
                    </a>
                </nav>

                <!-- USER ACTIONS -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-house"></i>
                            <span>Kembali ke Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 fw-semibold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Masuk ke Platform</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="container-xl py-4 flex-grow-1">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="legal-footer">
        <div class="container-xl">
            <div class="row align-items-center gy-3">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-1 fw-semibold text-dark small">
                        Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (PP PBVSI)
                    </p>
                    <p class="mb-0 text-muted small">
                        &copy; {{ date('Y') }} Platform Terpadu Bola Voli Indonesia. Hak Cipta Dilindungi Undang-Undang.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3 flex-wrap small">
                        <a href="{{ route('legal.privacy') }}" class="text-decoration-none {{ request()->routeIs('legal.privacy') ? 'fw-bold text-primary' : 'text-muted' }}">
                            <i class="fa-solid fa-shield-halved me-1"></i> Kebijakan Privasi
                        </a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('legal.terms') }}" class="text-decoration-none {{ request()->routeIs('legal.terms') ? 'fw-bold text-primary' : 'text-muted' }}">
                            <i class="fa-solid fa-file-contract me-1"></i> Syarat & Ketentuan
                        </a>
                        <span class="text-muted">&bull;</span>
                        <span class="badge bg-secondary-subtle text-secondary border">{{ date('Ymd') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
