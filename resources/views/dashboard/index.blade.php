@extends('layouts.app')

@section('title', 'Dashboard - PBVSI Platform')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px;">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center g-4">
                    <div class="col-auto">
                        <div class="rounded-circle bg-white p-3 shadow d-flex align-items-center justify-content-center" style="width: 88px; height: 88px;">
                            <img src="{{ asset('assets/images/logo_pbvsi.png') }}" alt="Logo PBVSI" class="img-fluid" style="max-height: 60px;" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo_pbvsi_64px.png') }}';">
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                <i class="fa-solid fa-shield-halved me-1"></i> Platform Resmi
                            </span>
                            <span class="badge bg-success-subtle text-success px-3 py-1 fw-semibold">
                                <i class="fa-solid fa-circle-check me-1"></i> Sistem Aktif
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-2 fs-1">Selamat Datang di Platform PBVSI</h2>
                        <p class="text-white-50 mb-0 fs-5">
                            Pusat kendali dan sistem informasi terpadu Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia.
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-black bg-opacity-25 border-top border-white border-opacity-10 px-4 px-md-5 py-3 text-white-50 small d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-user text-info"></i>
                    <span>Masuk sebagai: <strong class="text-white">{{ Auth::user()->name ?? 'Administrator' }}</strong></span>
                    @if(Auth::user() && Auth::user()->roles->isNotEmpty())
                        <span class="badge bg-secondary ms-1">{{ Auth::user()->roles->first()->display_name ?? Auth::user()->roles->first()->name }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <i class="fa-solid fa-calendar-day text-warning me-1"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
