<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard Terpadu PBVSI & Kejurnas Bola Voli">
    <meta name="author" content="PP PBVSI">
    <title>@yield('title', 'Dashboard Terpadu') - PBVSI</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/logo_pbvsi_64px.png') }}" />

    <!-- Google Fonts & AdminKit CSS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Alpine.js (Lightweight Reactive UI) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        .sidebar-brand-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1.15rem 1.5rem;
            color: #fff;
            text-decoration: none;
            background: #1e293b;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .badge-age {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <!-- SIDEBAR -->
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand-custom" href="{{ route('dashboard.index') }}">
                    <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" alt="PBVSI" height="36">
                    <span>PBVSI PUSAT</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Pusat Platform</li>
                    <li class="sidebar-item {{ request()->routeIs('dashboard*') && !request()->routeIs('kejurnas*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                            <i class="align-middle fa-solid fa-house"></i> <span class="align-middle">Dashboard Utama</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Kejurnas Kelompok Umur</li>
                    <li class="sidebar-item {{ request()->is('kejurnas/u16*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/kejurnas/u16/2026') }}">
                            <i class="align-middle fa-solid fa-volleyball text-info"></i> <span class="align-middle">Kejurnas U-16 2026</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->is('kejurnas/u17*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/kejurnas/u17/2026') }}">
                            <i class="align-middle fa-solid fa-volleyball text-success"></i> <span class="align-middle">Kejurnas U-17 2026</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->is('kejurnas/u18*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/kejurnas/u18/2026') }}">
                            <i class="align-middle fa-solid fa-volleyball text-warning"></i> <span class="align-middle">Kejurnas U-18 2026</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->is('kejurnas/u19*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/kejurnas/u19/2026') }}">
                            <i class="align-middle fa-solid fa-volleyball text-danger"></i> <span class="align-middle">Kejurnas U-19 2026</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Master & Anggota</li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-users"></i> <span class="align-middle">Bank Pemain Nasional</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-map-location-dot"></i> <span class="align-middle">Pengprov & Pengkot</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-shield-halved"></i> <span class="align-middle">Klub Resmi PBVSI</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-whistle"></i> <span class="align-middle">Wasit & Pelatih</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Modul Tambahan</li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-certificate"></i> <span class="align-middle">Sertifikasi & QR</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#!">
                            <i class="align-middle fa-solid fa-ticket"></i> <span class="align-middle">Tiket Online</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- MAIN APP -->
        <div class="main">
            <!-- TOP NAVBAR -->
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <span class="badge bg-primary me-2">PP PBVSI</span>
                                <span class="text-dark fw-bold">Superadmin PBVSI</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#!"><i class="align-middle me-1" data-feather="user"></i> Profil</a>
                                <a class="dropdown-item" href="#!"><i class="align-middle me-1" data-feather="settings"></i> Pengaturan</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#!"><i class="align-middle me-1" data-feather="log-out"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- CONTENT BODY -->
            <main class="content">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <strong>PBVSI Core Platform</strong> &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <span class="badge bg-secondary">Versi 2.0 (Integrated Monolith)</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- AdminKit JS -->
    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
