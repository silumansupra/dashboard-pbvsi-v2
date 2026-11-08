@extends('layouts.app')

@section('title', 'Akun Admin Tim - Menunggu Penugasan')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="card-body">
                <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                </div>
                <h4 class="fw-bold text-dark">Belum Ada Tim yang Dihubungkan</h4>
                <p class="text-muted">
                    Halo <strong>{{ $user->name }}</strong>, akun Anda berstatus <code>admin_tim</code> tetapi saat ini belum terdaftar mengelola tim atau kontingen peserta kejuaraan.
                </p>
                <div class="alert alert-info text-start small">
                    <i class="fa-solid fa-circle-info me-1"></i> Silakan hubungi <strong>Panitia Pusat PP PBVSI</strong> atau Pengprov terkait untuk menghubungkan akun Anda dengan tim peserta Kejurnas / Livoli.
                </div>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
