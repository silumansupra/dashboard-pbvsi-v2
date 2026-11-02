<?php

namespace App\Http\Controllers;

use App\Models\Turnamen;
use App\Models\Edisi;
use App\Models\PesertaTim;
use App\Models\Pemain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KejuaraanController extends Controller
{
    /**
     * Entry point utama Kejuaraan: /kejuaraan/{slug}/{tahun?}
     */
    public function show(Request $request, $slug, $tahun = null)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();

        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        // Jika tahun tidak dispesifikasikan di URL, pilih edisi aktif atau tahun terbaru
        if (!$tahun) {
            $activeEdisi = $edisiList->firstWhere('status_edisi', 'REGISTRASI') 
                        ?? $edisiList->firstWhere('status_edisi', 'RUNNING') 
                        ?? $edisiList->first();

            $tahun = $activeEdisi ? $activeEdisi->tahun : date('Y');
            return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]);
        }

        $edisi = Edisi::where('turnamen_id', $turnamen->id)
            ->where('tahun', $tahun)
            ->with(['timPeserta.pengprov', 'timPeserta.roster.pemain'])
            ->first();

        $timPutra = collect();
        $timPutri = collect();

        if ($edisi) {
            $timPutra = $edisi->timPeserta()->where('kategori_gender', 'PA')->get();
            $timPutri = $edisi->timPeserta()->where('kategori_gender', 'PI')->get();
        }

        return view('kejuaraan.panel', compact(
            'turnamen',
            'edisi',
            'edisiList',
            'timPutra',
            'timPutri',
            'slug',
            'tahun'
        ));
    }

    /**
     * Membuat Edisi Tahun Baru untuk Turnamen ini
     */
    public function storeEdisi(Request $request, $slug)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2035',
            'nama_edisi' => 'required|string|max:200',
            'kota_tuan_rumah' => 'nullable|string|max:100',
            'venue_utama' => 'nullable|string|max:150',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'min_tanggal_lahir' => 'nullable|date',
            'max_tanggal_lahir' => 'nullable|date',
            'max_kuota_pemain' => 'required|integer|min:6|max:20',
            'status_edisi' => 'required|in:DRAFT,REGISTRASI,RUNNING,FINISHED',
        ]);

        $slugEdisi = $slug . '-' . $validated['tahun'];

        // Cek jika edisi tahun tersebut sudah ada
        $exists = Edisi::where('turnamen_id', $turnamen->id)
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Edisi {$turnamen->nama_turnamen} untuk tahun {$validated['tahun']} sudah pernah dibuat.");
        }

        Edisi::create(array_merge($validated, [
            'turnamen_id' => $turnamen->id,
            'slug_edisi' => $slugEdisi,
        ]));

        return redirect()->route('kejuaraan.tahun', [
            'slug' => $slug,
            'tahun' => $validated['tahun']
        ])->with('success', "Edisi baru {$validated['nama_edisi']} berhasil dibuat dan siap digunakan!");
    }

    /**
     * Tab Daftar Tim Peserta
     */
    public function teams($slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();
        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        $teams = PesertaTim::where('edisi_id', $edisi->id)
            ->withCount('roster')
            ->paginate(15);

        return view('kejuaraan.teams', compact('turnamen', 'edisi', 'edisiList', 'teams', 'slug', 'tahun'));
    }

    /**
     * Tab Pemain
     */
    public function players($slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();
        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        $players = Pemain::latest()->paginate(20);

        return view('kejuaraan.players', compact('turnamen', 'edisi', 'edisiList', 'players', 'slug', 'tahun'));
    }
}
