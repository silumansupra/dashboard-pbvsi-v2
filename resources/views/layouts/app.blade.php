<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard Terpadu PBVSI & Kejurnas Bolavoli">
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
            pointer-events: none;
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
            }
            .modal-dialog.modal-sm {
                max-width: 300px;
            }
        }
        @media (min-width: 992px) {
            .modal-dialog.modal-lg {
                max-width: 800px;
            }
        }
        @media (min-width: 1200px) {
            .modal-dialog.modal-xl {
                max-width: 1140px;
            }
        }
        @media (min-width: 1400px) {
            .modal-dialog.modal-xl {
                max-width: 1320px;
            }
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
        /* Modern Clean Pagination Styling */
        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
            gap: 4px;
            align-items: center;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0.25rem 0.6rem;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1;
            color: #475569;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px !important;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }
        .pagination .page-link:hover {
            z-index: 2;
            color: #2563eb;
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .pagination .page-link:focus {
            z-index: 3;
            color: #2563eb;
            background-color: #eff6ff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }
        .pagination .page-item.active .page-link {
            z-index: 3;
            color: #ffffff !important;
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
        }
        .pagination .page-item.disabled .page-link {
            color: #94a3b8 !important;
            pointer-events: none;
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            opacity: 0.8;
            cursor: not-allowed;
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
    </style>
    @stack('styles')
</head>

<body>
    @php
        $authUser = auth()->user();
        $isSuperadmin = $authUser && $authUser->hasRole('superadmin');
        $isAdminTim = $authUser && $authUser->hasRole('admin_tim') && !$isSuperadmin;
        $isOtherRole = $authUser && !$isSuperadmin && !$isAdminTim;
        $userRoleObj = $authUser ? $authUser->roles->first() : null;
        $userRole = $userRoleObj ? $userRoleObj->display_name : ($authUser ? 'Anggota PBVSI' : 'Tamu');

        $userTeams = collect();
        $activeNavTeam = null;
        $teamRosterCount = 0;
        $teamOfficialCount = 0;

        if ($isAdminTim) {
            $userTeams = $authUser->teams()->with(['edisi.turnamen', 'pengprov'])->get();
            $activeTeamId = session('active_team_id', $userTeams->first()?->id);
            $activeNavTeam = $userTeams->where('id', $activeTeamId)->first() ?? $userTeams->first();
            if ($activeNavTeam) {
                $teamRosterCount = \App\Models\Roster::where('peserta_tim_id', $activeNavTeam->id)->count();
                $teamOfficialCount = \App\Models\OfficialTim::where('peserta_tim_id', $activeNavTeam->id)->count();
            }
        }

        $dynamicMenus = $authUser ? app(\App\Services\MenuService::class)->getMenusForUser($authUser) : [];
    @endphp

    <div class="wrapper">
        <!-- SIDEBAR -->
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand-custom" href="{{ route('dashboard.index') }}">
                    <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" alt="PBVSI" height="36">
                    <span>
                        @if($isAdminTim)
                            PORTAL TIM PBVSI
                        @elseif($isSuperadmin)
                            PBVSI PUSAT
                        @else
                            PBVSI PLATFORM
                        @endif
                    </span>
                </a>

                @if($isAdminTim)
                    <!-- WIDGET TIM AKTIF DI SIDEBAR -->
                    @if($activeNavTeam)
                        <div class="px-3 py-3 mx-2 my-2 rounded-3 text-white" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge {{ $activeNavTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ $activeNavTeam->kategori_gender == 'PA' ? 'PUTRA (PA)' : 'PUTRI (PI)' }}
                                </span>
                                <span class="small text-white-50 text-truncate">{{ $activeNavTeam->edisi?->tahun_edisi ?? 'Kejurnas' }}</span>
                            </div>
                            <div class="fw-bold text-white text-truncate fs-6" title="{{ $activeNavTeam->nama_tim }}">{{ $activeNavTeam->nama_tim }}</div>
                            <div class="small text-white-50 text-truncate">{{ $activeNavTeam->pengprov?->nama_pengprov ?? 'Kontingen Daerah' }}</div>

                            @if($userTeams->count() > 1)
                                <div class="mt-2 pt-2 border-top border-secondary-subtle">
                                    <div class="small text-white-50 mb-1" style="font-size: 0.75rem;"><i class="fa-solid fa-repeat me-1"></i> Ganti Tim Aktif:</div>
                                    <div class="d-flex gap-1">
                                        @foreach($userTeams as $ut)
                                            <a href="{{ route('tim.switch', $ut->id) }}"
                                               class="btn btn-xs btn-sm {{ $ut->id == $activeNavTeam->id ? ($ut->kategori_gender == 'PA' ? 'btn-primary' : 'btn-danger') : 'btn-outline-light' }} py-1 px-2 flex-fill text-center"
                                               style="font-size: 0.75rem;">
                                                {{ $ut->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <ul class="sidebar-nav">
                        <li class="sidebar-header">Manajemen Tim</li>
                        <li class="sidebar-item {{ request()->routeIs('dashboard*') && !request()->routeIs('tim.*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                                <i class="align-middle fa-solid fa-gauge-high text-primary"></i> <span class="align-middle">Dashboard Tim</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('tim.profil*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('tim.profil') }}">
                                <i class="align-middle fa-solid fa-shield-halved text-info"></i> <span class="align-middle">Profil & Jersey Tim</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('tim.pemain*') ? 'active' : '' }}">
                            <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('tim.pemain') }}">
                                <div>
                                    <i class="align-middle fa-solid fa-users text-success"></i> <span class="align-middle">Data Pemain</span>
                                </div>
                                <span class="badge {{ $teamRosterCount >= 10 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill">{{ $teamRosterCount }} / 14</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('tim.official*') ? 'active' : '' }}">
                            <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ route('tim.official') }}">
                                <div>
                                    <i class="align-middle fa-solid fa-user-tie text-warning"></i> <span class="align-middle">Official & Pelatih</span>
                                </div>
                                <span class="badge bg-secondary rounded-pill">{{ $teamOfficialCount }}</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('tim.roster*') && !request()->routeIs('tim.roster.print_o2') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('tim.roster') }}">
                                <i class="align-middle fa-solid fa-clipboard-list text-danger"></i> <span class="align-middle">Susunan Roster O-2</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('tim.roster.print_o2') }}" target="_blank">
                                <i class="align-middle fa-solid fa-print text-warning"></i> <span class="align-middle">Cetak Lembar O-2</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1 small text-white-50"></i>
                            </a>
                        </li>

                        @php
                            $extraTimMenus = collect($dynamicMenus)->filter(function($m) {
                                return !str_starts_with($m['route_name'] ?? '', 'tim.') && $m['route_name'] !== 'dashboard.index';
                            });
                        @endphp
                        @if($extraTimMenus->isNotEmpty())
                            <li class="sidebar-header mt-3 text-warning">
                                <i class="fa-solid fa-key me-1"></i> Wewenang Khusus
                            </li>
                            @foreach($extraTimMenus as $em)
                                @if(!empty($em['children']) && count($em['children']) > 0)
                                    <li class="sidebar-header mt-2">{{ $em['title'] }}</li>
                                    @foreach($em['children'] as $child)
                                        @php
                                            $isChildActive = (!empty($child['route_name']) && request()->routeIs($child['route_name'])) ||
                                                            (!empty($child['url']) && $child['url'] !== '#!' && request()->is(ltrim($child['url'], '/') . '*'));
                                        @endphp
                                        <li class="sidebar-item {{ $isChildActive ? 'active' : '' }}">
                                            <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}">
                                                <div>
                                                    <i class="align-middle {{ $child['icon'] ?: 'fa-regular fa-circle' }}"></i>
                                                    <span class="align-middle">{{ $child['title'] }}</span>
                                                </div>
                                                @if(!empty($child['badge_text']))
                                                    <span class="badge {{ $child['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                        {{ $child['badge_text'] }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    @php
                                        $isRootActive = (!empty($em['route_name']) && request()->routeIs($em['route_name'])) ||
                                                        (!empty($em['url']) && $em['url'] !== '#!' && request()->is(ltrim($em['url'], '/') . '*'));
                                    @endphp
                                    <li class="sidebar-item {{ $isRootActive ? 'active' : '' }}">
                                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $em['url'] }}" target="{{ $em['target'] ?? '_self' }}">
                                            <div>
                                                <i class="align-middle {{ $em['icon'] ?: 'fa-solid fa-circle-dot' }}"></i>
                                                <span class="align-middle">{{ $em['title'] }}</span>
                                            </div>
                                            @if(!empty($em['badge_text']))
                                                <span class="badge {{ $em['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                    {{ $em['badge_text'] }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                        <li class="sidebar-header mt-3">Pengaturan Akun</li>
                        <li class="sidebar-item {{ request()->routeIs('tim.akun*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('tim.akun') }}">
                                <i class="align-middle fa-solid fa-user-gear"></i> <span class="align-middle">Profil Akun Saya</span>
                            </a>
                        </li>
                        <li class="sidebar-item mt-2">
                            <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                @elseif($isSuperadmin)
                    <!-- TAMPILAN SIDEBAR RESMI DINAMIS SUPERADMIN / PP PBVSI (CACHED) -->
                    <ul class="sidebar-nav">
                        @if(!empty($dynamicMenus) && count($dynamicMenus) > 0)
                            @foreach($dynamicMenus as $item)
                                @if(!empty($item['children']) && count($item['children']) > 0)
                                    <li class="sidebar-header mt-3">{{ $item['title'] }}</li>
                                    @foreach($item['children'] as $child)
                                        @php
                                            $isChildActive = (!empty($child['route_name']) && request()->routeIs($child['route_name'])) ||
                                                            (!empty($child['url']) && $child['url'] !== '#!' && request()->is(ltrim($child['url'], '/') . '*'));
                                        @endphp
                                        <li class="sidebar-item {{ $isChildActive ? 'active' : '' }}">
                                            <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}">
                                                <div>
                                                    <i class="align-middle {{ $child['icon'] ?: 'fa-regular fa-circle' }}"></i>
                                                    <span class="align-middle">{{ $child['title'] }}</span>
                                                </div>
                                                @if(!empty($child['badge_text']))
                                                    <span class="badge {{ $child['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                        {{ $child['badge_text'] }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    @php
                                        $isRootActive = (!empty($item['route_name']) && request()->routeIs($item['route_name'])) ||
                                                        (!empty($item['url']) && $item['url'] !== '#!' && request()->is(ltrim($item['url'], '/') . '*'));
                                    @endphp
                                    <li class="sidebar-item {{ $isRootActive ? 'active' : '' }}">
                                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}">
                                            <div>
                                                <i class="align-middle {{ $item['icon'] ?: 'fa-solid fa-circle-dot' }}"></i>
                                                <span class="align-middle">{{ $item['title'] }}</span>
                                            </div>
                                            @if(!empty($item['badge_text']))
                                                <span class="badge {{ $item['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                    {{ $item['badge_text'] }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @else
                            <li class="sidebar-header">Pusat Platform</li>
                            <li class="sidebar-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                                    <i class="align-middle fa-solid fa-house"></i> <span class="align-middle">Dashboard Utama</span>
                                </a>
                            </li>
                        @endif

                        <li class="sidebar-header mt-3">Akun & Sesi</li>
                        <li class="sidebar-item">
                            <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                @elseif($isOtherRole)
                    <!-- TAMPILAN SIDEBAR UNTUK ANGGOTA / ROLE LAIN (PEMAIN, WASIT, PELATIH, PENGPROV, KLUB) -->
                    <div class="px-3 py-3 mx-2 my-2 rounded-3 text-white" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary px-2 py-1 small">
                                {{ strtoupper($userRole) }}
                            </span>
                        </div>
                        <div class="fw-bold text-white text-truncate fs-6" title="{{ $authUser->name }}">{{ $authUser->name }}</div>
                        <div class="small text-white-50 text-truncate">ID PBVSI: <code>{{ $authUser->username }}</code></div>
                    </div>

                    <ul class="sidebar-nav">
                        <li class="sidebar-header">Area Anggota</li>
                        <li class="sidebar-item {{ request()->routeIs('dashboard*') && !request()->routeIs('akun.*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                                <i class="align-middle fa-solid fa-house text-primary"></i> <span class="align-middle">Beranda Portal</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('akun.saya*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('akun.saya') }}">
                                <i class="align-middle fa-solid fa-user-gear text-info"></i> <span class="align-middle">Profil & Akun Saya</span>
                            </a>
                        </li>

                        @php
                            $extraOtherMenus = collect($dynamicMenus)->filter(function($m) {
                                return !in_array($m['route_name'] ?? '', ['dashboard.index', 'akun.saya']);
                            });
                        @endphp
                        @if($extraOtherMenus->isNotEmpty())
                            <li class="sidebar-header mt-3 text-warning">
                                <i class="fa-solid fa-key me-1"></i> Wewenang Khusus
                            </li>
                            @foreach($extraOtherMenus as $em)
                                @if(!empty($em['children']) && count($em['children']) > 0)
                                    <li class="sidebar-header mt-2">{{ $em['title'] }}</li>
                                    @foreach($em['children'] as $child)
                                        @php
                                            $isChildActive = (!empty($child['route_name']) && request()->routeIs($child['route_name'])) ||
                                                            (!empty($child['url']) && $child['url'] !== '#!' && request()->is(ltrim($child['url'], '/') . '*'));
                                        @endphp
                                        <li class="sidebar-item {{ $isChildActive ? 'active' : '' }}">
                                            <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}">
                                                <div>
                                                    <i class="align-middle {{ $child['icon'] ?: 'fa-regular fa-circle' }}"></i>
                                                    <span class="align-middle">{{ $child['title'] }}</span>
                                                </div>
                                                @if(!empty($child['badge_text']))
                                                    <span class="badge {{ $child['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                        {{ $child['badge_text'] }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    @php
                                        $isRootActive = (!empty($em['route_name']) && request()->routeIs($em['route_name'])) ||
                                                        (!empty($em['url']) && $em['url'] !== '#!' && request()->is(ltrim($em['url'], '/') . '*'));
                                    @endphp
                                    <li class="sidebar-item {{ $isRootActive ? 'active' : '' }}">
                                        <a class="sidebar-link d-flex justify-content-between align-items-center" href="{{ $em['url'] }}" target="{{ $em['target'] ?? '_self' }}">
                                            <div>
                                                <i class="align-middle {{ $em['icon'] ?: 'fa-solid fa-circle-dot' }}"></i>
                                                <span class="align-middle">{{ $em['title'] }}</span>
                                            </div>
                                            @if(!empty($em['badge_text']))
                                                <span class="badge {{ $em['badge_class'] ?: 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                                    {{ $em['badge_text'] }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                        <li class="sidebar-header mt-3">Kompetisi Nasional</li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/kejurnas-u16*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'kejurnas-u16']) }}">
                                <i class="align-middle fa-solid fa-volleyball text-info"></i> <span class="align-middle">Kejurnas U-16</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/kejurnas-u17*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'kejurnas-u17']) }}">
                                <i class="align-middle fa-solid fa-volleyball text-success"></i> <span class="align-middle">Kejurnas U-17</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/livoli-divisi-utama*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'livoli-divisi-utama']) }}">
                                <i class="align-middle fa-solid fa-trophy text-warning"></i> <span class="align-middle">Livoli Divisi Utama</span>
                            </a>
                        </li>

                        <li class="sidebar-header mt-3">Legalitas & Ketentuan</li>
                        <li class="sidebar-item {{ request()->routeIs('legal.privacy') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer">
                                <i class="align-middle fa-solid fa-shield-halved text-success"></i> <span class="align-middle">Kebijakan Privasi</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1 small text-white-50"></i>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('legal.terms') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('legal.terms') }}" target="_blank" rel="noopener noreferrer">
                                <i class="align-middle fa-solid fa-file-contract text-warning"></i> <span class="align-middle">Syarat & Ketentuan</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1 small text-white-50"></i>
                            </a>
                        </li>

                        <li class="sidebar-header mt-3">Sesi Login</li>
                        <li class="sidebar-item">
                            <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                @else
                    <!-- TAMPILAN SIDEBAR UNTUK TAMU (GUEST) -->
                    <ul class="sidebar-nav">
                        <li class="sidebar-header">Menu Publik</li>
                        <li class="sidebar-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                                <i class="align-middle fa-solid fa-house text-primary"></i> <span class="align-middle">Beranda Utama</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/kejurnas-u16*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'kejurnas-u16']) }}">
                                <i class="align-middle fa-solid fa-volleyball text-info"></i> <span class="align-middle">Kejurnas U-16</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/kejurnas-u17*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'kejurnas-u17']) }}">
                                <i class="align-middle fa-solid fa-volleyball text-success"></i> <span class="align-middle">Kejurnas U-17</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('kejuaraan/livoli-divisi-utama*') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kejuaraan.index', ['slug' => 'livoli-divisi-utama']) }}">
                                <i class="align-middle fa-solid fa-trophy text-warning"></i> <span class="align-middle">Livoli Divisi Utama</span>
                            </a>
                        </li>
                        <li class="sidebar-header mt-3">Legalitas</li>
                        <li class="sidebar-item {{ request()->routeIs('legal.privacy') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer">
                                <i class="align-middle fa-solid fa-shield-halved text-success"></i> <span class="align-middle">Kebijakan Privasi</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1 small text-white-50"></i>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('legal.terms') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('legal.terms') }}" target="_blank" rel="noopener noreferrer">
                                <i class="align-middle fa-solid fa-file-contract text-warning"></i> <span class="align-middle">Syarat & Ketentuan</span>
                                <i class="fa-solid fa-arrow-up-right-from-square ms-1 small text-white-50"></i>
                            </a>
                        </li>
                        <li class="sidebar-header mt-3">Akses Akun</li>
                        <li class="sidebar-item px-3 py-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100 fw-semibold shadow-sm">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk / Login
                            </a>
                        </li>
                    </ul>
                @endif
            </div>
        </nav>

        <!-- MAIN APP -->
        <div class="main">
            <!-- TOP NAVBAR -->
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                @php
                    $userName = $authUser ? $authUser->name : 'Pengunjung Tamu';
                @endphp

                <div class="navbar-collapse collapse">
                    @if($isAdminTim && $activeNavTeam)
                        <div class="d-none d-md-flex align-items-center gap-2 ms-3">
                            <span class="badge {{ $activeNavTeam->kategori_gender == 'PA' ? 'bg-primary' : 'bg-danger' }} px-2 py-1">
                                <i class="fa-solid {{ $activeNavTeam->kategori_gender == 'PA' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                                {{ $activeNavTeam->kategori_gender == 'PA' ? 'TIM PUTRA (PA)' : 'TIM PUTRI (PI)' }}
                            </span>
                            <span class="fw-bold text-dark">{{ $activeNavTeam->nama_tim }}</span>

                            @if($userTeams->count() > 1)
                                <div class="btn-group btn-group-sm ms-2 shadow-sm">
                                    @foreach($userTeams as $ut)
                                        <a href="{{ route('tim.switch', $ut->id) }}"
                                           class="btn btn-sm {{ $ut->id == $activeNavTeam->id ? ($ut->kategori_gender == 'PA' ? 'btn-primary' : 'btn-danger') : 'btn-outline-secondary' }}">
                                            {{ $ut->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)' }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <ul class="navbar-nav navbar-align align-items-center ms-auto">
                        @if($authUser)
                            <li class="nav-item dropdown">
                                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                    @if($authUser && !empty($authUser->avatar_path))
                                        <img src="{{ $authUser->avatar_url }}" alt="{{ $userName }}" class="avatar img-fluid rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                    @else
                                        <i class="align-middle" data-feather="settings"></i>
                                    @endif
                                </a>

                                <a class="nav-link dropdown-toggle d-none d-sm-inline-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                    @if($authUser && !empty($authUser->avatar_path))
                                        <img src="{{ $authUser->avatar_url }}" alt="{{ $userName }}" class="avatar img-fluid rounded-circle border shadow-xs" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($userName, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="badge {{ $isAdminTim ? 'bg-warning text-dark' : ($isSuperadmin ? 'bg-primary' : 'bg-info text-dark') }}">{{ $userRole }}</span>
                                    <span class="text-dark fw-bold">{{ $userName }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <div class="px-3 py-2 border-bottom">
                                        <div class="fw-bold text-dark">{{ $userName }}</div>
                                        <small class="text-muted">{{ $userRole }}</small>
                                    </div>
                                    @if($isAdminTim)
                                        <a class="dropdown-item py-2" href="{{ route('tim.akun') }}">
                                            <i class="fa-solid fa-user-gear me-2 text-primary"></i> Profil Akun Saya
                                        </a>
                                        <a class="dropdown-item py-2" href="{{ route('tim.profil') }}">
                                            <i class="fa-solid fa-shield-halved me-2 text-info"></i> Profil Tim
                                        </a>
                                    @elseif($isSuperadmin)
                                        <a class="dropdown-item py-2" href="{{ route('master.users') }}">
                                            <i class="fa-solid fa-user-shield me-2 text-primary"></i> User RBAC
                                        </a>
                                        <a class="dropdown-item py-2" href="{{ route('akun.saya') }}">
                                            <i class="fa-solid fa-user-gear me-2 text-info"></i> Profil Akun Saya
                                        </a>
                                    @else
                                        <a class="dropdown-item py-2" href="{{ route('akun.saya') }}">
                                            <i class="fa-solid fa-user-gear me-2 text-primary"></i> Profil Akun Saya
                                        </a>
                                    @endif
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </li>

                            <!-- TOMBOL LOGOUT LANGSUNG (STANDALONE) -->
                            <li class="nav-item ms-2">
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 shadow-sm px-3 py-1" title="Logout dari Sistem">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span class="d-none d-sm-inline fw-semibold">Logout</span>
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-sm btn-primary px-3 py-1 shadow-sm fw-semibold">
                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk / Login
                                </a>
                            </li>
                        @endif
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
                    <div class="row text-muted align-items-center">
                        <div class="col-md-6 text-start">
                            <p class="mb-0">
                                <strong>PBVSI Platform</strong> &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia
                            </p>
                        </div>
                        <div class="col-md-6 text-end d-flex align-items-center justify-content-end gap-2 flex-wrap mt-2 mt-md-0">
                            <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none small">
                                <i class="fa-solid fa-shield-halved me-1 text-primary"></i>Kebijakan Privasi
                            </a>
                            <span class="text-muted small">&bull;</span>
                            <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none small">
                                <i class="fa-solid fa-file-contract me-1 text-info"></i>Syarat & Ketentuan
                            </a>
                            <span class="badge bg-secondary ms-2">{{ date('Ymd') }}</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('modals')

    <!-- AdminKit JS (sudah include Bootstrap 5 Bundle & Popper secara internal) -->
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

        // Unregister stale service workers (e.g. from previous apps on localhost/127.0.0.1)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for (var r of registrations) {
                    r.unregister();
                }
            }).catch(function(err) {});
        }
    </script>

    <!-- Global Loading Overlay -->
    @include('layouts.loading_overlay')

    @stack('scripts')
</body>
</html>
