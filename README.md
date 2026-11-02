# PBVSI Integrated Platform & Dashboard Kejurnas (v2)

Platform Terpadu Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (PP PBVSI) yang menyatukan seluruh pengelolaan master data organisasi dan ekosistem kejuaraan nasional (Kejurnas U-16, U-17, U-18, U-19, Livoli, dan Voli Pantai) ke dalam satu panel berbasis **Laravel 12**, **Bootstrap 5 (AdminKit)**, dan **Alpine.js**.

---

## 🌟 Fitur Utama

1. **Pusat Kendali PBVSI (Central Platform)**:
   * **Master Pemain Nasional (PBVSI Player ID)**: Bank data atlet seumur hidup berbasis NIK & NISN.
   * **Organisasi**: Pengurus Provinsi (37 Pengprov), Pengurus Kota/Kabupaten (514 Pengkot/Pengkab), dan Klub Resmi.
   * **Perwasitan & Kepelatihan**: Lisensi, riwayat penataran/kursus, dan sertifikat ber-QR Code.
   * **Single Sign-On (SSO)**: Satu akun untuk seluruh modul dan turnamen tanpa duplikasi user.

2. **Panel Terpadu Kejurnas Kelompok Umur (U-16, U-17, U-18, U-19)**:
   * **Hierarki URL Dinamis**: `/kejurnas/{kelompok_umur}/{tahun}` (contoh: `/kejurnas/u18/2026`, `/kejurnas/u19/2026`).
   * **Validasi Batas Usia Otomatis**: Memastikan pemain memenuhi kualifikasi tanggal lahir per kelompok umur.
   * **Manajemen Tim & Roster**: Registrasi pemain putra & putri, nomor punggung, posisi, tinggi, dan berat badan.
   * **Formulir O-2 Resmi (FIVB / VIS)**: Kompatibel dengan template Excel O-2 Tim dan Player untuk Technical Meeting.
   * **Jadwal & Hasil Pertandingan**: Match schedule, venue, dan penugasan wasit nasional.

3. **Modul Siap Skala (Scalable Domains)**:
   * **Kursus & Penataran Wasit/Pelatih**
   * **Sertifikasi Nasional Ber-QR Code**
   * **Tiket Pertandingan Online (Payment Gateway Midtrans)**

---

## 🚀 Petunjuk Instalasi & Deploy ke Production

### 1. Clone & Setup Environment
```bash
git clone https://github.com/silumansupra/dashboard-pbvsi-v2.git
cd dashboard-pbvsi-v2
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

### 2. Konfigurasi Database di `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_pbvsi_v2
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data Master Otomatis
Cukup jalankan satu perintah untuk membuat seluruh tabel skema v2 dan mengisi master wilayah, kategori turnamen, serta role sistem:
```bash
php artisan migrate --seed
```

Perintah di atas akan mengeksekusi:
* Migrasi tabel Core, Organisasi, Keanggotaan, Turnamen, Kursus, Sertifikasi, dan Ticketing.
* `RolesAndPermissionsSeeder` (8 Role Utama: Superadmin, Admin Kejurnas, Pengprov, Pengkot, Klub, Wasit, Pelatih, Petugas Tiket).
* `MasterWilayahSeeder` (37 Provinsi Resmi PBVSI).
* `MasterTurnamenSeeder` (Kejurnas U-16, U-17, U-18, U-19, Livoli Divisi 1 & Utama, Voli Pantai, Kapolri Cup, beserta Edisi aktif).

---

## 🗺️ Knowledge Graph (Graphify)

Project ini dilengkapi dengan **Graphify Knowledge Graph** di dalam direktori `graphify-out/` agar tim pengembang dapat langsung memahami arsitektur, relasi modul, dan ketergantungan kode:

* **Visualisasi Interaktif**: Buka file `graphify-out/graph.html` di browser untuk melihat peta interaktif modul.
* **Laporan Arsitektur**: Baca `graphify-out/GRAPH_REPORT.md` untuk ringkasan komponen hub (*god nodes*) dan komunitas kode.
* **Graph Data**: File `graphify-out/graph.json` dapat digunakan untuk GraphRAG atau sinkronisasi tim.

Untuk memperbarui graph jika ada penambahan kode baru:
```bash
python -m graphify update .
```

---

## 📁 Struktur Direktori Utama

```text
dashboard-pbvsi-v2/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php   # Controller Pusat PBVSI
│   │   └── KejurnasController.php    # Controller Sub-Dashboard Kejurnas Umur
│   └── Models/
│       ├── User.php, Role.php, UserProfile.php
│       ├── Provinsi.php, Pengprov.php, Klub.php
│       ├── Pemain.php, Wasit.php
│       └── Turnamen.php, Edisi.php, PesertaTim.php, Roster.php
├── database/
│   ├── migrations/                   # 7 file migrasi database skema v2
│   └── seeders/                      # Seeder otomatis untuk deploy production
├── graphify-out/                     # Knowledge graph arsitektur sistem
├── public/assets/                    # Asset template AdminKit (Bootstrap 5)
├── resources/views/
│   ├── layouts/app.blade.php         # Layout universal AdminKit + Alpine.js
│   ├── dashboard/index.blade.php     # Tampilan Utama Pusat PBVSI
│   └── kejurnas/panel.blade.php      # Panel Sub-Dashboard Kejurnas Umur
└── routes/web.php                    # Route hierarki /kejurnas/{kelompok_umur}/{tahun}
```
