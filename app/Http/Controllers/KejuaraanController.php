<?php

namespace App\Http\Controllers;

use App\Models\Turnamen;
use App\Models\Edisi;
use App\Models\PesertaTim;
use App\Models\Pemain;
use App\Models\Pengprov;
use App\Models\Klub;
use App\Services\BunnyCDNService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KejuaraanController extends Controller
{
    /**
     * Resolusi ID tim (mendukung integer ID murni maupun encrypted token)
     */
    protected function resolveTeamId($id): int
    {
        if (is_numeric($id)) {
            return (int) $id;
        }
        $decrypted = decrypt_id($id, 'peserta_tim') ?? decrypt_id($id);
        return $decrypted ? (int) $decrypted : (int) $id;
    }

    /**
     * Entry point utama Kejuaraan: /kejuaraan/{slug}/{tahun?}
     * Tampilan tabel yang disederhanakan, fokus pada daftar tim peserta dan aksinya.
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
            ->first();

        $teams = collect();
        $stats = [
            'total' => 0,
            'putra' => 0,
            'putri' => 0,
            'aktif' => 0,
            'nonaktif' => 0,
            'trashed' => 0,
        ];

        $pengprovList = collect();
        $klubList = collect();

        if ($edisi) {
            // Hitung statistik untuk badge ringkas
            $stats['total'] = PesertaTim::where('edisi_id', $edisi->id)->count();
            $stats['putra'] = PesertaTim::where('edisi_id', $edisi->id)->where('kategori_gender', 'PA')->count();
            $stats['putri'] = PesertaTim::where('edisi_id', $edisi->id)->where('kategori_gender', 'PI')->count();
            $stats['aktif'] = PesertaTim::where('edisi_id', $edisi->id)->where('is_active', true)->count();
            $stats['nonaktif'] = PesertaTim::where('edisi_id', $edisi->id)->where('is_active', false)->count();
            $stats['trashed'] = PesertaTim::onlyTrashed()->where('edisi_id', $edisi->id)->count();

            // Query Tim dengan Filter
            $query = PesertaTim::where('edisi_id', $edisi->id);

            // Filter Status / Trashed
            $statusFilter = $request->get('status', 'all_active');
            if ($statusFilter === 'trashed') {
                $query->onlyTrashed();
            } elseif ($statusFilter === 'active') {
                $query->where('is_active', true);
            } elseif ($statusFilter === 'inactive') {
                $query->where('is_active', false);
            } elseif ($statusFilter === 'with_trashed') {
                $query->withTrashed();
            }

            // Filter Kategori Gender
            if ($request->filled('gender') && in_array($request->gender, ['PA', 'PI'])) {
                $query->where('kategori_gender', $request->gender);
            }

            // Filter Pencarian
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('nama_tim', 'like', "%{$search}%")
                      ->orWhere('kode_tim', 'like', "%{$search}%")
                      ->orWhere('pool_grup', 'like', "%{$search}%")
                      ->orWhereHas('pengprov', fn($sq) => $sq->where('nama_pengprov', 'like', "%{$search}%"))
                      ->orWhereHas('klub', fn($sq) => $sq->where('nama_klub', 'like', "%{$search}%"));
                });
            }

            $perPage = (int) $request->get('per_page', 15);
            $teams = $query->with(['pengprov', 'klub'])
                ->withCount('roster')
                ->orderBy('pool_grup', 'asc')
                ->orderBy('nama_tim', 'asc')
                ->paginate($perPage)
                ->withQueryString();

            // Master data untuk modal Assign Tim
            $pengprovList = Pengprov::where('status_aktif', true)->orderBy('nama_pengprov')->get();
            $klubList = Klub::orderBy('nama_klub')->get();
        }

        return view('kejuaraan.panel', compact(
            'turnamen',
            'edisi',
            'edisiList',
            'teams',
            'stats',
            'pengprovList',
            'klubList',
            'slug',
            'tahun'
        ));
    }

    /**
     * 1. CRUD EDISI: Tambah Edisi Tahun Baru
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
     * 1. CRUD EDISI: Update Pengaturan Edisi
     */
    public function updateEdisi(Request $request, $slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $validated = $request->validate([
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

        $edisi->update($validated);

        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun])
            ->with('success', "Pengaturan edisi {$edisi->nama_edisi} berhasil diperbarui!");
    }

    /**
     * 1. CRUD EDISI: Hapus / Arsip Edisi (Soft Delete)
     */
    public function destroyEdisi(Request $request, $slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $namaEdisi = $edisi->nama_edisi;
        $edisi->delete(); // Soft delete

        return redirect()->route('kejuaraan.index', ['slug' => $slug])
            ->with('success', "Edisi {$namaEdisi} berhasil diarsipkan (soft delete).");
    }

    /**
     * 2. ASSIGN TIM: Halaman Create / Assign Tim Peserta
     */
    public function createTeam($slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();
        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        $pengprovList = Pengprov::where('status_aktif', true)->orderBy('nama_pengprov')->get();
        $klubList = Klub::orderBy('nama_klub')->get();

        return view('kejuaraan.tim.create', compact('turnamen', 'edisi', 'edisiList', 'slug', 'tahun', 'pengprovList', 'klubList'));
    }

    /**
     * 2. ASSIGN TIM: Menugaskan Tim/Klub PBVSI ke dalam Edisi Turnamen
     * (Bukan membuat entitas master klub baru, melainkan memilih klub yang sudah terdaftar)
     */
    public function assignTeam(Request $request, $slug, $tahun)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $validated = $request->validate([
            'tipe_asal' => 'required|in:pengprov,klub',
            'pengprov_id' => 'required_if:tipe_asal,pengprov|nullable|integer|exists:org_pengprov,id',
            'klub_id' => 'required_if:tipe_asal,klub|nullable|integer|exists:org_klub,id',
            'kategori_gender' => 'required|in:PA,PI',
            'pool_grup' => 'nullable|string|max:10',
            'nama_tim_custom' => 'nullable|string|max:150',
            'is_active' => 'nullable|boolean',
        ]);

        $pengprovId = null;
        $klubId = null;
        $namaTim = '';
        $kodeTim = '';

        if ($validated['tipe_asal'] === 'pengprov') {
            $pengprov = Pengprov::findOrFail($validated['pengprov_id']);
            $pengprovId = $pengprov->id;
            $namaTim = $request->filled('nama_tim_custom') 
                ? trim($request->nama_tim_custom) 
                : $pengprov->nama_pengprov . ' ' . ($validated['kategori_gender'] === 'PA' ? 'Putra' : 'Putri');
            $kodeTim = strtoupper(substr($pengprov->nama_pengprov, 0, 3));

            // Cek apakah tim pengprov ini sudah di-assign dengan gender yang sama
            $exists = PesertaTim::where('edisi_id', $edisi->id)
                ->where('pengprov_id', $pengprovId)
                ->where('kategori_gender', $validated['kategori_gender'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', "Kontingen {$pengprov->nama_pengprov} untuk kategori {$validated['kategori_gender']} sudah terdaftar dalam edisi ini.");
            }
        } else {
            $klub = Klub::findOrFail($validated['klub_id']);
            $klubId = $klub->id;
            $pengprovId = $klub->pengprov_id;
            $namaTim = $request->filled('nama_tim_custom') ? trim($request->nama_tim_custom) : $klub->nama_klub;
            $kodeTim = $klub->kode_klub ?: strtoupper(substr($klub->nama_klub, 0, 3));

            // Cek apakah klub ini sudah di-assign dengan gender yang sama
            $exists = PesertaTim::where('edisi_id', $edisi->id)
                ->where('klub_id', $klubId)
                ->where('kategori_gender', $validated['kategori_gender'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', "Klub {$klub->nama_klub} untuk kategori {$validated['kategori_gender']} sudah terdaftar dalam edisi ini.");
            }
        }

        $peserta = PesertaTim::create([
            'edisi_id' => $edisi->id,
            'pengprov_id' => $pengprovId,
            'klub_id' => $klubId,
            'nama_tim' => $namaTim,
            'kode_tim' => $kodeTim,
            'kategori_gender' => $validated['kategori_gender'],
            'pool_grup' => $request->pool_grup ? strtoupper(trim($request->pool_grup)) : null,
            'status_verifikasi' => 'VERIFIED',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun])
            ->with('success', "Tim {$peserta->nama_tim} berhasil di-assign ke edisi {$edisi->nama_edisi}!");
    }

    /**
     * 3. DETAIL TIM PESERTA: Melihat Profil Kepesertaan, Roster Pemain, & Official
     */
    public function showTeam($slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();
        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::withTrashed()
            ->where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->with(['edisi', 'pengprov', 'klub', 'roster.pemain', 'officials.user'])
            ->firstOrFail();

        return view('kejuaraan.tim.detail', compact('turnamen', 'edisi', 'edisiList', 'team', 'slug', 'tahun'));
    }

    /**
     * 4. ATUR PUL / KEPESERTAAN: Halaman Edit Partisipasi Tim
     * (Hanya mengatur Pul/Grup, Kategori Gender, dan Status Partisipasi Turnamen)
     */
    public function editTeam($slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();
        $edisiList = $turnamen->edisi()->orderByDesc('tahun')->get();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->firstOrFail();

        return view('kejuaraan.tim.edit', compact('turnamen', 'edisi', 'edisiList', 'team', 'slug', 'tahun'));
    }

    /**
     * 4. UPDATE PARTISIPASI TIM: Simpan Pul / Status Partisipasi Turnamen
     */
    public function updateTeamParticipation(Request $request, $slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->firstOrFail();

        $validated = $request->validate([
            'pool_grup' => 'nullable|string|max:10',
            'kategori_gender' => 'required|in:PA,PI',
            'is_active' => 'nullable|boolean',
            'status_verifikasi' => 'nullable|in:DRAFT,SUBMITTED,VERIFIED,REJECTED',
            'nama_tim_custom' => 'nullable|string|max:150',
        ]);

        if ($request->filled('nama_tim_custom')) {
            $team->nama_tim = trim($request->nama_tim_custom);
        }

        $team->kategori_gender = $validated['kategori_gender'];
        $team->pool_grup = $request->pool_grup ? strtoupper(trim($request->pool_grup)) : null;
        $team->is_active = $request->has('is_active') ? $request->boolean('is_active') : $team->is_active;

        if ($request->filled('status_verifikasi')) {
            $team->status_verifikasi = $request->status_verifikasi;
        }

        $team->save();

        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun])
            ->with('success', "Pengaturan pul & kepesertaan tim {$team->nama_tim} berhasil diperbarui!");
    }

    /**
     * 5. TOGGLE AKTIF: Mengaktifkan / Menonaktifkan Status Partisipasi Tim di Turnamen
     */
    public function toggleActiveTeam(Request $request, $slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->firstOrFail();

        $team->is_active = !$team->is_active;
        $team->save();

        $statusLabel = $team->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $message = "Status partisipasi tim {$team->nama_tim} berhasil {$statusLabel}!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $team->is_active,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * 6. UNASSIGN / KELUARKAN TIM: Melepas Tim dari Edisi Turnamen Ini (Soft Delete Partisipasi)
     * (Master data klub tetap aman di database PBVSI)
     */
    public function destroyTeam(Request $request, $slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->firstOrFail();

        $namaTim = $team->nama_tim;
        $team->delete(); // Soft Delete partisipasi

        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun])
            ->with('success', "Tim {$namaTim} berhasil dikeluarkan dari edisi turnamen ini.");
    }

    /**
     * 7. RESTORE KEPESERTAAN: Memulihkan Tim yang Sebelumnya Dikeluarkan
     */
    public function restoreTeam(Request $request, $slug, $tahun, $id)
    {
        $turnamen = Turnamen::where('slug', $slug)->firstOrFail();
        $edisi = Edisi::where('turnamen_id', $turnamen->id)->where('tahun', $tahun)->firstOrFail();

        $resolvedId = $this->resolveTeamId($id);
        $team = PesertaTim::onlyTrashed()
            ->where('edisi_id', $edisi->id)
            ->where('id', $resolvedId)
            ->firstOrFail();

        $namaTim = $team->nama_tim;
        $team->restore(); // Restore partisipasi

        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun])
            ->with('success', "Kepesertaan tim {$namaTim} berhasil dipulihkan!");
    }

    /**
     * Tab Daftar Tim Peserta (Backward compatibility)
     */
    public function teams($slug, $tahun)
    {
        return redirect()->route('kejuaraan.tahun', ['slug' => $slug, 'tahun' => $tahun]);
    }

    /**
     * Tab Pemain (Backward compatibility)
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
