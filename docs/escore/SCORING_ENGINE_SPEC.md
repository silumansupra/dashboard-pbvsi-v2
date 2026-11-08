# Spesifikasi Logika Scoring Engine (Alpine.js)

Dokumen ini memuat spesifikasi logika lapangan pertandingan **bolavoli** dan implementasi komponen reaktif Alpine.js siap-pakai.

---

## 1. Aturan Resmi Pertandingan Bolavoli (FIVB / PBVSI)

1. **Struktur Posisi Lapangan:**
   - Baris Depan: Posisi IV (Kiri Depan), Posisi III (Tengah Depan), Posisi II (Kanan Depan).
   - Baris Belakang: Posisi V (Kiri Belakang), Posisi VI (Tengah Belakang), Posisi I (Kanan Belakang / Servis).
   - Indeks Array Komputer: `[0: Pos I, 1: Pos II, 2: Pos III, 3: Pos IV, 4: Pos V, 5: Pos VI]`.

2. **Aturan Rotasi (Side-Out):**
   - Saat tim penerima servis memenangkan reli, tim tersebut mendapatkan 1 poin dan **wajib melakukan rotasi satu posisi searah jarum jam**:
     - Posisi II &rarr; Posisi I (pemain ini menjadi server baru).
     - Posisi III &rarr; Posisi II.
     - Posisi IV &rarr; Posisi III.
     - Posisi V &rarr; Posisi IV.
     - Posisi VI &rarr; Posisi V.
     - Posisi I &rarr; Posisi VI.

3. **Aturan Pergantian Pemain Reguler (Substitutions):**
   - Kuota: Maksimal **6 kali** pergantian pemain per tim dalam satu set.
   - Pasangan Terkunci (*Tied Pairs*): Pemain awal (Starting VI) yang diganti hanya boleh masuk kembali 1 kali menggantikan pemain penggantinya, dan pemain pengganti tidak boleh menggantikan pemain lain di set yang sama.

4. **Aturan Pergantian Libero:**
   - Tidak dihitung dalam kuota 6 pergantian pemain reguler (bebas kuota).
   - Libero hanya boleh berada di baris belakang (**Posisi I, VI, atau V**).
   - Saat rotasi membawa libero ke baris depan (Posisi IV), libero wajib digantikan kembali oleh pemain reguler aslinya sebelum reli berikutnya.

5. **Penentuan Pemenang Set (Set Over):**
   - Set 1 s/d 4: Target **25 poin** dengan selisih minimal **2 poin** (contoh: 25-23, jika 24-24 deuce lanjut hingga 26-24, 27-25, dst.).
   - Set 5 (Deciding Set): Target **15 poin** dengan selisih minimal **2 poin** (lanjut jika 14-14).
   - Pindah Sisi (Change Side) di Set 5: Terjadi tepat saat salah satu tim pertama kali mencapai poin **8**.

6. **Time-Out (TO):**
   - Setiap tim memiliki jatah **2 kali time-out** (durasi 30 detik) per set.

---

## 2. Kode Komponen Alpine.js (`volleyballScoreEngine`)

Letakkan script ini pada view operator eScore (`resources/views/escore/operator.blade.php`):

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('volleyballScoreEngine', (initData) => ({
        matchId: initData.matchId,
        currentSetNum: initData.currentSetNum || 1,
        setScoreA: initData.setScoreA || 0,
        setScoreB: initData.setScoreB || 0,
        
        // Data Set Aktif
        currentSet: {
            scoreA: 0,
            scoreB: 0,
            servingTeam: 'A', // 'A' atau 'B'
            courtA: [null, null, null, null, null, null], // [Pos I, II, III, IV, V, VI]
            courtB: [null, null, null, null, null, null],
            timeoutsA: 0,
            timeoutsB: 0,
            subsCountA: 0,
            subsCountB: 0,
            subPairsA: {}, // { originalId: subId }
            subPairsB: {},
            liberosCourtA: {}, // { posIndex: liberoId }
            liberosCourtB: {}
        },

        // Stack Riwayat untuk Undo Penuh
        actionHistory: [],

        // Tambah Poin
        addPoint(team) {
            this.pushHistoryState();
            const prevServer = this.currentSet.servingTeam;
            
            if (team === 'A') {
                this.currentSet.scoreA++;
            } else {
                this.currentSet.scoreB++;
            }

            // Jika side-out (tim penerima merebut poin), rotasi tim pemenang
            if (prevServer !== team) {
                this.currentSet.servingTeam = team;
                this.rotateTeam(team);
            }

            // Pindah sisi di set ke-5 pada poin ke-8
            if (this.currentSetNum === 5 && (this.currentSet.scoreA === 8 || this.currentSet.scoreB === 8)) {
                alert('Poin 8 tercapai pada Set Penentuan: Kedua Tim Bertukar Sisi Lapangan!');
            }

            this.checkSetOver();
            this.syncToServer('POINT', { team });
        },

        // Rotasi Lapangan Searah Jarum Jam
        rotateTeam(team) {
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            const newCourt = [
                court[1], // II -> I (Server Baru)
                court[2], // III -> II
                court[3], // IV -> III
                court[4], // V -> IV
                court[5], // VI -> V
                court[0]  // I -> VI
            ];
            if (team === 'A') this.currentSet.courtA = newCourt;
            else this.currentSet.courtB = newCourt;
        },

        // Eksekusi Pergantian Pemain Reguler (Max 6)
        makeSubstitution(team, posIndex, playerOutId, playerInId) {
            let subsCount = team === 'A' ? this.currentSet.subsCountA : this.currentSet.subsCountB;
            if (subsCount >= 6) {
                alert('Batas maksimal 6 pergantian pemain untuk set ini sudah tercapai!');
                return false;
            }

            let subPairs = team === 'A' ? this.currentSet.subPairsA : this.currentSet.subPairsB;
            if (subPairs[playerInId] && subPairs[playerInId] !== playerOutId) {
                alert('Pelanggaran Aturan FIVB: Pemain ini hanya boleh menggantikan pemain pasangannya!');
                return false;
            }

            this.pushHistoryState();
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            court[posIndex] = playerInId;
            subPairs[playerOutId] = playerInId;

            if (team === 'A') this.currentSet.subsCountA++;
            else this.currentSet.subsCountB++;

            this.syncToServer('SUBSTITUTION', { team, posIndex, playerOutId, playerInId });
            return true;
        },

        // Pergantian Libero (Bebas kuota, khusus baris belakang: Pos I, VI, V)
        replaceLibero(team, posIndex, regularPlayerId, liberoPlayerId) {
            if (![0, 5, 4].includes(posIndex)) {
                alert('Libero hanya boleh berada di baris belakang (Posisi I, VI, atau V)!');
                return false;
            }
            this.pushHistoryState();
            let court = team === 'A' ? this.currentSet.courtA : this.currentSet.courtB;
            court[posIndex] = liberoPlayerId;
            this.syncToServer('LIBERO_REPLACE', { team, posIndex, regularPlayerId, liberoPlayerId });
            return true;
        },

        // Request Time Out 30 Detik
        requestTimeOut(team) {
            let toCount = team === 'A' ? this.currentSet.timeoutsA : this.currentSet.timeoutsB;
            if (toCount >= 2) {
                alert(`Jatah 2 kali Time-Out untuk Tim ${team} pada set ini sudah habis!`);
                return;
            }
            this.pushHistoryState();
            if (team === 'A') this.currentSet.timeoutsA++;
            else this.currentSet.timeoutsB++;
            
            // Trigger timer visual 30s
            this.startTimer(30);
            this.syncToServer('TIMEOUT', { team });
        },

        // Tombol Undo Penuh (Rollback Reli Terakhir)
        undoLastAction() {
            if (this.actionHistory.length === 0) {
                alert('Tidak ada riwayat reli untuk di-undo.');
                return;
            }
            const prevState = this.actionHistory.pop();
            this.currentSet = JSON.parse(prevState);
            this.syncToServer('UNDO', {});
        },

        pushHistoryState() {
            this.actionHistory.push(JSON.stringify(this.currentSet));
        },

        // Evaluasi Set Selesai
        checkSetOver() {
            const targetScore = this.currentSetNum === 5 ? 15 : 25;
            const scoreA = this.currentSet.scoreA;
            const scoreB = this.currentSet.scoreB;

            if ((scoreA >= targetScore || scoreB >= targetScore) && Math.abs(scoreA - scoreB) >= 2) {
                const winner = scoreA > scoreB ? 'A' : 'B';
                if (winner === 'A') this.setScoreA++; else this.setScoreB++;
                alert(`Set ${this.currentSetNum} Selesai! Pemenang: Tim ${winner} (${scoreA} - ${scoreB})`);
                
                if (this.setScoreA === 3 || this.setScoreB === 3) {
                    alert(`Pertandingan Selesai! Tim ${this.setScoreA === 3 ? 'A' : 'B'} Memenangkan Laga!`);
                }
            }
        },

        // Background Async Sync ke Laravel
        async syncToServer(actionType, meta) {
            try {
                await fetch(`/escore/${this.matchId}/sync-rally`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        set_num: this.currentSetNum,
                        action_type: actionType,
                        state: this.currentSet,
                        meta: meta
                    })
                });
            } catch (err) {
                console.warn('Gagal sinkron ke server (mode offline aktif):', err);
            }
        }
    }));
});
```
