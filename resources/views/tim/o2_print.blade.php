<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Formulir O-2 - {{ $activeTeam->nama_tim }} ({{ $activeTeam->kategori_gender }}) - PBVSI</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', Arial, sans-serif;
            color: #1e293b;
        }
        .print-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header-logo {
            width: 80px;
            height: auto;
        }
        .title-kop h2 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .title-kop h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .title-kop p {
            font-size: 0.8rem;
            margin-bottom: 0;
            color: #475569;
        }
        .divider-thick {
            border-top: 3px solid #0f172a;
            margin: 12px 0 16px 0;
        }
        .table-o2 {
            font-size: 0.85rem;
        }
        .table-o2 th {
            background-color: #f8fafc !important;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #cbd5e1 !important;
        }
        .table-o2 td {
            vertical-align: middle;
            border: 1px solid #cbd5e1 !important;
            padding: 6px 8px;
        }
        .signature-box {
            margin-top: 40px;
            font-size: 0.85rem;
        }
        .sign-line {
            height: 70px;
        }
        @media print {
            body {
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
            }
            .table-o2 th, .table-o2 td {
                border-color: #000 !important;
            }
            .divider-thick {
                border-color: #000 !important;
            }
        }
    </style>
</head>
<body>

<!-- FLOATING PRINT TOOLBAR (SCREEN ONLY) -->
<div class="no-print bg-dark py-3 px-4 shadow sticky-top text-white d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('tim.roster') }}" class="btn btn-outline-light btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Roster
        </a>
        <span class="fw-bold">Preview Formulir O-2: {{ $activeTeam->nama_tim }} ({{ $activeTeam->kategori_gender }})</span>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold px-3">
            <i class="fa-solid fa-print me-1"></i> CETAK / SIMPAN PDF
        </button>
    </div>
</div>

<div class="print-container">
    <!-- KOP FORMULIR PBVSI -->
    <div class="d-flex align-items-center gap-4 text-center justify-content-center">
        <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" alt="PBVSI" class="header-logo">
        <div class="title-kop text-center">
            <h2>PENGURUS PUSAT PERSATUAN BOLA VOLI SELURUH INDONESIA</h2>
            <h3>FORMULIR PENDAFTARAN SKUAD TIM & ROSTER (FORM O-2)</h3>
            <p>{{ $activeTeam->edisi?->turnamen?->nama_turnamen ?? 'KEJUARAAN NASIONAL BOLA VOLI' }} {{ $activeTeam->edisi?->tahun_edisi ? 'TAHUN ' . $activeTeam->edisi->tahun_edisi : '' }}</p>
        </div>
    </div>
    <div class="divider-thick"></div>

    <!-- DATA IDENTITAS TIM -->
    <table class="table table-sm table-borderless mb-3" style="font-size: 0.85rem;">
        <tr>
            <td style="width: 15%; font-weight: bold;">NAMA TIM / KLUB</td>
            <td style="width: 35%;">: <strong>{{ strtoupper($activeTeam->nama_tim) }}</strong></td>
            <td style="width: 18%; font-weight: bold;">KATEGORI GENDER</td>
            <td style="width: 32%;">: <strong>{{ $activeTeam->kategori_gender == 'PA' ? 'PUTRA (PA)' : 'PUTRI (PI)' }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">PENGPROV ASAL</td>
            <td>: {{ $activeTeam->pengprov?->nama_pengprov ?? '-' }}</td>
            <td style="font-weight: bold;">POOL / GRUP</td>
            <td>: {{ $activeTeam->pool_grup ?: '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">JERSEY REGULER</td>
            <td>: J1: {{ $activeTeam->warna_jersey_1 ?: '-' }} / J2: {{ $activeTeam->warna_jersey_2 ?: '-' }}</td>
            <td style="font-weight: bold;">JERSEY LIBERO</td>
            <td>: {{ $activeTeam->warna_jersey_libero_1 ?: '-' }}</td>
        </tr>
    </table>

    <!-- DAFTAR ROSTER O-2 PEMAIN -->
    <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.9rem;">I. SUSUNAN PEMAIN (MAKSIMAL 14 PEMAIN)</h6>
    <table class="table table-o2 mb-4">
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th style="width: 60px;">JERSEY</th>
                <th>NAMA LENGKAP ATLET</th>
                <th style="width: 90px;">POSISI</th>
                <th style="width: 140px;">TEMPAT / TGL LAHIR</th>
                <th style="width: 80px;">TB / BB</th>
                <th style="width: 110px;">PERAN (C/L)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roster as $idx => $r)
                @php $p = $r->pemain; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold fs-6">#{{ $r->nomor_punggung }}</td>
                    <td>
                        <strong>{{ $p ? $p->nama_lengkap : 'Pemain #' . $r->nomor_punggung }}</strong>
                        @if($p && $p->nik && !str_starts_with($p->nik, '00000000'))
                            <div class="small text-muted" style="font-size: 0.72rem;">NIK: {{ $p->nik }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $r->posisi_bermain ?: ($p ? $p->posisi_utama : '-') }}</td>
                    <td class="text-center">
                        @if($p && $p->tanggal_lahir)
                            {{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') }}
                            <div style="font-size: 0.72rem;">{{ $p->tempat_lahir }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $r->tinggi_cm ? $r->tinggi_cm . 'cm' : '-' }}<br><span style="font-size: 0.75rem;">{{ $r->berat_kg ? $r->berat_kg . 'kg' : '-' }}</span></td>
                    <td class="text-center fw-bold">
                        @if($r->is_captain)
                            <span class="badge bg-warning text-dark border border-dark">KAPTEN (C)</span>
                        @elseif($r->is_libero)
                            <span class="badge bg-secondary text-white">LIBERO (L)</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-3">Tidak ada data pemain terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- DAFTAR OFFICIAL -->
    <h6 class="fw-bold text-uppercase mb-2" style="font-size: 0.9rem;">II. SUSUNAN OFFICIAL & PELATIH (BENCH)</h6>
    <table class="table table-o2 mb-4">
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th style="width: 220px;">JABATAN</th>
                <th>NAMA LENGKAP</th>
                <th style="width: 200px;">NO. LISENSI KEPELATIHAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($officials as $idx => $o)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $o->label_jabatan }}</strong></td>
                    <td>{{ $o->nama_lengkap }}</td>
                    <td>{{ $o->nomor_lisensi ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-2 text-muted">Belum ada official terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN -->
    <div class="row text-center signature-box">
        <div class="col-4">
            <div>Manajer / Official Tim,</div>
            <div class="sign-line"></div>
            <div><strong>( {{ strtoupper($user->name) }} )</strong></div>
            <small class="text-muted">Admin Kontingen</small>
        </div>
        <div class="col-4">
            <div>Mengetahui,<br>Pengprov PBVSI</div>
            <div class="sign-line"></div>
            <div><strong>( ......................................... )</strong></div>
            <small class="text-muted">Ketua Umum / Sekum</small>
        </div>
        <div class="col-4">
            <div>Diverifikasi Oleh,<br>Dewan Hakim / Panpel Pusat</div>
            <div class="sign-line"></div>
            <div><strong>( ......................................... )</strong></div>
            <small class="text-muted">Match Commissioner PBVSI</small>
        </div>
    </div>
</div>

</body>
</html>
