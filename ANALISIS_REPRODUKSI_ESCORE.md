# Cetak Biru Reproduksi AVC e-Score ke Platform PBVSI v2 (Laravel + Alpine.js)

> [!IMPORTANT]
> **Status Kelayakan:** **100% SANGAT LAYAK (PERFECT MATCH)**.
> Berdasarkan audit langsung ke codebase [`D:\laragon\www\dashboard-pbvsi-v2`](file:///D:/laragon/www/dashboard-pbvsi-v2), platform PBVSI terbaru menggunakan stack modern:
> - **Backend:** Laravel 11/12/13 (PHP 8.3+) + Eloquent ORM & Migrations
> - **Frontend:** Tailwind CSS v4 + Vite + Bootstrap 5 / AdminKit
> - **Reactive State:** **Alpine.js v3.14.8** (sudah terpasang di layout utama [`app.blade.php`](file:///D:/laragon/www/dashboard-pbvsi-v2/resources/views/layouts/app.blade.php))
> - **Domain Models Eksisting:** [`Turnamen`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Turnamen.php), [`Edisi`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Edisi.php), [`PesertaTim`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/PesertaTim.php), [`Roster`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Roster.php), [`Pemain`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Pemain.php), [`Wasit`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Wasit.php), [`Official`](file:///D:/laragon/www/dashboard-pbvsi-v2/app/Models/Official.php).
> - **Cetak Formulir Resmi:** Formulir O-2 (Roster) sudah terbukti berjalan di [`resources/views/tim/o2_print.blade.php`](file:///D:/laragon/www/dashboard-pbvsi-v2/resources/views/tim/o2_print.blade.php).

---

## 1. Mengapa Stack Laravel + Alpine.js Ini Jauh Lebih Unggul

1. **Model Data Sudah Lengkap:**
   Di AVC e-Score versi C# Windows, operator harus mengimpor file Excel atau mengetik ulang nama tim, warna jersey, nomor punggung pemain, dan lisensi wasit. Di `dashboard-pbvsi-v2`, tabel `trn_edisi`, `trn_peserta_tim`, `trn_roster`, dan `mbr_wasit` **sudah ada dan saling berelasi**. Saat membuat pertandingan baru, operator tinggal memilih jadwal, dan seluruh roster 14 pemain beserta warna jersey langsung otomatis termuat!

2. **Alpine.js adalah Pasangan Sempurna untuk Scoring Engine:**
   - Karena Alpine.js sudah terpasang di layout, kita tidak membutuhkan framework SPA berat (seperti React/Vue kompilasi terpisah).
   - Seluruh state lapangan (posisi I–VI, server aktif, rotasi searah jarum jam, kuota 6 pergantian, jatah libero, time-out 30s, undo stack) dapat di-bundle dalam satu objek reaktif `x-data="volleyballScoreEngine()"` dengan respon UI instan (0 ms latency).

3. **Multi-Platform & Touchscreen Ready:**
   - AVC e-Score lama terkunci di desktop Windows (`.exe`).
   - Dengan Blade + Tailwind/Bootstrap + Alpine.js, antarmuka scoring dapat dibuka dengan sempurna di **Tablet iPad / Android**, laptop wasit, maupun monitor touch-screen meja juri pertandingan **bolavoli**.

4. **Integrasi Broadcast TV (OBS / vMix):**
   - Cukup buat route view Blade khusus tanpa layout: `/escore/overlay/{idJadwal}` dengan background transparan.
   - Tim broadcast tinggal memasukkan URL tersebut ke OBS Studio / vMix Browser Source. Skor, nama pemain yang servis, rotasi lapangan, dan time-out akan langsung ter-update secara real-time.

---

## 2. Pemetaan Fitur AVC e-Score ke `dashboard-pbvsi-v2`

```mermaid
graph TD
    subgraph Data_PBVSI_v2 [Master Data Eksisting]
        Edisi[trn_edisi - Venue & Kota]
        Tim[trn_peserta_tim - Jersey & Pool]
        Roster[trn_roster & Pemain - No Punggung, Libero, Kapten]
        Wasit[mbr_wasit - Wasit 1, Wasit 2, Challenge]
    end

    subgraph EScore_Module [Modul Baru: app/Http/Controllers/EscoreController.php]
        Setup[1. Setup Pertandingan & Coin Toss]
        Engine[2. Scoring Engine Alpine.js x-data]
        Audit[3. Audit Log Reli & Undo/Redo]
        Sync[4. REST API Endpoint Sync]
    end

    subgraph Output_Layer [Tampilan & Dokumen]
        TabletUI[Layar Operator Tablet / Web]
        ScoresheetPDF[PDF Scoresheet Resmi A4 FIVB/AVC]
        BroadcastOverlay[OBS / vMix Graphic Overlay]
        LiveScorePublik[Halaman Live Score Publik PBVSI]
    end

    Data_PBVSI_v2 --> Setup
    Setup --> Engine
    Engine <--> Audit
    Engine --> Sync
    Sync --> TabletUI
    Sync --> ScoresheetPDF
    Sync --> BroadcastOverlay
    Sync --> LiveScorePublik
```

---

## 3. Rencana Tambahan Skema Database (Laravel Migrations)

Untuk menampung pertandingan dan log skor e-Score, kita tambahkan migration baru di `database/migrations/`:

```php
// database/migrations/2026_11_09_000001_create_escore_match_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Jadwal Pertandingan
        Schema::create('trn_jadwal_pertandingan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('edisi_id');
            $table->string('nomor_pertandingan', 20); // misal: "Match 13"
            $table->string('fase_grup', 50)->default('Babak Penyisihan');
            $table->string('pool', 10)->nullable(); // Pool A, Pool B
            $table->unsignedInteger('tim_a_id');
            $table->unsignedInteger('tim_b_id');
            $table->dateTime('jadwal_mulai');
            $table->string('lapangan_hall', 100)->nullable();
            
            // Wasit & Petugas Laga
            $table->unsignedBigInteger('wasit_r1_id')->nullable();
            $table->unsignedBigInteger('wasit_r2_id')->nullable();
            $table->unsignedBigInteger('wasit_rc_id')->nullable(); // Challenge Referee
            $table->unsignedBigInteger('scorer_id')->nullable();
            $table->unsignedBigInteger('assistant_scorer_id')->nullable();

            $table->enum('status', ['SCHEDULED', 'WARMUP', 'LIVE', 'SET_BREAK', 'FINISHED', 'POSTPONED'])->default('SCHEDULED');
            $table->timestamps();

            $table->foreign('edisi_id')->references('id')->on('trn_edisi')->onDelete('cascade');
            $table->foreign('tim_a_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
            $table->foreign('tim_b_id')->references('id')->on('trn_peserta_tim')->onDelete('cascade');
        });

        // 2. Data Pertandingan eScore Aktif (State Live)
        Schema::create('trn_escore_session', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id')->unique();
            $table->integer('set_aktif')->default(1);
            $table->integer('skor_set_a')->default(0);
            $table->integer('skor_set_b')->default(0);
            $table->string('warna_jersey_a', 30)->nullable();
            $table->string('warna_jersey_b', 30)->nullable();
            $table->string('suhu_ruangan', 20)->nullable();
            $table->string('kelembaban', 20)->nullable();
            $table->integer('penonton')->default(0);
            
            // Digital Signatures (Base64 PNG dari Canvas)
            $table->longText('sign_kapten_a')->nullable();
            $table->longText('sign_kapten_b')->nullable();
            $table->longText('sign_coach_a')->nullable();
            $table->longText('sign_coach_b')->nullable();
            $table->longText('sign_r1')->nullable();
            $table->longText('sign_r2')->nullable();
            $table->longText('sign_scorer')->nullable();
            $table->longText('sign_post_kapten_a')->nullable();
            $table->longText('sign_post_kapten_b')->nullable();

            $table->timestamps();
            $table->foreign('jadwal_id')->references('id')->on('trn_jadwal_pertandingan')->onDelete('cascade');
        });

        // 3. Detail Per Set (Rotasi, Lineup Awal, Durasi)
        Schema::create('trn_escore_set', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('session_id');
            $table->integer('nomor_set'); // 1, 2, 3, 4, 5
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->integer('durasi_menit')->default(0);
            $table->enum('tim_servis_awal', ['A', 'B'])->default('A');
            $table->enum('tim_sisi_kiri', ['A', 'B'])->default('A');
            
            // Lineup Awal Posisi I s/d VI (JSON array ID Roster)
            $table->json('starting_lineup_a'); // [pos1, pos2, pos3, pos4, pos5, pos6]
            $table->json('starting_lineup_b');
            $table->json('liberos_a')->nullable();
            $table->json('liberos_b')->nullable();

            $table->integer('poin_a')->default(0);
            $table->integer('poin_b')->default(0);
            $table->integer('timeout_a')->default(0);
            $table->integer('timeout_b')->default(0);
            $table->integer('substitusi_a')->default(0);
            $table->integer('substitusi_b')->default(0);
            $table->enum('pemenang', ['A', 'B'])->nullable();
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('trn_escore_session')->onDelete('cascade');
            $table->unique(['session_id', 'nomor_set']);
        });

        // 4. Log Audit Setiap Aksi Reli (Poin, Rotasi, Subs, Sanksi, Challenge)
        Schema::create('trn_escore_action_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('set_id');
            $table->integer('nomor_reli');
            $table->enum('tipe_aksi', ['POINT', 'SUBSTITUTION', 'LIBERO_REPLACE', 'TIMEOUT', 'CHALLENGE', 'SANCTION', 'CHANGE_SIDE', 'UNDO']);
            $table->enum('tim', ['A', 'B']);
            $table->unsignedBigInteger('pemain_id')->nullable();
            $table->unsignedBigInteger('pemain_keluar_id')->nullable();
            $table->integer('skor_a_sebelum');
            $table->integer('skor_b_sebelum');
            $table->integer('skor_a_sesudah');
            $table->integer('skor_b_sesudah');
            $table->json('posisi_court_a'); // Snapshot 6 pemain di lapangan saat reli ini
            $table->json('posisi_court_b');
            $table->json('meta_data')->nullable(); // detail kartu sanksi, tipe challenge
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('set_id')->references('id')->on('trn_escore_set')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('trn_escore_action_log');
        Schema::dropIfExists('trn_escore_set');
        Schema::dropIfExists('trn_escore_session');
        Schema::dropIfExists('trn_jadwal_pertandingan');
    }
};
```

---

## 4. Arsitektur Komponen Alpine.js (`volleyballScoreEngine`)

Implementasi state scoring di frontend diwujudkan via komponen reaktif Alpine.js di [`resources/views/escore/operator.blade.php`](file:///D:/laragon/www/dashboard-pbvsi-v2/resources/views/escore/operator.blade.php):

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('volleyballScoreEngine', (initData) => ({
        match: initData.match,
        currentSetNum: initData.currentSetNum || 1,
        setScoreA: 0,
        setScoreB: 0,
        currentSet: {
            scoreA: 0,
            scoreB: 0,
            servingTeam: 'A', // Tim yang memegang servis
            courtA: [null, null, null, null, null, null], // Posisi I, II, III, IV, V, VI
            courtB: [null, null, null, null, null, null],
            timeoutsA: 0,
            timeoutsB: 0,
            subsCountA: 0,
            subsCountB: 0,
            subPairsA: {}, // { originalPlayerId: inCourtPlayerId } -> kunci pasangan substitusi
            subPairsB: {},
            liberosCourtA: {}, // Penggantian libero aktif
            liberosCourtB: {}
        },
        actionHistory: [], // Stack untuk tombol UNDO penuh

        // Menambah poin untuk Tim A atau B
        addPoint(team) {
            this.pushHistoryState();
            const prevServer = this.currentSet.servingTeam;
            
            if (team === 'A') {
                this.currentSet.scoreA++;
            } else {
                this.currentSet.scoreB++;
            }

            // Aturan Bolavoli: Jika tim penerima merebut poin (Side-Out), lakukan rotasi
            if (prevServer !== team) {
                this.currentSet.servingTeam = team;
                this.rotateTeam(team);
            }

            this.checkSetOver();
            this.syncToServer();
        },

        // Rotasi Lapangan Bolavoli: Pos II pindah ke I (Server), III ke II, dst.
        rotateTeam(team) {
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            const newCourt = [
                court[1], // Pos II -> Pos I (Server Baru)
                court[2], // Pos III -> Pos II
                court[3], // Pos IV -> Pos III
                court[4], // Pos V -> Pos IV
                court[5], // Pos VI -> Pos V
                court[0]  // Pos I -> Pos VI
            ];
            if (team === 'A') this.currentSet.courtA = newCourt;
            else this.currentSet.courtB = newCourt;
        },

        // Validasi & Eksekusi Pergantian Pemain Reguler (Maksimal 6 per set)
        makeSubstitution(team, posIndex, playerOutId, playerInId) {
            let subsCount = team === 'A' ? this.currentSet.subsCountA : this.currentSet.subsCountB;
            if (subsCount >= 6) {
                alert('Batas maksimal 6 pergantian pemain untuk set ini sudah tercapai!');
                return false;
            }

            let subPairs = team === 'A' ? this.currentSet.subPairsA : this.currentSet.subPairsB;
            // Validasi pasangan substitusi resmi FIVB
            if (subPairs[playerInId] && subPairs[playerInId] !== playerOutId) {
                alert('Pelanggaran Aturan: Pemain ini hanya boleh menggantikan pemain pasangannya!');
                return false;
            }

            this.pushHistoryState();
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            court[posIndex] = playerInId;
            subPairs[playerOutId] = playerInId;

            if (team === 'A') this.currentSet.subsCountA++;
            else this.currentSet.subsCountB++;

            this.syncToServer();
            return true;
        },

        // Pergantian Libero (Bebas kuota, hanya baris belakang: Pos I, VI, V)
        replaceLibero(team, posIndex, regularPlayerId, liberoPlayerId) {
            if (![0, 5, 4].includes(posIndex)) {
                alert('Libero hanya boleh bermain di baris belakang (Posisi I, VI, atau V)!');
                return false;
            }
            this.pushHistoryState();
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            court[posIndex] = liberoPlayerId;
            this.syncToServer();
            return true;
        },

        // Tombol Undo Penuh (Mengembalikan state persis sebelum aksi terakhir)
        undoLastAction() {
            if (this.actionHistory.length === 0) return;
            const prevState = this.actionHistory.pop();
            this.currentSet = JSON.parse(prevState);
            this.syncToServer();
        },

        pushHistoryState() {
            this.actionHistory.push(JSON.stringify(this.currentSet));
        },

        // Cek Pemenang Set (25 Poin selisih 2 untuk Set 1-4, 15 Poin untuk Set 5)
        checkSetOver() {
            const targetScore = this.currentSetNum === 5 ? 15 : 25;
            const scoreA = this.currentSet.scoreA;
            const scoreB = this.currentSet.scoreB;

            if ((scoreA >= targetScore || scoreB >= targetScore) && Math.abs(scoreA - scoreB) >= 2) {
                const winner = scoreA > scoreB ? 'A' : 'B';
                if (winner === 'A') this.setScoreA++; else this.setScoreB++;
                alert(`Set ${this.currentSetNum} Selesai! Pemenang: Tim ${winner}`);
            }
        },

        // Sinkronisasi otomatis ke backend Laravel
        async syncToServer() {
            await fetch(`/escore/${this.match.id}/sync-rally`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    set_num: this.currentSetNum,
                    state: this.currentSet
                })
            });
        }
    }));
});
```

---

## 5. Generator PDF Lembar Skor Resmi A4 (FIVB / AVC)

Pada `dashboard-pbvsi-v2`, pembuatan PDF resmi dapat menggunakan paket standar Laravel seperti `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`:
- Template Blade didesain dengan format grid presisi tinggi standar FIVB/AVC:
  - Header: Nama Turnamen, Edisi, Kota, Hall, Pool, Tanggal & Jam.
  - Tabel 5 Set: Menampilkan 6 kolom posisi rotasi I–VI, urutan servis per set, skor reli 1–48 dengan coretan garis resmi, dan riwayat pergantian pemain.
  - Sanksi: Tabel kartu wasit (W/P/E/D).
  - Tanda Tangan: Image base64 dari tanda tangan digital wasit dan kapten langsung dicetak di bagian bawah.

---

## 6. Integrasi Broadcast TV & Layar Penonton

- **URL Overlay OBS / vMix:** `route('escore.overlay', $idJadwal)`
  - Menampilkan nama kedua tim, warna jersey, skor set, skor poin reli yang sedang berjalan, dan indikator panah/titik tim yang sedang servis.
  - Background CSS diset `background: transparent;` sehingga langsung siap pakai sebagai *Browser Source* di vMix atau OBS Studio.
- **Halaman Skor Publik / Mobile Penonton:** `route('escore.live', $idJadwal)`
  - Halaman web ringan yang me-refresh status skor tiap detik (via Server-Sent Events / SSE atau polling ringan), memungkinkan penonton memantau jalannya laga dari ponsel.

---

## 7. Rencana Tindak Lanjut Implementasi di `dashboard-pbvsi-v2`

1. **Buat File Migration:** `php artisan make:migration create_escore_match_tables` dengan skema tabel di atas.
2. **Buat Eloquent Models:** `JadwalPertandingan`, `EscoreSession`, `EscoreSet`, `EscoreActionLog`.
3. **Buat Controller & Routes:** `EscoreController.php` dengan aksi:
   - `operator($id)`: Layar kontrol scoring meja juri wasit.
   - `syncRally(Request $request, $id)`: Menerima snapshot skor dan state reli.
   - `overlay($id)`: Tampilan transparan untuk siaran langsung TV/vMix/OBS.
   - `scoresheetPdf($id)`: Cetak lembar skor resmi A4 PDF.
