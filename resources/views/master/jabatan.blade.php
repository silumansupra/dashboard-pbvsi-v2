@extends('layouts.app')

@section('title', 'Master Jabatan & Official')

@section('content')
<div class="breadcrumb-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house fa-sm me-1 text-primary"></i> PBVSI Pusat</a></li>
            <li class="breadcrumb-item text-secondary">Pengaturan Master</li>
            <li class="breadcrumb-item active"><span class="badge bg-primary px-2 py-1">Master Jabatan</span></li>
        </ol>
    </nav>
    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Jabatan</button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fw-bold">Daftar Jabatan Resmi (Struktur Organisasi & Official Tim)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Nama Jabatan</th>
                                <th>Kategori Penugasan</th>
                                <th>Status Aktif</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jabatan as $j)
                            <tr>
                                <td>{{ $j->id }}</td>
                                <td><strong>{{ $j->nama_jabatan }}</strong></td>
                                <td>
                                    @if($j->is_official_tim)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="fa-solid fa-clipboard-user me-1"></i> Official Tim / Turnamen
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            <i class="fa-solid fa-sitemap me-1"></i> Pengurus Organisasi PBVSI
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $j->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $j->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen-to-square"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
