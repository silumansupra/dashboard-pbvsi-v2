<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Jabatan;
use App\Models\PosisiPemain;
use App\Models\Pengprov;
use App\Models\Pengkot;
use App\Models\Klub;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * 1. Master Wilayah (Provinsi & Kabupaten) - Read & Filter
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

        $kabupaten = $kabQuery->orderBy('nama_kabupaten')
            ->paginate(15)
            ->withQueryString();

        return view('master.wilayah', compact('provinsi', 'kabupaten', 'selectedProvId', 'activeProvinsi'));
    }

    /**
     * Simpan Provinsi Baru
     */
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

    /**
     * Update Provinsi
     */
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

    /**
     * Hapus Provinsi
     */
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

    /**
     * Simpan Kabupaten / Kota Baru
     */
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
        // Otomatis pastikan penamaan sesuai tipe jika belum diawali KOTA / KABUPATEN
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

    /**
     * Update Kabupaten / Kota
     */
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

    /**
     * Hapus Kabupaten / Kota
     */
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
     * 2. Master Jabatan
     */
    public function jabatan()
    {
        $jabatan = Jabatan::orderBy('is_official_tim', 'desc')->orderBy('nama_jabatan')->get();
        return view('master.jabatan', compact('jabatan'));
    }

    /**
     * 3. Master Posisi Pemain
     */
    public function posisi()
    {
        $posisi = PosisiPemain::orderBy('kode_posisi')->get();
        return view('master.posisi', compact('posisi'));
    }

    /**
     * 4. Master Pengprov
     */
    public function pengprov()
    {
        $pengprov = Pengprov::orderBy('nama_pengprov')->paginate(15);
        return view('master.pengprov', compact('pengprov'));
    }

    /**
     * 5. Master Pengkot
     */
    public function pengkot(Request $request)
    {
        $pengprovList = Pengprov::orderBy('nama_pengprov')->get();
        $query = Pengkot::with(['pengprov', 'kabupaten']);

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        $pengkot = $query->orderBy('nama_pengkot')->paginate(15)->withQueryString();
        return view('master.pengkot', compact('pengkot', 'pengprovList'));
    }

    /**
     * 6. Master Klub / Tim Terdaftar
     */
    public function klub()
    {
        $klub = Klub::with('pengprov')->latest()->paginate(15);
        $pengprovList = Pengprov::orderBy('nama_pengprov')->get();
        return view('master.klub', compact('klub', 'pengprovList'));
    }

    /**
     * 7. Manajemen User & User Akses (RBAC)
     */
    public function users(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $query = User::with(['roles', 'profile']);

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('master.users', compact('users', 'roles'));
    }
}
