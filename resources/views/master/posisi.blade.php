@extends('layouts.app')

@section('title', 'Master Posisi Pemain Voli')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Posisi Pemain</span></li>
        </ol>
    </nav>
    <div class="d-none d-md-flex align-items-center text-muted small">
        <i class="fa-solid fa-volleyball text-warning me-1"></i> Standar Resmi FIVB / PBVSI
    </div>
</div>

<div class="row">
    @foreach($posisi as $pos)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100 border-start border-4 
            @if($pos->kode_posisi == 'L') border-success
            @elseif($pos->kode_posisi == 'S') border-primary
            @elseif($pos->kode_posisi == 'OH') border-danger
            @elseif($pos->kode_posisi == 'OP') border-warning
            @elseif($pos->kode_posisi == 'MB') border-info
            @endif">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge fs-5 px-3 py-1 text-white 
                        @if($pos->kode_posisi == 'L') bg-success
                        @elseif($pos->kode_posisi == 'S') bg-primary
                        @elseif($pos->kode_posisi == 'OH') bg-danger
                        @elseif($pos->kode_posisi == 'OP') bg-warning text-dark
                        @elseif($pos->kode_posisi == 'MB') bg-info
                        @endif">
                        {{ $pos->kode_posisi }}
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                </div>
                <h5 class="card-title fw-bold mt-2 mb-1">{{ $pos->nama_posisi }}</h5>
                <p class="card-text text-muted small">{{ $pos->deskripsi }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
