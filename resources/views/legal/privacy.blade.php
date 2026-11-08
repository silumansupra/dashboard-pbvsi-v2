@extends('layouts.app')

@section('title', 'Kebijakan Privasi - PBVSI Platform')

@section('content')
<!-- BREADCRUMB -->
<div class="breadcrumb-custom">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#!">Legalitas & Kepatuhan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kebijakan Privasi</li>
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
                <span class="badge bg-primary px-3 py-1 mb-2 fs-6">
                    <i class="fa-solid fa-shield-halved me-1"></i> Perlindungan Data Pribadi (UU PDP No. 27/2022)
                </span>
                <h2 class="fw-bold text-white mb-2">Kebijakan Privasi Sistem Informasi Terpadu PBVSI</h2>
                <p class="text-white-50 mb-0 lead fs-6">
                    Standar pengelolaan, kerahasiaan, dan perlindungan data pribadi bagi Atlet, Pelatih, Wasit, Official Kontingen, dan Pengurus PBVSI di seluruh Indonesia.
                </p>
                <div class="small text-white-50 mt-3">
                    <i class="fa-regular fa-clock me-1 text-warning"></i> Berlaku Efektif: <strong>1 Januari 2025</strong> &bull; Terakhir Diperbarui: <strong>{{ date('d F Y') }}</strong>
                </div>
            </div>
            <div class="col-md-auto text-md-end">
                <button onclick="window.print()" class="btn btn-outline-light btn-sm px-3 py-2 fw-semibold">
                    <i class="fa-solid fa-print me-1"></i> Cetak Kebijakan
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
                    <i class="fa-solid fa-list-ol text-primary me-2"></i>Daftar Bagian
                </h6>
            </div>
            <div class="list-group list-group-flush small">
                <a href="#pendahuluan" class="list-group-item list-group-item-action py-2">1. Pendahuluan & Komitmen</a>
                <a href="#data-dikumpulkan" class="list-group-item list-group-item-action py-2">2. Data yang Dikumpulkan</a>
                <a href="#atlet-anak" class="list-group-item list-group-item-action py-2">3. Ketentuan Atlet Usia Dini</a>
                <a href="#tujuan-penggunaan" class="list-group-item list-group-item-action py-2">4. Tujuan Pemrosesan Data</a>
                <a href="#keamanan-penyimpanan" class="list-group-item list-group-item-action py-2">5. Keamanan & Penyimpanan</a>
                <a href="#pembagian-data" class="list-group-item list-group-item-action py-2">6. Pembagian Data Pihak Ketiga</a>
                <a href="#hak-subjek" class="list-group-item list-group-item-action py-2">7. Hak Subjek Data</a>
                <a href="#kontak-pengaduan" class="list-group-item list-group-item-action py-2">8. Kontak & Pengaduan</a>
            </div>
        </div>
    </div>

    <!-- MAIN POLICY CONTENT -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <!-- 1. PENDAHULUAN -->
            <section id="pendahuluan" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">1.</span> Pendahuluan & Komitmen Organisasi
                </h4>
                <p class="text-muted leading-relaxed">
                    Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (<strong>PP PBVSI</strong>) sebagai otoritas tunggal cabang olahraga bola voli dan bola voli pantai di Indonesia yang berafiliasi dengan Federasi Bola Voli Internasional (<strong>FIVB</strong>), Konfederasi Bola Voli Asia (<strong>AVC</strong>), dan Komite Olahraga Nasional Indonesia (<strong>KONI</strong>), memiliki komitmen tertinggi dalam menjaga kerahasiaan, keutuhan, dan keamanan data pribadi seluruh pihak yang terdaftar di dalam platform sistem informasi terpadu ini.
                </p>
                <p class="text-muted leading-relaxed">
                    Kebijakan Privasi ini disusun berlandaskan ketentuan <strong>Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong> serta regulasi administrasi olahraga nasional. Dengan menggunakan layanan platform ini, setiap Pengprov, Pengkot/Pengkab, Klub, Atlet, Pelatih, Wasit, dan Official menyatakan memahami dan menyetujui pemrosesan data sebagaimana dijabarkan di bawah ini.
                </p>
            </section>

            <!-- 2. DATA YANG DIKUMPULKAN -->
            <section id="data-dikumpulkan" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">2.</span> Kategori Data Pribadi yang Dikumpulkan
                </h4>
                <p class="text-muted">Dalam rangka tata kelola kompetisi bola voli terpadu, sistem memproses beberapa kategori data berikut:</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-id-card text-primary me-2"></i>A. Identitas Pribadi Atlet & Anggota</h6>
                            <ul class="text-muted small ps-3 mb-0">
                                <li>Nama lengkap (sesuai Akta Kelahiran/KTP/Ijazah).</li>
                                <li>Nomor Induk Kependudukan (NIK) & Nomor Kartu Keluarga (KK).</li>
                                <li>Nomor Induk Siswa Nasional (NISN) bagi atlet pelajar.</li>
                                <li>Tempat dan tanggal lahir (penentu verifikasi kelompok umur).</li>
                                <li>Jenis kelamin (Putra / Putri).</li>
                                <li>Alamat domisili, provinsi, dan kota/kabupaten asal.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-file-lines text-info me-2"></i>B. Dokumen Verifikasi Keabsahan</h6>
                            <ul class="text-muted small ps-3 mb-0">
                                <li>Pindaian (scan) Akta Kelahiran resmi Disdukcapil.</li>
                                <li>Pindaian Ijazah asli atau Surat Keterangan Lulus (SKL).</li>
                                <li>Pindaian Kartu Keluarga (KK) terbaru.</li>
                                <li>Pindaian KTP / Kartu Identitas Anak (KIA) / Kartu Pelajar.</li>
                                <li>Pas foto resmi atlet berlatar belakang seragam tim.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-ruler-vertical text-success me-2"></i>C. Parameter Fisik & Teknis Olahraga</h6>
                            <ul class="text-muted small ps-3 mb-0">
                                <li>Tinggi badan (cm) dan berat badan (kg).</li>
                                <li>Jangkauan lompatan pukulan (<em>Spike Reach</em> cm).</li>
                                <li>Jangkauan bendungan (<em>Block Reach</em> cm).</li>
                                <li>Posisi bermain spesifik (Setter, Outside Hitter, Opposite, Middle Blocker, Libero).</li>
                                <li>Nomor punggung dan nama pada jersey pertandingan.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-award text-warning me-2"></i>D. Kompetensi & Lisensi Kepelatihan/Wasit</h6>
                            <ul class="text-muted small ps-3 mb-0">
                                <li>Nomor dan tingkatan lisensi kepelatihan (FIVB Level I/II/III, Nasional A/B/C).</li>
                                <li>Tingkatan lisensi wasit (FIVB International, Nasional A/B/C, Daerah).</li>
                                <li>Riwayat penataran resmi dan masa berlaku lisensi.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. KETENTUAN ATLET USIA DINI -->
            <section id="atlet-anak" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">3.</span> Ketentuan Khusus Atlet Usia Dini & Perlindungan Anak
                </h4>
                <div class="alert alert-info border-info d-flex align-items-start gap-3 p-3">
                    <i class="fa-solid fa-child-reaching fa-2x text-info mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Perlindungan Atlet di Bawah Umur (U-16, U-17, U-18)</h6>
                        <p class="small mb-0 leading-normal text-dark">
                            PBVSI menyadari sepenuhnya bahwa atlet usia dini memerlukan penanganan data yang khusus. Jika calon atlet belum memiliki e-KTP atau NIK pribadi, sistem mengizinkan penginputan sementara dengan verifikasi utama dialihkan pada <strong>Akta Kelahiran asli dan NISN/Ijazah Sekolah</strong>.
                        </p>
                    </div>
                </div>
                <p class="text-muted leading-relaxed">
                    Data atlet anak-anak tidak dipublikasikan secara komersial dan akses dokumen pribadi (Akta, KK, Ijazah) dibatasi secara ketat hanya dapat dilihat oleh Panitia Keabsahan PP PBVSI dan Match Commissioner resmi yang bertugas.
                </p>
            </section>

            <!-- 4. TUJUAN PENGGUNAAN -->
            <section id="tujuan-penggunaan" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">4.</span> Tujuan Pemrosesan & Penggunaan Data
                </h4>
                <p class="text-muted leading-relaxed">Data yang dihimpun dalam sistem platform ini semata-mata dimanfaatkan untuk keperluan:</p>
                <div class="row g-2">
                    <div class="col-12 d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span><strong>Verifikasi Keabsahan Turnamen:</strong> Menjamin keaslian identitas dan mencegah tindak kecurangan manipulasi usia (<em>age fraud</em>) pada Kejurnas Kelompok Umur.</span>
                    </div>
                    <div class="col-12 d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span><strong>Penerbitan Identitas Atlet PBVSI:</strong> Pembuatan PBVSI Player ID permanen yang terintegrasi sepanjang karir atlet dari tingkat junior hingga senior.</span>
                    </div>
                    <div class="col-12 d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span><strong>Pencetakan Formulir Resmi O-2:</strong> Registrasi susunan skuad resmi 10-14 pemain serta official untuk buku pertandingan dan delegasi teknis.</span>
                    </div>
                    <div class="col-12 d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span><strong>Pangkalan Data Prestasi Nasional (Database Scouting):</strong> Menyediakan data statistik bagi Badan Tim Nasional (BTN) PP PBVSI dalam memantau talenta atlet nasional.</span>
                    </div>
                </div>
            </section>

            <!-- 5. KEAMANAN & PENYIMPANAN -->
            <section id="keamanan-penyimpanan" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">5.</span> Keamanan & Infrastruktur Penyimpanan
                </h4>
                <p class="text-muted leading-relaxed">
                    PP PBVSI menerapkan standar keamanan teknis dan organisasi untuk melindungi data dari akses tanpa hak, pengubahan, pengungkapan, atau pemusnahan yang melanggar hukum:
                </p>
                <ul class="text-muted leading-relaxed ps-3">
                    <li><strong>Enkripsi Dokumen & Media:</strong> Berkas dokumen pendukung disimpan pada jaringan penyimpanan berbasis cloud <em>BunnyCDN Enterprise Storage</em> dengan token otorisasi aman.</li>
                    <li><strong>Enkripsi Sandi & Akses Akun:</strong> Seluruh kata sandi akun dienkripsi menggunakan algoritma cryptographic <code>bcrypt</code> dan proteksi transmisi SSL/TLS (HTTPS).</li>
                    <li><strong>Role-Based Access Control (RBAC):</strong> Hak akses data dipisahkan secara ketat antara Superadmin PP PBVSI, Administrator Pengprov, Dewan Hakim, dan Administrator Tim Peserta.</li>
                    <li><strong>Enkripsi Pengidentifikasi Publik:</strong> ID pada URL diproteksi menggunakan token terenkripsi konteks guna mencegah pencurian data massal (<em>scraping</em>).</li>
                </ul>
            </section>

            <!-- 6. PEMBAGIAN DATA -->
            <section id="pembagian-data" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">6.</span> Pembagian Data kepada Pihak Ketiga
                </h4>
                <div class="alert alert-warning small border-warning">
                    <i class="fa-solid fa-lock me-1 fw-bold text-dark"></i>
                    <strong>Prinsip Kerahasiaan Mutlak:</strong> PP PBVSI <strong>TIDAK PERNAH</strong> menjual, menyewakan, atau memperdagangkan data pribadi atlet, pelatih, wasit, maupun official kepada pihak komersial pihak ketiga mana pun.
                </div>
                <p class="text-muted leading-relaxed">
                    Pengungkapan data hanya dimungkinkan terbatas kepada:
                </p>
                <ol class="text-muted small ps-3">
                    <li>Kementerian Pemuda dan Olahraga Republik Indonesia (Kemenpora RI) dan Komite Olahraga Nasional Indonesia (KONI) untuk pendataan atlet nasional dan laporan pembinaan olahraga.</li>
                    <li>Federasi Internasional (FIVB / AVC) khusus bagi atlet atau pelatih yang didaftarkan pada kejuaraan resmi internasional resmi PBVSI.</li>
                    <li>Aparat penegak hukum yang berwenang apabila terdapat instruksi penetapan hukum resmi sesuai peraturan perundang-undangan Republik Indonesia.</li>
                </ol>
            </section>

            <!-- 7. HAK SUBJEK DATA -->
            <section id="hak-subjek" class="mb-5">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">7.</span> Hak Subjek Data Pribadi
                </h4>
                <p class="text-muted leading-relaxed">
                    Sesuai amanat UU PDP No. 27/2022, setiap pemilik data pribadi berhak untuk:
                </p>
                <ul class="text-muted ps-3 small leading-relaxed">
                    <li>Mengakses dan memperoleh salinan data pribadi yang tersimpan dalam sistem.</li>
                    <li>Memperbarui atau memperbaiki data pribadi yang tidak akurat, keliru, atau sudah kedaluwarsa melalui Administrator Tim atau Pengprov terkait.</li>
                    <li>Mengajukan sanggahan atau klarifikasi resmi terhadap keputusan verifikasi keabsahan melalui prosedur Dewan Hakim PBVSI.</li>
                </ul>
            </section>

            <!-- 8. KONTAK RESMI -->
            <section id="kontak-pengaduan">
                <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <span class="text-primary me-1">8.</span> Narahubung Resmi Pelindungan Data PP PBVSI
                </h4>
                <p class="text-muted leading-relaxed">
                    Apabila Anda memiliki pertanyaan, keberatan, atau ingin mengajukan permohonan terkait privasi data pribadi Anda pada sistem PBVSI Platform, silakan menghubungi:
                </p>
                <div class="card border bg-light">
                    <div class="card-body p-3">
                        <div class="fw-bold text-dark mb-1">PENGURUS PUSAT PERSATUAN BOLA VOLI SELURUH INDONESIA (PP PBVSI)</div>
                        <div class="small text-muted mb-1"><i class="fa-solid fa-location-dot me-2 text-danger"></i>Kantor Sekretariat: Gedung Graha Pemuda Senayan / Padepokan Voli Jenderal Polisi Kunarto, Sentul, Bogor, Jawa Barat.</div>
                        <div class="small text-muted mb-1"><i class="fa-solid fa-envelope me-2 text-primary"></i>Email Resmi: <a href="mailto:sekretariat@pbvsi.or.id" class="text-primary text-decoration-none">sekretariat@pbvsi.or.id</a></div>
                        <div class="small text-muted"><i class="fa-solid fa-globe me-2 text-success"></i>Website Resmi: <a href="https://pbvsi.or.id" target="_blank" class="text-success text-decoration-none">https://pbvsi.or.id</a></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
