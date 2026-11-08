@extends('layouts.app')

@section('title', 'Syarat & Ketentuan - PBVSI Platform')

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#!">Legalitas & Kepatuhan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Syarat & Ketentuan</li>
        </ol>
    </nav>
</div>

<!-- HEADER HERO -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="card-body p-4 p-md-5 text-white position-relative">
        <div class="row align-items-center g-4">
            <div class="col-md-auto text-center text-md-start">
                <div class="rounded-circle bg-white p-3 d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 88px; height: 88px;">
                    <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" alt="PBVSI" height="56">
                </div>
            </div>
            <div class="col">
                <span class="badge bg-info text-dark px-3 py-1 mb-2 fs-6">
                    <i class="fa-solid fa-file-contract me-1"></i> Regulasi Resmi PBVSI & FIVB
                </span>
                <h2 class="fw-bold text-white mb-2">Syarat & Ketentuan Platform Terpadu PBVSI</h2>
                <p class="text-white-50 mb-0 lead fs-6">
                    Ketentuan hukum, integritas pendaftaran atlet, regulasi susunan roster O-2, lisensi kepelatihan, dan sanksi disiplin kompetisi bola voli nasional.
                </p>
                <div class="small text-white-50 mt-3">
                    <i class="fa-regular fa-clock me-1 text-warning"></i> Berlaku Efektif: <strong>1 Januari 2025</strong> &bull; Terakhir Diperbarui: <strong>{{ date('d F Y') }}</strong>
                </div>
            </div>
            <div class="col-md-auto text-md-end">
                <button onclick="window.print()" class="btn btn-outline-light btn-sm px-3 py-2 fw-semibold">
                    <i class="fa-solid fa-print me-1"></i> Cetak Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- TABLE OF CONTENTS (SIDEBAR) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card border-0 shadow-sm sticky-top" style="top: 80px; z-index: 10;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-list-ol text-info me-2"></i>Daftar Pasal Regulasi
                </h6>
            </div>
            <div class="list-group list-group-flush small">
                <a href="#ketentuan-umum" class="list-group-item list-group-item-action py-2">Pasal 1. Ketentuan Umum</a>
                <a href="#keabsahan-data" class="list-group-item list-group-item-action py-2">Pasal 2. Keabsahan & Berkas</a>
                <a href="#larangan-pencurian-umur" class="list-group-item list-group-item-action py-2">Pasal 3. Larangan Age Fraud & Sanksi</a>
                <a href="#regulasi-roster-o2" class="list-group-item list-group-item-action py-2">Pasal 4. Regulasi Roster O-2</a>
                <a href="#lisensi-official" class="list-group-item list-group-item-action py-2">Pasal 5. Lisensi Pelatih & Official</a>
                <a href="#tanggung-jawab-akun" class="list-group-item list-group-item-action py-2">Pasal 6. Tanggung Jawab Akun</a>
                <a href="#wewenang-dewan-hakim" class="list-group-item list-group-item-action py-2">Pasal 7. Wewenang Dewan Hakim</a>
                <a href="#hukum-yurisdiksi" class="list-group-item list-group-item-action py-2">Pasal 8. Hukum & Yurisdiksi</a>
            </div>
        </div>
    </div>

    <!-- MAIN TERMS CONTENT -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <!-- PASAL 1: KETENTUAN UMUM -->
            <section id="ketentuan-umum" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 1.</span> Ketentuan Umum & Ruang Lingkup
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Syarat dan Ketentuan ini mengatur hak, kewajiban, tata tertib, dan sanksi bagi seluruh pengguna Sistem Informasi dan Database Terpadu Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (<strong>PP PBVSI</strong>), yang meliputi Pengurus Provinsi (Pengprov), Pengurus Kota/Kabupaten (Pengkot/Pengkab), Pengurus Klub, Manajer Tim, Administrator Kontingen, Pelatih, Wasit, dan Atlet di seluruh wilayah Negara Kesatuan Republik Indonesia.
                </p>
                <p class="text-muted leading-relaxed">
                    2. Pendaftaran partisipasi pada setiap kejuaraan resmi yang diselenggarakan atau direkomendasikan oleh PBVSI (seperti Kejuaraan Nasional Kelompok Umur U-16, U-17, U-18, U-19, Livoli Divisi Utama, Livoli Divisi 1, Piala Kapolri, dan Sirkuit Nasional Bola Voli Pantai) wajib dilakukan melalui platform ini dengan tunduk pada Petunjuk Pelaksanaan (Juklak) dan Petunjuk Teknis (Juknis) kejuaraan yang berlaku.
                </p>
            </section>

            <!-- PASAL 2: KEABSAHAN DATA & BERKAS -->
            <section id="keabsahan-data" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 2.</span> Integritas & Keabsahan Dokumen Pendaftaran
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Setiap administrator tim dan pengurus wajib mengunggah dokumen digital yang bersumber langsung dari dokumen asli yang sah, berkekuatan hukum, dan tidak direkayasa (Akta Kelahiran, Ijazah Asli / SKL, Kartu Keluarga, dan Pas Foto resmi).
                </p>
                <p class="text-muted leading-relaxed">
                    2. Verifikasi dokumen dilakukan secara bertahap:
                </p>
                <ul class="text-muted small ps-3 leading-relaxed">
                    <li><strong>Verifikasi Dokumen Awal (Sistem):</strong> Pengecekan kelengkapan data administratif dan foto atlet secara daring.</li>
                    <li><strong>Screening / Uji Keabsahan Fisik:</strong> Pemeriksaan berkas fisik asli oleh Panitia Keabsahan PP PBVSI menjelang <em>Technical Meeting</em> kejuaraan di kota penyelenggara.</li>
                </ul>
            </section>

            <!-- PASAL 3: LARANGAN PENCURIAN UMUR & SANKSI -->
            <section id="larangan-pencurian-umur" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2 text-danger">
                    <span class="text-danger me-1">Pasal 3.</span> Larangan Manipulasi Usia (Age Fraud) & Sanksi Disiplin
                </h4>
                <div class="alert alert-danger border-danger p-3 mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation fa-lg mt-1 text-danger"></i>
                        <div>
                            <strong class="d-block mb-1">NOL TOLERANSI TERHADAP PENCURIAN UMUR:</strong>
                            <span class="small">Manipulasi tanggal lahir atau penggunaan identitas palsu merusak integritas pembinaan usia muda, menghancurkan masa depan atlet, dan merupakan tindak pidana pemalsuan surat sesuai Pasal 263 Kitab Undang-Undang Hukum Pidana (KUHP).</span>
                        </div>
                    </div>
                </div>

                <p class="text-muted leading-relaxed">
                    Apabila ditemukan bukti otentik adanya pemalsuan berkas atau manipulasi usia atlet pada Kejuaraan Nasional, PP PBVSI berhak menjatuhkan sanksi administratif dan disiplin berupa:
                </p>
                <ol class="text-muted ps-3 small leading-relaxed">
                    <li><strong>Diskualifikasi Tim:</strong> Pembatalan kepesertaan dan/atau pencabutan gelar juara tim kontingen bersangkutan serta penyerahan medali/trofi kepada tim urutan berikutnya.</li>
                    <li><strong>Skorsing Atlet:</strong> Larangan berpartisipasi dalam seluruh kegiatan bola voli resmi nasional selama minimal 2 (dua) tahun hingga seumur hidup.</li>
                    <li><strong>Sanksi Manajer & Pelatih:</strong> Pencabutan lisensi kepelatihan dan larangan mendampingi tim bagi pelatih dan manajer yang terbukti mengetahui atau merekayasa dokumen.</li>
                    <li><strong>Tindakan Pidana:</strong> PP PBVSI berhak melimpahkan bukti-bukti pemalsuan kepada Kepolisian Negara Republik Indonesia untuk proses hukum pidana.</li>
                </ol>
            </section>

            <!-- PASAL 4: REGULASI ROSTER FORMULIR O-2 -->
            <section id="regulasi-roster-o2" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 4.</span> Ketentuan Susunan Skuad Roster O-2 & Pemain Libero
                </h4>
                <p class="text-muted leading-relaxed">
                    Sesuai dengan Regulasi Resmi FIVB dan Peraturan Pertandingan PP PBVSI, susunan pemain dalam lembar Form O-2 wajib memenuhi kaidah berikut:
                </p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 200px;">Elemen Roster</th>
                                <th>Ketetapan Regulasi Resmi PBVSI / FIVB</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Kuota Jumlah Pemain</td>
                                <td>Setiap tim wajib mendaftarkan minimal <strong>10 pemain</strong> dan maksimal <strong>14 pemain</strong> aktif.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kapten Tim (C)</td>
                                <td>Wajib menetapkan <strong>1 (satu) orang Kapten Tim</strong> yang bertanda (C) pada lembar O-2 dan memiliki garis di bawah nomor jersey.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Pemain Libero (L)</td>
                                <td>Tim berhak mendaftarkan <strong>maksimal 2 (dua) Libero</strong> pada daftar roster O-2.</td>
                            </tr>
                            <tr class="table-warning">
                                <td class="fw-bold text-danger">Larangan Rangkap Kapten & Libero</td>
                                <td class="text-danger fw-semibold">
                                    <i class="fa-solid fa-ban me-1"></i> Sesuai Regulasi FIVB Bab 5.1 & PBVSI: <u>KAPTEN TIM TIDAK BOLEH MERANGKAP SEBAGAI LIBERO</u>. Sistem akan menolak penugasan ini secara otomatis.
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Warna Jersey Pertandingan</td>
                                <td>Tim wajib menyiapkan minimal 2 set jersey kontras untuk pemain reguler dan jersey khusus dengan warna kontras untuk pemain Libero.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- PASAL 5: LISENSI PELATIH & OFFICIAL -->
            <section id="lisensi-official" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 5.</span> Standar Lisensi Pelatih & Official Pendukung
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Pelatih Kepala (<em>Head Coach</em>) dan Asisten Pelatih yang didaftarkan wajib memiliki lisensi kepelatihan resmi PBVSI / FIVB yang masih berlaku sesuai tingkatan kompetisi:
                </p>
                <ul class="text-muted small ps-3">
                    <li>Kejurnas Antar Klub / Kelompok Umur: Minimal Lisensi C Nasional / Lisensi Daerah Pengprov yang direkomendasikan.</li>
                    <li>Livoli Divisi I dan Divisi Utama: Minimal Lisensi B Nasional atau Lisensi FIVB Level 1.</li>
                </ul>
                <p class="text-muted leading-relaxed">
                    2. Jumlah official yang berhak duduk di bangku cadangan (<em>bench</em>) pertandingan dibatasi maksimal 5 hingga 7 orang yang mencakup Pelatih Kepala, Asisten Pelatih 1, Asisten Pelatih 2, Manajer, Terapis, dan Dokter Tim.
                </p>
            </section>

            <!-- PASAL 6: TANGGUNG JAWAB AKUN -->
            <section id="tanggung-jawab-akun" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 6.</span> Hak Akses & Tanggung Jawab Akun Pengguna
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Kredensial akun (Username berawalan <code>#adm...</code> dan kata sandi) merupakan identitas digital resmi perwakilan kontingen/klub yang bersifat rahasia.
                </p>
                <p class="text-muted leading-relaxed">
                    2. Administrator tim bertanggung jawab penuh atas segala aktivitas, penambahan pemain, permohonan lisensi, dan perubahan data yang dilakukan menggunakan akun miliknya.
                </p>
                <p class="text-muted leading-relaxed">
                    3. Pengguna dilarang memindahtangankan, menjual, atau memberikan hak akses login kepada pihak luar yang tidak memiliki surat tugas sah dari pengurus klub atau pengprov bersangkutan.
                </p>
            </section>

            <!-- PASAL 7: WEWENANG DEWAN HAKIM -->
            <section id="wewenang-dewan-hakim" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 7.</span> Wewenang Mutlak Dewan Hakim & Match Commissioner
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Dewan Hakim Kejuaraan dan Delegasi Teknis (<em>Technical Delegate / Match Commissioner</em>) yang ditunjuk oleh PP PBVSI memiliki wewenang mutlak dalam memverifikasi keabsahan fisik, meminta klarifikasi atlet, dan memutuskan kelayakan bertanding setiap atlet yang didaftarkan.
                </p>
                <p class="text-muted leading-relaxed">
                    2. Keputusan Dewan Hakim mengenai status keabsahan pemain dalam sidang keabsahan sebelum dimulainya kejuaraan bersifat <strong>final dan mengikat</strong> untuk turnamen bersangkutan.
                </p>
            </section>

            <!-- PASAL 8: HUKUM & YURISDIKSI -->
            <section id="hukum-yurisdiksi">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-info me-1">Pasal 8.</span> Hukum yang Berlaku & Ketentuan Penutup
                </h4>
                <p class="text-muted leading-relaxed">
                    1. Syarat dan Ketentuan ini tunduk pada hukum positif yang berlaku di Negara Kesatuan Republik Indonesia, Anggaran Dasar/Anggaran Rumah Tangga (AD/ART) PP PBVSI, dan Statuta FIVB.
                </p>
                <p class="text-muted leading-relaxed">
                    2. PP PBVSI berhak memperbarui atau merevisi ketentuan ini sewaktu-waktu sesuai perkembangan regulasi keolahragaan nasional dan internasional. Setiap perubahan akan diumumkan secara transparan melalui portal resmi ini.
                </p>
                <div class="mt-4 pt-3 border-top text-center text-muted small">
                    Ditetapkan di Jakarta &bull; Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (PP PBVSI)
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
