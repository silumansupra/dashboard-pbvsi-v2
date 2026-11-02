<?php

namespace App\Http\Controllers;

use App\Models\Turnamen;
use App\Models\Edisi;
use App\Models\PesertaTim;
use App\Models\Pemain;
use Illuminate\Http\Request;

class KejurnasController extends Controller
{
    /**
     * Entry point dinamis: /kejurnas/{kelompok_umur}/{tahun}
     * Contoh: /kejurnas/u18/2026, /kejurnas/u19/2026
     */
    public function show($kelompok_umur, $tahun)
    {
        $kelompok_umur = strtoupper($kelompok_umur); // e.g. U18, U19, U16, U17

        $turnamen = Turnamen::where('kategori', 'KEJURNAS_UMUR')
            ->where('kelompok_umur', $kelompok_umur)
            ->firstOrFail();

        $edisi = Edisi::where('turnamen_id', $turnamen->id)
            ->where('tahun', $tahun)
            ->with(['timPeserta.roster.pemain', 'timPeserta.pengprov'])
            ->first();

        if (!$edisi) {
            // Jika edisi tahun ini belum ada, buat otomatis atau tampilkan halaman belum dimulai
            abort(404, "Edisi Kejurnas {$kelompok_umur} Tahun {$tahun} belum dibuat oleh PP PBVSI.");
        }

        $timPutra = $edisi->timPeserta()->where('kategori_gender', 'PA')->get();
        $timPutri = $edisi->timPeserta()->where('kategori_gender', 'PI')->get();

        return view('kejurnas.panel', compact('turnamen', 'edisi', 'timPutra', 'timPutri', 'kelompok_umur', 'tahun'));
    }

    /**
     * Tab Daftar Tim Peserta
     */
    public function teams($kelompok_umur, $tahun)
    {
        $kelompok_umur = strtoupper($kelompok_umur);
        $edisi = Edisi::whereHas('turnamen', function ($q) use ($kelompok_umur) {
            $q->where('kelompok_umur', $kelompok_umur);
        })->where('tahun', $tahun)->firstOrFail();

        $teams = PesertaTim::where('edisi_id', $edisi->id)
            ->withCount('roster')
            ->paginate(15);

        return view('kejurnas.teams', compact('edisi', 'teams', 'kelompok_umur', 'tahun'));
    }

    /**
     * Tab Bank Pemain Terverifikasi
     */
    public function players($kelompok_umur, $tahun)
    {
        $kelompok_umur = strtoupper($kelompok_umur);
        $edisi = Edisi::whereHas('turnamen', function ($q) use ($kelompok_umur) {
            $q->where('kelompok_umur', $kelompok_umur);
        })->where('tahun', $tahun)->firstOrFail();

        $players = Pemain::latest()->paginate(20);

        return view('kejurnas.players', compact('edisi', 'players', 'kelompok_umur', 'tahun'));
    }
}
