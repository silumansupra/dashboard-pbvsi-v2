<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Jabatan;
use App\Models\PosisiPemain;
use App\Models\Pengprov;
use App\Models\Pengkot;
use App\Models\Klub;
use App\Models\PesertaTim;
use App\Models\OfficialTim;
use App\Models\User;
use App\Models\Role;
use App\Services\SecureId;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterController extends Controller
{
    /**
     * =========================================================================
     * 1. MASTER WILAYAH (PROVINSI & KABUPATEN)
     * =========================================================================
     */
    public function wilayah(Request $request)
    {
        $provQuery = Provinsi::withCount('kabupaten');

        if ($request->filled('search_prov')) {
            $provQuery->where('nama_provinsi', 'like', '%' . $request->search_prov . '%')
                      ->orWhere('kode_provinsi', 'like', '%' . $request->search_prov . '%');
        }

        $provinsi = $provQuery->orderBy('nama_provinsi')->get();
        
        $selectedProvId = $request->get('provinsi_id', $provinsi->first()?->id);
        $activeProvinsi = Provinsi::withCount(['kabupaten', 'pengprov'])->find($selectedProvId);

        $kabQuery = Kabupaten::where('provinsi_id', $selectedProvId);

        if ($request->filled('search_kab')) {
            $kabQuery->where('nama_kabupaten', 'like', '%' . $request->search_kab . '%');
        }

        if ($request->filled('tipe')) {
            $kabQuery->where('tipe', $request->tipe);
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $kabupaten = $kabQuery->orderBy('nama_kabupaten')
            ->paginate($perPage)
            ->withQueryString();

        return view('master.wilayah', compact('provinsi', 'kabupaten', 'selectedProvId', 'activeProvinsi'));
    }

    public function storeProvinsi(Request $request)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100',
            'kode_provinsi' => 'nullable|string|max:10|unique:core_wilayah_provinsi,kode_provinsi',
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'kode_provinsi.unique' => 'Kode provinsi sudah terdaftar.',
        ]);

        $prov = Provinsi::create([
            'nama_provinsi' => strtoupper(trim($request->nama_provinsi)),
            'kode_provinsi' => $request->filled('kode_provinsi') ? trim($request->kode_provinsi) : null,
            'is_active' => true,
        ]);

        return redirect()->route('master.wilayah', ['provinsi_id' => $prov->id])
            ->with('success', "Provinsi {$prov->nama_provinsi} berhasil ditambahkan!");
    }

    public function updateProvinsi(Request $request, $id)
    {
        $prov = Provinsi::findOrFail($id);

        $request->validate([
            'nama_provinsi' => 'required|string|max:100',
            'kode_provinsi' => 'nullable|string|max:10|unique:core_wilayah_provinsi,kode_provinsi,' . $id,
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'kode_provinsi.unique' => 'Kode provinsi sudah digunakan oleh provinsi lain.',
        ]);

        $prov->update([
            'nama_provinsi' => strtoupper(trim($request->nama_provinsi)),
            'kode_provinsi' => $request->filled('kode_provinsi') ? trim($request->kode_provinsi) : null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('master.wilayah', ['provinsi_id' => $prov->id])
            ->with('success', "Data provinsi {$prov->nama_provinsi} berhasil diperbarui!");
    }

    public function destroyProvinsi($id)
    {
        $prov = Provinsi::withCount(['kabupaten', 'pengprov'])->findOrFail($id);

        if ($prov->pengprov_count > 0) {
            return redirect()->route('master.wilayah', ['provinsi_id' => $id])
                ->with('error', "Gagal menghapus: Provinsi {$prov->nama_provinsi} terikat dengan data Pengurus Provinsi (Pengprov). Hapus atau ubah relasi pengprov terlebih dahulu.");
        }

        $nama = $prov->nama_provinsi;
        $prov->delete();

        return redirect()->route('master.wilayah')
            ->with('success', "Provinsi {$nama} berhasil dihapus beserta kabupaten di dalamnya.");
    }

    public function storeKabupaten(Request $request)
    {
        $request->validate([
            'provinsi_id' => 'required|exists:core_wilayah_provinsi,id',
            'nama_kabupaten' => 'required|string|max:100',
            'tipe' => 'required|in:KOTA,KABUPATEN',
            'kode_kabupaten' => 'nullable|string|max:10',
        ], [
            'nama_kabupaten.required' => 'Nama kabupaten / kota wajib diisi.',
            'provinsi_id.required' => 'Provinsi induk wajib dipilih.',
        ]);

        $nama = strtoupper(trim($request->nama_kabupaten));
        if ($request->tipe === 'KOTA' && !str_starts_with($nama, 'KOTA ')) {
            $nama = 'KOTA ' . $nama;
        } elseif ($request->tipe === 'KABUPATEN' && !str_starts_with($nama, 'KABUPATEN ') && !str_starts_with($nama, 'KAB. ')) {
            $nama = 'KABUPATEN ' . $nama;
        }

        $kab = Kabupaten::create([
            'provinsi_id' => $request->provinsi_id,
            'nama_kabupaten' => $nama,
            'tipe' => $request->tipe,
            'kode_kabupaten' => $request->filled('kode_kabupaten') ? trim($request->kode_kabupaten) : null,
        ]);

        return redirect()->route('master.wilayah', ['provinsi_id' => $request->provinsi_id])
            ->with('success', "{$kab->nama_kabupaten} berhasil ditambahkan!");
    }

    public function updateKabupaten(Request $request, $id)
    {
        $kab = Kabupaten::findOrFail($id);

        $request->validate([
            'nama_kabupaten' => 'required|string|max:100',
            'tipe' => 'required|in:KOTA,KABUPATEN',
            'kode_kabupaten' => 'nullable|string|max:10',
        ]);

        $nama = strtoupper(trim($request->nama_kabupaten));
        if ($request->tipe === 'KOTA' && !str_starts_with($nama, 'KOTA ')) {
            $nama = 'KOTA ' . $nama;
        } elseif ($request->tipe === 'KABUPATEN' && !str_starts_with($nama, 'KABUPATEN ') && !str_starts_with($nama, 'KAB. ')) {
            $nama = 'KABUPATEN ' . $nama;
        }

        $kab->update([
            'nama_kabupaten' => $nama,
            'tipe' => $request->tipe,
            'kode_kabupaten' => $request->filled('kode_kabupaten') ? trim($request->kode_kabupaten) : null,
        ]);

        return redirect()->route('master.wilayah', ['provinsi_id' => $kab->provinsi_id])
            ->with('success', "Data {$kab->nama_kabupaten} berhasil diperbarui!");
    }

    public function destroyKabupaten($id)
    {
        $kab = Kabupaten::withCount('pengkot')->findOrFail($id);
        $provId = $kab->provinsi_id;
        $nama = $kab->nama_kabupaten;

        if ($kab->pengkot_count > 0) {
            return redirect()->route('master.wilayah', ['provinsi_id' => $provId])
                ->with('error', "Gagal menghapus: {$nama} terikat dengan data Pengurus Kota/Kabupaten (Pengkot). Hapus atau ubah relasi pengkot terlebih dahulu.");
        }

        $kab->delete();

        return redirect()->route('master.wilayah', ['provinsi_id' => $provId])
            ->with('success', "{$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 2. MASTER PENGPROV (PENGURUS PROVINSI)
     * =========================================================================
     */
    public function pengprov(Request $request)
    {
        $query = Pengprov::with(['provinsi'])->withCount(['pengkot', 'klub']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengprov', 'like', "%{$search}%")
                  ->orWhere('ketum', 'like', "%{$search}%")
                  ->orWhere('sekum', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $pengprov = $query->orderBy('nama_pengprov')->paginate($perPage)->withQueryString();
        $provinces = Provinsi::where('is_active', true)->orderBy('nama_provinsi')->get();

        return view('master.pengprov', compact('pengprov', 'provinces'));
    }

    public function createPengprov()
    {
        $provinces = Provinsi::where('is_active', true)->orderBy('nama_provinsi')->get();
        return view('master.pengprov_create', compact('provinces'));
    }

    public function editPengprov($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!Pengprov::where('id', $rawId)->exists()) {
                abort(404, 'Data Pengprov tidak ditemukan.');
            }
            return redirect()->route('master.pengprov.edit', ['id' => encrypt_id($rawId, 'pengprov')], 301);
        }

        $realId = decrypt_id($id, 'pengprov');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $pengprov = Pengprov::findOrFail($realId);
        $provinces = Provinsi::where('is_active', true)->orderBy('nama_provinsi')->get();

        return view('master.pengprov_edit', compact('pengprov', 'provinces'));
    }

    public function storePengprov(Request $request)
    {
        $request->validate([
            'provinsi_id' => 'required|exists:core_wilayah_provinsi,id|unique:org_pengprov,provinsi_id',
            'nama_pengprov' => 'required|string|max:150',
            'ketum' => 'nullable|string|max:150',
            'sekum' => 'nullable|string|max:150',
            'nohp_ketum' => 'nullable|string|max:30',
            'nohp_sekum' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:30',
            'website' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'status_aktif' => 'required|in:0,1',
        ], [
            'provinsi_id.unique' => 'Pengurus Provinsi untuk wilayah ini sudah terdaftar sebelumnya.',
            'nama_pengprov.required' => 'Nama Pengprov wajib diisi.',
        ]);

        $pengprov = Pengprov::create([
            'provinsi_id' => $request->provinsi_id,
            'nama_pengprov' => strtoupper(trim($request->nama_pengprov)),
            'ketum' => $request->ketum,
            'sekum' => $request->sekum,
            'nohp_ketum' => $request->nohp_ketum,
            'nohp_sekum' => $request->nohp_sekum,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'website' => $request->website,
            'alamat' => $request->alamat,
            'status_aktif' => (bool) $request->status_aktif,
        ]);

        return redirect()->route('master.pengprov')
            ->with('success', "Pengprov {$pengprov->nama_pengprov} berhasil didaftarkan!");
    }

    public function updatePengprov(Request $request, $id)
    {
        $realId = decrypt_id($id, 'pengprov') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengprov tidak valid.');
        }

        $pengprov = Pengprov::findOrFail($realId);

        $request->validate([
            'provinsi_id' => 'required|exists:core_wilayah_provinsi,id|unique:org_pengprov,provinsi_id,' . $pengprov->id,
            'nama_pengprov' => 'required|string|max:150',
            'ketum' => 'nullable|string|max:150',
            'sekum' => 'nullable|string|max:150',
            'nohp_ketum' => 'nullable|string|max:30',
            'nohp_sekum' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:30',
            'website' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'status_aktif' => 'required|in:0,1',
        ]);

        $pengprov->update([
            'provinsi_id' => $request->provinsi_id,
            'nama_pengprov' => strtoupper(trim($request->nama_pengprov)),
            'ketum' => $request->ketum,
            'sekum' => $request->sekum,
            'nohp_ketum' => $request->nohp_ketum,
            'nohp_sekum' => $request->nohp_sekum,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'website' => $request->website,
            'alamat' => $request->alamat,
            'status_aktif' => (bool) $request->status_aktif,
        ]);

        return redirect()->route('master.pengprov')
            ->with('success', "Data Pengprov {$pengprov->nama_pengprov} berhasil diperbarui!");
    }

    public function destroyPengprov($id)
    {
        $realId = decrypt_id($id, 'pengprov') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengprov tidak valid.');
        }

        $pengprov = Pengprov::withCount(['pengkot', 'klub'])->findOrFail($realId);

        if ($pengprov->pengkot_count > 0 || $pengprov->klub_count > 0) {
            return redirect()->route('master.pengprov')
                ->with('error', "Gagal menghapus: {$pengprov->nama_pengprov} masih memiliki {$pengprov->pengkot_count} Pengkot/Pengkab dan {$pengprov->klub_count} Klub binaan. Hapus relasi tersebut terlebih dahulu.");
        }

        $nama = $pengprov->nama_pengprov;
        $pengprov->delete();

        return redirect()->route('master.pengprov')
            ->with('success', "Pengprov {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 3. MASTER PENGKOT / PENGKAB
     * =========================================================================
     */
    public function pengkot(Request $request)
    {
        $query = Pengkot::with(['pengprov.provinsi', 'kabupaten'])->withCount('klub');

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengkot', 'like', "%{$search}%")
                  ->orWhere('ketum', 'like', "%{$search}%")
                  ->orWhere('sekum', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $pengkot = $query->orderBy('nama_pengkot')->paginate($perPage)->withQueryString();
        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get();

        return view('master.pengkot', compact('pengkot', 'pengprovList'));
    }

    public function createPengkot()
    {
        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get();
        return view('master.pengkot_create', compact('pengprovList'));
    }

    public function editPengkot($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!Pengkot::where('id', $rawId)->exists()) {
                abort(404, 'Data Pengkot tidak ditemukan.');
            }
            return redirect()->route('master.pengkot.edit', ['id' => encrypt_id($rawId, 'pengkot')], 301);
        }

        $realId = decrypt_id($id, 'pengkot');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $pengkot = Pengkot::with('pengprov')->findOrFail($realId);
        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get();
        $kabupatenList = Kabupaten::where('provinsi_id', $pengkot->pengprov?->provinsi_id)->orderBy('nama_kabupaten')->get();

        return view('master.pengkot_edit', compact('pengkot', 'pengprovList', 'kabupatenList'));
    }

    public function apiKabupatenByPengprov($pengprovId)
    {
        $realId = decrypt_id($pengprovId, 'pengprov') ?? (is_numeric($pengprovId) ? (int)$pengprovId : null);
        if (!$realId) {
            return response()->json([]);
        }
        $pengprov = Pengprov::findOrFail($realId);
        $kabupaten = Kabupaten::where('provinsi_id', $pengprov->provinsi_id)
            ->orderBy('nama_kabupaten')
            ->select('id', 'nama_kabupaten', 'tipe')
            ->get();

        return response()->json($kabupaten);
    }

    public function apiPengkotByPengprov($pengprovId)
    {
        $realId = decrypt_id($pengprovId, 'pengprov') ?? (is_numeric($pengprovId) ? (int)$pengprovId : null);
        if (!$realId) {
            return response()->json([]);
        }
        $pengkot = Pengkot::where('pengprov_id', $realId)
            ->where('status_aktif', 1)
            ->orderBy('nama_pengkot')
            ->select('id', 'nama_pengkot')
            ->get();

        return response()->json($pengkot);
    }

    public function storePengkot(Request $request)
    {
        if ($request->filled('pengprov_id') && !is_numeric($request->pengprov_id)) {
            $request->merge(['pengprov_id' => decrypt_id($request->pengprov_id, 'pengprov')]);
        }

        $request->validate([
            'pengprov_id' => 'required|exists:org_pengprov,id',
            'kabupaten_id' => 'required|exists:core_wilayah_kabupaten,id|unique:org_pengkot,kabupaten_id',
            'nama_pengkot' => 'required|string|max:150',
            'ketum' => 'nullable|string|max:150',
            'sekum' => 'nullable|string|max:150',
            'nohp_ketum' => 'nullable|string|max:30',
            'nohp_sekum' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'status_aktif' => 'required|in:0,1',
        ], [
            'kabupaten_id.unique' => 'Pengurus untuk Kota/Kabupaten ini sudah terdaftar.',
            'nama_pengkot.required' => 'Nama Pengkot/Pengkab wajib diisi.',
        ]);

        $pengkot = Pengkot::create([
            'pengprov_id' => $request->pengprov_id,
            'kabupaten_id' => $request->kabupaten_id,
            'nama_pengkot' => strtoupper(trim($request->nama_pengkot)),
            'ketum' => $request->ketum,
            'sekum' => $request->sekum,
            'nohp_ketum' => $request->nohp_ketum,
            'nohp_sekum' => $request->nohp_sekum,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'status_aktif' => (bool) $request->status_aktif,
        ]);

        return redirect()->route('master.pengkot', ['pengprov_id' => $request->pengprov_id])
            ->with('success', "Pengurus {$pengkot->nama_pengkot} berhasil didaftarkan!");
    }

    public function updatePengkot(Request $request, $id)
    {
        $realId = decrypt_id($id, 'pengkot') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengkot tidak valid.');
        }

        $pengkot = Pengkot::findOrFail($realId);

        if ($request->filled('pengprov_id') && !is_numeric($request->pengprov_id)) {
            $request->merge(['pengprov_id' => decrypt_id($request->pengprov_id, 'pengprov')]);
        }

        $request->validate([
            'pengprov_id' => 'required|exists:org_pengprov,id',
            'kabupaten_id' => 'required|exists:core_wilayah_kabupaten,id|unique:org_pengkot,kabupaten_id,' . $pengkot->id,
            'nama_pengkot' => 'required|string|max:150',
            'ketum' => 'nullable|string|max:150',
            'sekum' => 'nullable|string|max:150',
            'nohp_ketum' => 'nullable|string|max:30',
            'nohp_sekum' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'status_aktif' => 'required|in:0,1',
        ]);

        $pengkot->update([
            'pengprov_id' => $request->pengprov_id,
            'kabupaten_id' => $request->kabupaten_id,
            'nama_pengkot' => strtoupper(trim($request->nama_pengkot)),
            'ketum' => $request->ketum,
            'sekum' => $request->sekum,
            'nohp_ketum' => $request->nohp_ketum,
            'nohp_sekum' => $request->nohp_sekum,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'status_aktif' => (bool) $request->status_aktif,
        ]);

        return redirect()->route('master.pengkot', ['pengprov_id' => $pengkot->pengprov_id])
            ->with('success', "Data {$pengkot->nama_pengkot} berhasil diperbarui!");
    }

    public function destroyPengkot($id)
    {
        $realId = decrypt_id($id, 'pengkot') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengkot tidak valid.');
        }

        $pengkot = Pengkot::withCount('klub')->findOrFail($realId);

        if ($pengkot->klub_count > 0) {
            return redirect()->route('master.pengkot', ['pengprov_id' => $pengkot->pengprov_id])
                ->with('error', "Gagal menghapus: {$pengkot->nama_pengkot} masih memiliki {$pengkot->klub_count} Klub terdaftar.");
        }

        $nama = $pengkot->nama_pengkot;
        $provId = $pengkot->pengprov_id;
        $pengkot->delete();

        return redirect()->route('master.pengkot', ['pengprov_id' => $provId])
            ->with('success', "{$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 4. MASTER KLUB / TIM TERDAFTAR
     * =========================================================================
     */
    public function klub(Request $request)
    {
        $tipe = $request->get('tipe', 'klub'); // 'klub' | 'pengprov'
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get();
        $totalKlubCount = Klub::count();
        $totalPengprovCount = Pengprov::whereHas('pesertaKontingen')->count();

        if ($tipe === 'pengprov') {
            $query = Pengprov::whereHas('pesertaKontingen')
                ->withCount('pesertaKontingen')
                ->with(['provinsi']);

            if ($request->filled('pengprov_id')) {
                $pid = decrypt_id($request->pengprov_id, 'pengprov')
                    ?? (is_numeric($request->pengprov_id) ? (int)$request->pengprov_id : null);
                if ($pid) {
                    $query->where('id', $pid);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pengprov', 'like', "%{$search}%")
                      ->orWhere('ketum', 'like', "%{$search}%")
                      ->orWhere('sekum', 'like', "%{$search}%");
                });
            }

            $pengprovTeams = $query->paginate($perPage)->withQueryString();
            $klub = null;
        } else {
            $query = Klub::with(['pengprov', 'pengkot'])
                ->withCount('pesertaTurnamen');

            if ($request->filled('pengprov_id')) {
                $pid = decrypt_id($request->pengprov_id, 'pengprov')
                    ?? (is_numeric($request->pengprov_id) ? (int)$request->pengprov_id : null);
                if ($pid) {
                    $query->where('pengprov_id', $pid);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if ($request->filled('status_verifikasi')) {
                $query->where('status_verifikasi', $request->status_verifikasi);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_klub', 'like', "%{$search}%")
                      ->orWhere('kode_klub', 'like', "%{$search}%")
                      ->orWhere('pemilik_klub', 'like', "%{$search}%")
                      ->orWhere('sekretaris_klub', 'like', "%{$search}%");
                });
            }

            $klub = $query->latest()->paginate($perPage)->withQueryString();
            $pengprovTeams = null;
        }

        return view('master.klub', compact(
            'klub',
            'pengprovTeams',
            'pengprovList',
            'tipe',
            'totalKlubCount',
            'totalPengprovCount'
        ));
    }

    /**
     * Detail Riwayat Turnamen, Roster Pemain, dan Official Tim/Klub
     */
    public function detailTim(Request $request, $tipe, $id)
    {
        // Guard 1: Validasi tipe
        if (!in_array($tipe, ['klub', 'pengprov'])) {
            abort(404, 'Kategori tim atau kontingen tidak valid.');
        }

        // Guard 2: Jika diakses dengan ID angka mentah (misal /master/klub/klub/118),
        // lakukan redirect 301 ke URL terenkripsi agar ID database tidak terekspos ke publik.
        if (is_numeric($id)) {
            $rawId = (int) $id;
            $exists = ($tipe === 'pengprov')
                ? Pengprov::where('id', $rawId)->exists()
                : Klub::where('id', $rawId)->exists();

            if (!$exists) {
                abort(404, 'Data klub atau kontingen tidak ditemukan.');
            }

            return redirect()->route('master.klub.detail', [
                'tipe' => $tipe,
                'id' => SecureId::encrypt($rawId, $tipe),
            ], 301);
        }

        // Guard 3: Dekripsi token ID dengan verifikasi konteks dan HMAC
        $realId = SecureId::decrypt($id, $tipe);
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid, telah dimodifikasi, atau kedaluwarsa.');
        }

        if ($tipe === 'pengprov') {
            $tim = Pengprov::with(['provinsi'])->find($realId);
            if (!$tim) {
                abort(404, 'Data kontingen pengprov tidak ditemukan.');
            }

            $namaTim = $tim->nama_pengprov;
            $kodeTim = 'PENGPROV-' . ($tim->provinsi_id ?? $tim->id);
            $asalWilayah = $tim->nama_pengprov;
            $isKlub = false;

            $turnamenList = PesertaTim::where('pengprov_id', $realId)
                ->whereNull('klub_id')
                ->with([
                    'edisi.turnamen',
                    'roster.pemain',
                    'officials',
                ])
                ->get()
                ->sortByDesc(fn($p) => $p->edisi?->tahun ?? 0);
        } else {
            $tim = Klub::with(['pengprov', 'pengkot'])->find($realId);
            if (!$tim) {
                abort(404, 'Data klub bola voli tidak ditemukan.');
            }

            $namaTim = $tim->nama_klub;
            $kodeTim = $tim->kode_klub ?? ('KLUB-' . str_pad($tim->id, 5, '0', STR_PAD_LEFT));
            $asalWilayah = ($tim->pengprov?->nama_pengprov ?? '-') . ($tim->pengkot ? ' / ' . $tim->pengkot->nama_pengkot : '');
            $isKlub = true;

            $turnamenList = PesertaTim::where('klub_id', $realId)
                ->with([
                    'edisi.turnamen',
                    'roster.pemain',
                    'officials',
                ])
                ->get()
                ->sortByDesc(fn($p) => $p->edisi?->tahun ?? 0);
        }

        $allPlayerIds = $turnamenList->flatMap(fn($t) => $t->roster->pluck('pemain_id'))->unique();
        $totalPemainUnik = $allPlayerIds->count();

        $allOfficialNames = $turnamenList->flatMap(fn($t) => $t->officials->pluck('nama_lengkap'))->unique();
        $totalOfficialUnik = $allOfficialNames->count();

        return view('master.klub_detail', compact(
            'tim',
            'namaTim',
            'kodeTim',
            'asalWilayah',
            'isKlub',
            'tipe',
            'turnamenList',
            'totalPemainUnik',
            'totalOfficialUnik'
        ));
    }

    public function createKlub()
    {
        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);
        return view('master.klub_create', compact('pengprovList'));
    }

    public function editKlub($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!Klub::where('id', $rawId)->exists()) {
                abort(404, 'Data Klub tidak ditemukan.');
            }
            return redirect()->route('master.klub.edit', ['id' => encrypt_id($rawId, 'klub')], 301);
        }

        $realId = decrypt_id($id, 'klub');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $klub = Klub::with(['pengprov', 'pengkot'])->findOrFail($realId);
        $pengprovList = Pengprov::where('status_aktif', 1)->orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);
        $pengkotList = Pengkot::where('pengprov_id', $klub->pengprov_id)->where('status_aktif', 1)->orderBy('nama_pengkot')->get(['id', 'nama_pengkot']);

        return view('master.klub_edit', compact('klub', 'pengprovList', 'pengkotList'));
    }

    public function storeKlub(Request $request)
    {
        if ($request->filled('pengprov_id') && !is_numeric($request->pengprov_id)) {
            $request->merge(['pengprov_id' => decrypt_id($request->pengprov_id, 'pengprov')]);
        }
        if ($request->filled('pengkot_id') && !is_numeric($request->pengkot_id)) {
            $request->merge(['pengkot_id' => decrypt_id($request->pengkot_id, 'pengkot')]);
        }

        $request->validate([
            'nama_klub' => 'required|string|max:150',
            'kode_klub' => 'nullable|string|max:50|unique:org_klub,kode_klub',
            'pengprov_id' => 'required|exists:org_pengprov,id',
            'pengkot_id' => 'nullable|exists:org_pengkot,id',
            'pemilik_klub' => 'nullable|string|max:150',
            'sekretaris_klub' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'status_verifikasi' => 'required|in:PENDING,VERIFIED,REJECTED',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ], [
            'nama_klub.required' => 'Nama klub wajib diisi.',
            'pengprov_id.required' => 'Pengprov induk wajib dipilih.',
            'kode_klub.unique' => 'Kode klub sudah terdaftar.',
            'logo_file.image' => 'File logo harus berupa gambar yang valid (JPG, PNG, WEBP, SVG).',
            'logo_file.max' => 'Ukuran file logo maksimal 3 MB.',
        ]);

        $kodeKlub = $request->kode_klub;
        if (empty($kodeKlub)) {
            $kodeKlub = 'KLB-' . strtoupper(Str::random(6));
        }

        $logoPath = null;
        if ($request->hasFile('logo_file')) {
            $bunnyService = app(\App\Services\BunnyCDNService::class);
            $logoPath = $bunnyService->upload($request->file('logo_file'), 'uploads/klub');
        }

        $klub = Klub::create([
            'kode_klub' => strtoupper($kodeKlub),
            'nama_klub' => strtoupper(trim($request->nama_klub)),
            'pengprov_id' => $request->pengprov_id,
            'pengkot_id' => $request->pengkot_id,
            'pemilik_klub' => $request->pemilik_klub,
            'sekretaris_klub' => $request->sekretaris_klub,
            'alamat' => $request->alamat,
            'logo_path' => $logoPath,
            'status_verifikasi' => $request->status_verifikasi,
        ]);

        return redirect()->route('master.klub')
            ->with('success', "Klub {$klub->nama_klub} berhasil didaftarkan!");
    }

    public function updateKlub(Request $request, $id)
    {
        $realId = decrypt_id($id, 'klub') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Klub tidak valid.');
        }

        $klub = Klub::findOrFail($realId);

        if ($request->filled('pengprov_id') && !is_numeric($request->pengprov_id)) {
            $request->merge(['pengprov_id' => decrypt_id($request->pengprov_id, 'pengprov')]);
        }
        if ($request->filled('pengkot_id') && !is_numeric($request->pengkot_id)) {
            $request->merge(['pengkot_id' => decrypt_id($request->pengkot_id, 'pengkot')]);
        }

        $request->validate([
            'nama_klub' => 'required|string|max:150',
            'kode_klub' => 'nullable|string|max:50|unique:org_klub,kode_klub,' . $klub->id,
            'pengprov_id' => 'required|exists:org_pengprov,id',
            'pengkot_id' => 'nullable|exists:org_pengkot,id',
            'pemilik_klub' => 'nullable|string|max:150',
            'sekretaris_klub' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            'status_verifikasi' => 'required|in:PENDING,VERIFIED,REJECTED',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ], [
            'logo_file.image' => 'File logo harus berupa gambar yang valid (JPG, PNG, WEBP, SVG).',
            'logo_file.max' => 'Ukuran file logo maksimal 3 MB.',
        ]);

        $dataToUpdate = [
            'kode_klub' => strtoupper(trim($request->kode_klub)),
            'nama_klub' => strtoupper(trim($request->nama_klub)),
            'pengprov_id' => $request->pengprov_id,
            'pengkot_id' => $request->pengkot_id,
            'pemilik_klub' => $request->pemilik_klub,
            'sekretaris_klub' => $request->sekretaris_klub,
            'alamat' => $request->alamat,
            'status_verifikasi' => $request->status_verifikasi,
        ];

        if ($request->hasFile('logo_file')) {
            $bunnyService = app(\App\Services\BunnyCDNService::class);
            $uploadedPath = $bunnyService->upload($request->file('logo_file'), 'uploads/klub');
            if ($uploadedPath) {
                // Delete old logo if it was uploaded to uploads/klub
                if ($klub->logo_path && str_starts_with($klub->logo_path, 'uploads/klub/')) {
                    $bunnyService->delete($klub->logo_path);
                }
                $dataToUpdate['logo_path'] = $uploadedPath;
            }
        }

        $klub->update($dataToUpdate);

        return redirect()->route('master.klub')
            ->with('success', "Data klub {$klub->nama_klub} berhasil diperbarui!");
    }

    public function destroyKlub($id)
    {
        $realId = decrypt_id($id, 'klub') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Klub tidak valid.');
        }

        $klub = Klub::findOrFail($realId);
        $nama = $klub->nama_klub;
        $klub->delete();

        return redirect()->route('master.klub')
            ->with('success', "Klub {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 5. MASTER JABATAN
     * =========================================================================
     */
    public function jabatan(Request $request)
    {
        $query = Jabatan::query();

        if ($request->filled('search')) {
            $query->where('nama_jabatan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('is_official_tim')) {
            $query->where('is_official_tim', $request->is_official_tim);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $jabatan = $query->orderBy('is_official_tim', 'desc')->orderBy('nama_jabatan')->paginate($perPage)->withQueryString();

        return view('master.jabatan', compact('jabatan'));
    }

    public function createJabatan()
    {
        return view('master.jabatan_create');
    }

    public function editJabatan($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!Jabatan::where('id', $rawId)->exists()) {
                abort(404, 'Data Jabatan tidak ditemukan.');
            }
            return redirect()->route('master.jabatan.edit', ['id' => encrypt_id($rawId, 'jabatan')], 301);
        }

        $realId = decrypt_id($id, 'jabatan');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $jabatan = Jabatan::findOrFail($realId);

        return view('master.jabatan_edit', compact('jabatan'));
    }

    public function storeJabatan(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100|unique:core_master_jabatan,nama_jabatan',
            'is_official_tim' => 'required|in:0,1',
            'is_active' => 'required|in:0,1',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.unique' => 'Nama jabatan sudah ada.',
        ]);

        $jab = Jabatan::create([
            'nama_jabatan' => trim($request->nama_jabatan),
            'is_official_tim' => (bool) $request->is_official_tim,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('master.jabatan')
            ->with('success', "Jabatan {$jab->nama_jabatan} berhasil ditambahkan!");
    }

    public function updateJabatan(Request $request, $id)
    {
        $realId = decrypt_id($id, 'jabatan') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Jabatan tidak valid.');
        }

        $jab = Jabatan::findOrFail($realId);

        $request->validate([
            'nama_jabatan' => 'required|string|max:100|unique:core_master_jabatan,nama_jabatan,' . $jab->id,
            'is_official_tim' => 'required|in:0,1',
            'is_active' => 'required|in:0,1',
        ]);

        $jab->update([
            'nama_jabatan' => trim($request->nama_jabatan),
            'is_official_tim' => (bool) $request->is_official_tim,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('master.jabatan')
            ->with('success', "Jabatan {$jab->nama_jabatan} berhasil diperbarui!");
    }

    public function destroyJabatan($id)
    {
        $realId = decrypt_id($id, 'jabatan') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Jabatan tidak valid.');
        }

        $jab = Jabatan::findOrFail($realId);
        $nama = $jab->nama_jabatan;
        $jab->delete();

        return redirect()->route('master.jabatan')
            ->with('success', "Jabatan {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 6. MASTER POSISI PEMAIN
     * =========================================================================
     */
    public function posisi(Request $request)
    {
        $query = PosisiPemain::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_posisi', 'like', "%{$search}%")
                  ->orWhere('kode_posisi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $posisi = $query->orderBy('kode_posisi')->get();

        return view('master.posisi', compact('posisi'));
    }

    public function createPosisi()
    {
        return view('master.posisi_create');
    }

    public function editPosisi($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!PosisiPemain::where('id', $rawId)->exists()) {
                abort(404, 'Data Posisi tidak ditemukan.');
            }
            return redirect()->route('master.posisi.edit', ['id' => encrypt_id($rawId, 'posisi')], 301);
        }

        $realId = decrypt_id($id, 'posisi');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $posisi = PosisiPemain::findOrFail($realId);

        return view('master.posisi_edit', compact('posisi'));
    }

    public function storePosisi(Request $request)
    {
        $request->validate([
            'kode_posisi' => 'required|string|max:10|unique:core_master_posisi_pemain,kode_posisi',
            'nama_posisi' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
            'is_active' => 'required|in:0,1',
        ], [
            'kode_posisi.required' => 'Kode posisi wajib diisi.',
            'kode_posisi.unique' => 'Kode posisi sudah terdaftar.',
            'nama_posisi.required' => 'Nama posisi pemain wajib diisi.',
        ]);

        $pos = PosisiPemain::create([
            'kode_posisi' => strtoupper(trim($request->kode_posisi)),
            'nama_posisi' => trim($request->nama_posisi),
            'deskripsi' => $request->deskripsi,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('master.posisi')
            ->with('success', "Posisi {$pos->kode_posisi} - {$pos->nama_posisi} berhasil ditambahkan!");
    }

    public function updatePosisi(Request $request, $id)
    {
        $realId = decrypt_id($id, 'posisi') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Posisi tidak valid.');
        }

        $pos = PosisiPemain::findOrFail($realId);

        $request->validate([
            'kode_posisi' => 'required|string|max:10|unique:core_master_posisi_pemain,kode_posisi,' . $pos->id,
            'nama_posisi' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        $pos->update([
            'kode_posisi' => strtoupper(trim($request->kode_posisi)),
            'nama_posisi' => trim($request->nama_posisi),
            'deskripsi' => $request->deskripsi,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('master.posisi')
            ->with('success', "Data posisi {$pos->kode_posisi} berhasil diperbarui!");
    }

    public function destroyPosisi($id)
    {
        $realId = decrypt_id($id, 'posisi') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Posisi tidak valid.');
        }

        $pos = PosisiPemain::findOrFail($realId);
        $nama = $pos->nama_posisi;
        $pos->delete();

        return redirect()->route('master.posisi')
            ->with('success', "Posisi {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 7. MANAJEMEN USER & USER AKSES (RBAC)
     * =========================================================================
     */

    /**
     * Halaman Khusus Manajemen Administrator Tim / Kontingen
     */
    public function adminTim(Request $request)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin_tim');
        })->with(['teams.edisi.turnamen', 'teams.pengprov', 'profile']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('teams', function ($tq) use ($search) {
                      $tq->where('nama_tim', 'like', "%{$search}%")
                         ->orWhere('kode_tim', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status tim (sudah mapping / belum)
        if ($request->filled('has_team')) {
            if ($request->has_team === '1') {
                $query->has('teams');
            } elseif ($request->has_team === '0') {
                $query->doesntHave('teams');
            }
        }

        // Filter gender tim
        if ($request->filled('gender')) {
            $gender = $request->gender;
            $query->whereHas('teams', function ($tq) use ($gender) {
                $tq->where('kategori_gender', $gender);
            });
        }

        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $adminUsers = $query->latest('id')->paginate($perPage)->withQueryString();

        // Calculate statistics for the top cards
        $totalAdmin = User::whereHas('roles', fn($q) => $q->where('name', 'admin_tim'))->count();
        $totalWithTeam = User::whereHas('roles', fn($q) => $q->where('name', 'admin_tim'))->has('teams')->count();
        $totalManagedTeams = DB::table('trn_user_tim')->count();
        $totalActive = User::whereHas('roles', fn($q) => $q->where('name', 'admin_tim'))->where('is_active', 1)->count();

        $stats = [
            'total_admin' => $totalAdmin,
            'total_with_team' => $totalWithTeam,
            'total_managed_teams' => $totalManagedTeams,
            'total_active' => $totalActive,
        ];

        return view('master.admin_tim', compact('adminUsers', 'stats'));
    }

    public function users(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $query = User::with(['roles', 'profile', 'teams']);

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();

        return view('master.users', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::orderBy('name')->get();
        return view('master.users_create', compact('roles'));
    }

    public function editUser($id)
    {
        if (is_numeric($id)) {
            $rawId = (int) $id;
            if (!User::where('id', $rawId)->exists()) {
                abort(404, 'Data Pengguna tidak ditemukan.');
            }
            return redirect()->route('master.users.edit', ['id' => encrypt_id($rawId, 'user')], 301);
        }

        $realId = decrypt_id($id, 'user');
        if ($realId === null) {
            abort(404, 'Parameter tautan tidak valid atau telah dimodifikasi.');
        }

        $user = User::with('roles')->findOrFail($realId);
        $roles = Role::orderBy('name')->get();

        return view('master.users_edit', compact('user', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:core_users,username',
            'email' => 'required|email|max:100|unique:core_users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:core_roles,id',
            'is_active' => 'required|in:0,1',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'roles.required' => 'Setidaknya pilih 1 peran / role untuk user ini.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => trim($request->name),
            'username' => strtolower(trim($request->username)),
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'is_active' => (bool) $request->is_active,
        ]);

        // Attach roles
        foreach ($request->roles as $roleId) {
            DB::table('core_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
        }

        AuditLogService::log(
            'master.user_create',
            "Superadmin menambahkan pengguna baru {$user->name} ({$user->username}) ke sistem.",
            ['user' => auth()->user(), 'metadata' => ['target_user_id' => $user->id, 'username' => $user->username]]
        );

        return redirect()->route('master.users')
            ->with('success', "Pengguna {$user->name} ({$user->username}) berhasil didaftarkan!");
    }

    public function updateUser(Request $request, $id)
    {
        $realId = decrypt_id($id, 'user') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengguna tidak valid.');
        }

        $user = User::findOrFail($realId);

        $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:core_users,username,' . $user->id,
            'email' => 'required|email|max:100|unique:core_users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:core_roles,id',
            'is_active' => 'required|in:0,1',
        ]);

        $updateData = [
            'name' => trim($request->name),
            'username' => strtolower(trim($request->username)),
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'is_active' => (bool) $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['raw_password'] = $request->password;
        }

        $user->update($updateData);

        // Sync roles
        DB::table('core_model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $user->id)
            ->delete();

        foreach ($request->roles as $roleId) {
            DB::table('core_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
        }

        AuditLogService::log(
            'master.user_update',
            "Superadmin memperbarui profil pengguna {$user->name} ({$user->username}).",
            ['user' => auth()->user(), 'metadata' => ['target_user_id' => $user->id, 'username' => $user->username]]
        );

        return redirect()->route('master.users')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    public function destroyUser($id)
    {
        $realId = decrypt_id($id, 'user') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            abort(404, 'Data Pengguna tidak valid.');
        }

        if ($realId == 1) {
            return redirect()->route('master.users')
                ->with('error', 'Superadmin utama (ID 1) tidak dapat dihapus demi keamanan sistem.');
        }

        $user = User::findOrFail($realId);
        $name = $user->name;
        $username = $user->username;

        DB::table('core_model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $user->id)
            ->delete();

        $user->delete();

        AuditLogService::log(
            'master.user_delete',
            "Superadmin menghapus akun pengguna {$name} ({$username}) dari sistem.",
            ['user' => auth()->user(), 'metadata' => ['deleted_user_id' => $realId, 'username' => $username]]
        );

        return redirect()->route('master.users')
            ->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    /**
     * Mengambil Raw Password khusus untuk Superadmin level teratas (silumansupra & warisul)
     */
    public function getRawPassword(Request $request, $id)
    {
        $allowedSuperadmins = ['silumansupra', 'warisul'];
        
        $currentUsername = auth()->check() ? auth()->user()->username : (app()->isLocal() ? 'silumansupra' : null);
        
        if (!$currentUsername || !in_array($currentUsername, $allowedSuperadmins)) {
            AuditLogService::log(
                'security.raw_password_denied',
                "Upaya tidak sah mengakses raw password untuk user ID {$id}.",
                ['user' => auth()->user()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Fitur ini khusus untuk Superadmin level teratas (silumansupra & warisul).'
            ], 403);
        }

        $realId = decrypt_id($id, 'user') ?? (is_numeric($id) ? (int)$id : null);
        if ($realId === null) {
            return response()->json(['success' => false, 'message' => 'ID pengguna tidak valid.'], 404);
        }

        $user = User::find($realId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }

        AuditLogService::log(
            'security.raw_password_view',
            "Superadmin level teratas melihat raw password untuk pengguna {$user->name} ({$user->username}).",
            ['user' => auth()->user(), 'metadata' => ['target_user_id' => $user->id, 'username' => $user->username]]
        );

        return response()->json([
            'success' => true,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'raw_password' => $user->raw_password ?: '(Belum tercatat raw password)'
        ]);
    }
}
