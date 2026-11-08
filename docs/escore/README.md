# Knowledge Base & Cetak Biru: Modul e-Score PBVSI (Reproduksi AVC e-Score)

> [!NOTE]
> Dokumen ini adalah dokumentasi teknis dan cetak biru (*blueprint*) hasil reverse engineering dari software **AVC e-Score (SportsStreamLab)** untuk direproduksi ke dalam ekosistem **dashboard-pbvsi-v2** (Laravel 11/12 + Tailwind CSS v4 + Alpine.js).
>
> Simpanan ini siap dieksekusi kapan saja saat penyesuaian platform utama PBVSI telah selesai.

---

## 1. Daftar Dokumen Spesifikasi

1. **[DATABASE_SCHEMA.md](file:///D:/laragon/www/dashboard-pbvsi-v2/docs/escore/DATABASE_SCHEMA.md)**
   - Rencana skema tabel migration baru (`trn_jadwal_pertandingan`, `trn_escore_session`, `trn_escore_set`, `trn_escore_action_log`).
   - Relasi ke model master data eksisting ([`Turnamen`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Turnamen.php), [`Edisi`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Edisi.php), [`PesertaTim`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/PesertaTim.php), [`Roster`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Roster.php), [`Wasit`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Wasit.php)).

2. **[SCORING_ENGINE_SPEC.md](file:///D:/laragon/www/dashboard-pbvsi-v2/docs/escore/SCORING_ENGINE_SPEC.md)**
   - Spesifikasi algoritma lapangan dan aturan resmi pertandingan **bolavoli**.
   - State machine Alpine.js (`volleyballScoreEngine`): rotasi 6 posisi (I–VI), validasi libero baris belakang, kuota 6 pergantian pemain berpasangan, timer time-out 30s & interval set 3m, modal video challenge, dan sistem audit undo/redo.

3. **[BROADCAST_AND_PDF_SPEC.md](file:///D:/laragon/www/dashboard-pbvsi-v2/docs/escore/BROADCAST_AND_PDF_SPEC.md)**
   - Spesifikasi halaman live graphic overlay transparan untuk siaran langsung (OBS Studio / vMix).
   - Format pembuatan lembar skor resmi PDF A4 standar internasional (FIVB / AVC) serta formulir pelengkap (O-4 Match Report, R-3 Daily Duty, R-5 Line-Up).

---

## 2. Peta Komparasi Fitur Inti (AVC e-Score vs PBVSI v2)

| Fitur | AVC e-Score (.NET WinForms) | Solusi PBVSI v2 (Laravel + Alpine) |
| :--- | :--- | :--- |
| **Input Data Tim** | Manual / Import Excel berkali-kali | Otomatis dari `trn_peserta_tim` & `trn_roster` |
| **Target Perangkat** | Desktop Windows saja | Tablet Touchscreen (iPad/Android), Laptop, PC |
| **Rotasi Lapangan** | Logika C# `Rotate()` | Reaktif di frontend via Alpine.js (0 ms latency) |
| **Tanda Tangan** | Form dialog mouse | HTML5 Canvas Signature Pad (dukungan stylus/touch) |
| **Penyimpanan Data**| File proprietary `.match` | Database MySQL terpusat + Snapshot JSON reli |
| **Live Broadcast** | Nancy self-host HTTP port 8887 | Route Blade `/escore/overlay/{id}` untuk OBS/vMix |
| **Cetak Skor Resmi**| PdfSharp library | Template Blade to PDF resmi A4 FIVB/AVC |

---

## 3. Langkah Cepat Saat Siap Mengimplementasikan

Ketika Anda siap mengeksekusi fitur ini:
1. Jalankan pembuatan migration:
   ```bash
   php artisan make:migration create_escore_match_tables
   ```
   *(Salin skema dari [DATABASE_SCHEMA.md](file:///D:/laragon/www/dashboard-pbvsi-v2/docs/escore/DATABASE_SCHEMA.md))*
2. Buat Model Eloquent:
   ```bash
   php artisan make:model JadwalPertandingan
   php artisan make:model EscoreSession
   php artisan make:model EscoreSet
   php artisan make:model EscoreActionLog
   ```
3. Buat Controller:
   ```bash
   php artisan make:controller EscoreController
   ```
4. Pasang view operator scoring di `resources/views/escore/operator.blade.php` dengan template dari [SCORING_ENGINE_SPEC.md](file:///D:/laragon/www/dashboard-pbvsi-v2/docs/escore/SCORING_ENGINE_SPEC.md).
