# Spesifikasi Live Broadcast Overlay & Ekspor PDF Lembar Skor Resmi

Dokumen ini memuat spesifikasi integrasi grafik siaran langsung (OBS/vMix) dan pencetakan lembar skor resmi (FIVB/AVC Scoresheet).

---

## 1. Live Graphic Overlay untuk OBS Studio / vMix

### Endpoint: `GET /escore/overlay/{jadwal_id}`

View Blade ini dirancang khusus untuk dijadikan **Browser Source** di OBS Studio atau vMix:
- Resolusi default: 1920x1080 (Full HD).
- Latar belakang: transparan (`body { background: transparent; }`).
- Polling / WebSocket: mengambil snapshot skor terbaru setiap 1–2 detik secara ringan (`/escore/{id}/live-json`).

### Contoh Struktur Blade Overlay (`resources/views/escore/overlay.blade.php`):

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Live Scoreboard Overlay</title>
    @vite(['resources/css/app.css'])
    <style>
        body { background: transparent; overflow: hidden; }
        .score-box {
            background: rgba(15, 23, 42, 0.9);
            color: #ffffff;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            border-bottom: 4px solid #ef4444;
        }
    </style>
</head>
<body class="p-8">
    <div x-data="liveOverlay({{ $jadwal->id }})" class="fixed top-8 left-8 score-box rounded-xl p-4 flex items-center gap-6">
        <!-- Tim A -->
        <div class="flex items-center gap-3">
            <span class="w-4 h-4 rounded-full" :style="{ backgroundColor: matchData.teamA.jerseyColor }"></span>
            <span class="font-bold text-xl uppercase tracking-wider" x-text="matchData.teamA.name"></span>
            <span class="text-2xl font-black bg-slate-800 px-3 py-1 rounded" x-text="matchData.currentSet.scoreA"></span>
            <template x-if="matchData.currentSet.servingTeam === 'A'">
                <span class="text-yellow-400 font-bold text-xs animate-pulse">● SERVE</span>
            </template>
        </div>

        <div class="text-slate-400 font-semibold px-2">SET <span x-text="matchData.setNum"></span></div>

        <!-- Tim B -->
        <div class="flex items-center gap-3">
            <template x-if="matchData.currentSet.servingTeam === 'B'">
                <span class="text-yellow-400 font-bold text-xs animate-pulse">SERVE ●</span>
            </template>
            <span class="text-2xl font-black bg-slate-800 px-3 py-1 rounded" x-text="matchData.currentSet.scoreB"></span>
            <span class="font-bold text-xl uppercase tracking-wider" x-text="matchData.teamB.name"></span>
            <span class="w-4 h-4 rounded-full" :style="{ backgroundColor: matchData.teamB.jerseyColor }"></span>
        </div>
    </div>

    <script>
        function liveOverlay(matchId) {
            return {
                matchData: {
                    setNum: 1,
                    teamA: { name: 'TIM A', jerseyColor: '#ef4444' },
                    teamB: { name: 'TIM B', jerseyColor: '#3b82f6' },
                    currentSet: { scoreA: 0, scoreB: 0, servingTeam: 'A' }
                },
                init() {
                    setInterval(async () => {
                        try {
                            const res = await fetch(`/escore/${matchId}/live-json`);
                            if (res.ok) this.matchData = await res.json();
                        } catch(e) {}
                    }, 1000);
                }
            }
        }
    </script>
</body>
</html>
```

---

## 2. Format PDF Lembar Skor Resmi A4 (FIVB / AVC Standards)

Lembar skor resmi dicetak dalam format **A4 Landscape** dengan layout presisi tinggi:

1. **Header Pertandingan:**
   - Nama Kejuaraan / Turnamen, Edisi, Kategori (Putra/Putri).
   - Kota Tuan Rumah, Nama Hall/Venue, Tanggal & Waktu Mulai/Selesai.
   - Pool / Nomor Pertandingan.

2. **Matriks 5 Set:**
   - Kolom Urutan Servis (Service Order I s/d VI) untuk Tim A dan Tim B.
   - Baris Pemain Awal (Starting Players).
   - Baris Substitusi (Substitutions) dan skor saat substitusi terjadi.
   - Matriks Poin Reli (Angka 1 s/d 48 dicoret secara berurutan sesuai jalannya pertandingan).

3. **Tabel Sanksi (Sanctions):**
   - Kolom: Warning (W), Penalty (P), Expulsion (E), Disqualification (D).
   - Mencatat nomor punggung atau official, tim, set, dan skor saat pelanggaran terjadi.

4. **Tanda Tangan Digital Resmi:**
   - Tanda tangan Kapten Tim A & B (sebelum dan sesudah pertandingan).
   - Tanda tangan Wasit 1 (R1), Wasit 2 (R2), Scorer, dan Assistant Scorer.
   - Base64 PNG yang dihasilkan dari HTML5 Canvas langsung disematkan pada tag `<img>` di dalam PDF.
