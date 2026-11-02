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

    <!-- Google Fonts & Bootstrap 5 & AdminKit CSS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
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

        /* Modern Breadcrumb Styling */
        .breadcrumb-custom {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            border: 1px solid #edf2f7;
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
        }
        .breadcrumb-custom .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: color 0.15s ease-in-out;
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
            content: "›";
            color: #94a3b8;
            font-size: 1.2rem;
            line-height: 1;
            padding: 0 0.55rem;
        }

        /* Pastikan Modal Bootstrap 5 selalu di atas backdrop dan tersembunyi jika tidak aktif */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055 !important;
            display: none;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
        }
        .modal.show {
            display: block !important;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050 !important;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 500px;
            pointer-events: none;
        }
        .modal.show .modal-dialog {
            transform: none;
        }
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #ffffff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.175);
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            outline: 0;
        }
        /* Pagination styling & safety */
        .pagination {
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        .pagination .page-link {
            padding: 0.25rem 0.55rem;
            font-size: 0.8125rem;
            line-height: 1.4;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .pagination .page-link svg,
        .pagination .page-item svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
            font-weight: 600;
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

                    <li class="sidebar-header">Database Personel PBVSI</li>
                    <li class="sidebar-item {{ request()->routeIs('personel.pemain*') ? 'active' : '' }}">
                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('personel.pemain') }}">
                            <div>
                                <i class="align-middle fa-solid fa-id-card-clip text-primary"></i> <span class="align-middle">Bank Pemain</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">7.5k</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('personel.pelatih*') ? 'active' : '' }}">
                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('personel.pelatih') }}">
                            <div>
                                <i class="align-middle fa-solid fa-chalkboard-user text-warning"></i> <span class="align-middle">Data Pelatih</span>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">222</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('personel.wasit*') ? 'active' : '' }}">
                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('personel.wasit') }}">
                            <div>
                                <i class="align-middle fa-solid fa-flag text-danger"></i> <span class="align-middle">Data Wasit</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">119</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('personel.official*') ? 'active' : '' }}">
                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('personel.official') }}">
                            <div>
                                <i class="align-middle fa-solid fa-user-tie text-info"></i> <span class="align-middle">Official Tim</span>
                            </div>
                            <span class="badge bg-info text-dark rounded-pill">594</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Pengaturan & Master Data</li>
                    <li class="sidebar-item {{ request()->routeIs('master.wilayah') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.wilayah') }}">
                            <i class="align-middle fa-solid fa-map-location-dot"></i> <span class="align-middle">Master Wilayah</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.pengprov') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.pengprov') }}">
                            <i class="align-middle fa-solid fa-building-flag"></i> <span class="align-middle">Data Pengprov</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.pengkot') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.pengkot') }}">
                            <i class="align-middle fa-solid fa-city"></i> <span class="align-middle">Data Pengkot/Pengkab</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.klub') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.klub') }}">
                            <i class="align-middle fa-solid fa-shield-halved"></i> <span class="align-middle">Data Tim / Klub</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.jabatan') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.jabatan') }}">
                            <i class="align-middle fa-solid fa-user-tag"></i> <span class="align-middle">Master Jabatan</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.posisi') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.posisi') }}">
                            <i class="align-middle fa-solid fa-arrows-down-to-people"></i> <span class="align-middle">Posisi Pemain</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('master.users') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.users') }}">
                            <i class="align-middle fa-solid fa-user-shield"></i> <span class="align-middle">User & RBAC</span>
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

    @stack('modals')

    <!-- Bootstrap 5 Bundle JS & AdminKit JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    <script>
        // Pindahkan semua modal ke direct child <body> agar tidak terperangkap stacking context di .main / .wrapper
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal').forEach(function(modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
