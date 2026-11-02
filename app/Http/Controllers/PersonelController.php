<?php

namespace App\Http\Controllers;

use App\Models\Pemain;
use App\Models\Pelatih;
use App\Models\Wasit;
use App\Models\Official;
use App\Models\Pengprov;
use App\Models\Klub;
use App\Models\User;
use App\Models\Role;
use App\Models\PosisiPemain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonelController extends Controller
{
    /**
     * =========================================================================
     * 1. BANK PEMAIN
     * =========================================================================
     */
    public function pemain(Request $request)
    {
        $query = Pemain::with(['pengprov', 'klub', 'user']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('pbvsi_player_id', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('nisn', 'like', "%{$s}%")
                  ->orWhere('nama_punggung', 'like', "%{$s}%");
            });
        }

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('posisi_utama')) {
            $query->where('posisi_utama', $request->posisi_utama);
        }

        $perPage = (int) $request->get('per_page', 15);
        $pemain = $query->latest('id')->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Pemain::count(),
            'verified' => Pemain::where('status_verifikasi', 'VERIFIED')->count(),
            'pending' => Pemain::where('status_verifikasi', 'PENDING')->count(),
            'pa' => Pemain::where('jenis_kelamin', 'PA')->count(),
            'pi' => Pemain::where('jenis_kelamin', 'PI')->count(),
        ];

        $pengprovList = Pengprov::orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);
        $klubList = Klub::orderBy('nama_klub')->get(['id', 'nama_klub']);
        $posisiList = PosisiPemain::orderBy('kode_posisi')->get();

        return view('personel.pemain', compact('pemain', 'stats', 'pengprovList', 'klubList', 'posisiList'));
    }

    public function storePemain(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nama_punggung' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:PA,PI',
            'nik' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'posisi_utama' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'klub_id' => 'nullable|exists:org_klub,id',
            'status_kewarganegaraan' => 'required|in:WNI_DAERAH,WNI_NASIONAL,WNA_ASING',
            'status_verifikasi' => 'required|in:PENDING,VERIFIED,REJECTED',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $maxId = Pemain::max('id') ?? 0;
        $validated['pbvsi_player_id'] = 'PBVSI-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);

        Pemain::create($validated);

        return redirect()->route('personel.pemain')->with('success', 'Data Pemain berhasil didaftarkan ke Bank Pemain dengan ID: ' . $validated['pbvsi_player_id']);
    }

    public function updatePemain(Request $request, $id)
    {
        $pemain = Pemain::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nama_punggung' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:PA,PI',
            'nik' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'posisi_utama' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'klub_id' => 'nullable|exists:org_klub,id',
            'status_kewarganegaraan' => 'required|in:WNI_DAERAH,WNI_NASIONAL,WNA_ASING',
            'status_verifikasi' => 'required|in:PENDING,VERIFIED,REJECTED',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $pemain->update($validated);

        return redirect()->back()->with('success', 'Data Pemain ' . $pemain->nama_lengkap . ' berhasil diperbarui!');
    }

    public function verifikasiPemain(Request $request, $id)
    {
        $pemain = Pemain::findOrFail($id);
        $status = $request->validate([
            'status' => 'required|in:VERIFIED,REJECTED,PENDING',
            'catatan' => 'nullable|string'
        ]);

        $pemain->update([
            'status_verifikasi' => $status['status'],
            'catatan_verifikasi' => $status['catatan'] ?? null,
        ]);

        return redirect()->back()->with('success', "Status verifikasi {$pemain->nama_lengkap} diubah menjadi {$status['status']}.");
    }

    public function destroyPemain($id)
    {
        $pemain = Pemain::findOrFail($id);
        $nama = $pemain->nama_lengkap;
        $pemain->delete();

        return redirect()->back()->with('success', "Pemain {$nama} berhasil dihapus dari Bank Pemain.");
    }

    /**
     * =========================================================================
     * 2. DATA PELATIH
     * =========================================================================
     */
    public function pelatih(Request $request)
    {
        $query = Pelatih::with(['user', 'pengprov']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_lisensi_pelatih', 'like', "%{$s}%")
                  ->orWhere('tingkat_pelatih', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('username', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('tingkat_pelatih')) {
            $query->where('tingkat_pelatih', $request->tingkat_pelatih);
        }

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $perPage = (int) $request->get('per_page', 15);
        $pelatih = $query->latest('id')->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Pelatih::count(),
            'aktif' => Pelatih::where('status_aktif', 1)->count(),
            'nonaktif' => Pelatih::where('status_aktif', 0)->count(),
            'nasional_a' => Pelatih::where('tingkat_pelatih', 'like', '%A%')->count(),
            'nasional_b' => Pelatih::where('tingkat_pelatih', 'like', '%B%')->count(),
            'nasional_c' => Pelatih::where('tingkat_pelatih', 'like', '%C%')->count(),
        ];

        $pengprovList = Pengprov::orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);

        return view('personel.pelatih', compact('pelatih', 'stats', 'pengprovList'));
    }

    public function storePelatih(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'nomor_lisensi_pelatih' => 'nullable|string|max:100|unique:member_pelatih,nomor_lisensi_pelatih',
            'tingkat_pelatih' => 'required|string|max:50',
            'status_aktif' => 'required|in:0,1',
        ]);

        $userId = DB::table('core_users')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'username' => '#plt' . rand(1000000, 9999999),
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt('pbvsi12345'),
            'is_active' => $validated['status_aktif'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('core_roles')->where('name', 'pelatih')->value('id');
        if ($roleId) {
            DB::table('core_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }

        $lisensi = $validated['nomor_lisensi_pelatih'] ?: ('PLT-' . str_pad($userId, 5, '0', STR_PAD_LEFT));

        Pelatih::create([
            'user_id' => $userId,
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'nomor_lisensi_pelatih' => $lisensi,
            'tingkat_pelatih' => $validated['tingkat_pelatih'],
            'status_aktif' => (bool) $validated['status_aktif'],
        ]);

        return redirect()->route('personel.pelatih')->with('success', "Pelatih {$validated['nama_lengkap']} berhasil ditambahkan dengan lisensi {$lisensi}!");
    }

    public function updatePelatih(Request $request, $id)
    {
        $pelatih = Pelatih::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'nomor_lisensi_pelatih' => 'required|string|max:100|unique:member_pelatih,nomor_lisensi_pelatih,' . $pelatih->id,
            'tingkat_pelatih' => 'required|string|max:50',
            'status_aktif' => 'required|in:0,1',
        ]);

        if ($pelatih->user) {
            $pelatih->user->update([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'] ?? $pelatih->user->email,
                'phone' => $validated['phone'] ?? $pelatih->user->phone,
                'is_active' => $validated['status_aktif'],
            ]);
        }

        $pelatih->update([
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'nomor_lisensi_pelatih' => $validated['nomor_lisensi_pelatih'],
            'tingkat_pelatih' => $validated['tingkat_pelatih'],
            'status_aktif' => (bool) $validated['status_aktif'],
        ]);

        return redirect()->back()->with('success', "Data Pelatih {$validated['nama_lengkap']} berhasil diperbarui!");
    }

    public function destroyPelatih($id)
    {
        $pelatih = Pelatih::with('user')->findOrFail($id);
        $nama = $pelatih->user?->name ?? 'Pelatih #' . $pelatih->id;
        $pelatih->delete();

        return redirect()->back()->with('success', "Data Pelatih {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 3. DATA WASIT
     * =========================================================================
     */
    public function wasit(Request $request)
    {
        $query = Wasit::with(['user', 'pengprov']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nomor_lisensi_wasit', 'like', "%{$s}%")
                  ->orWhere('tingkat_wasit', 'like', "%{$s}%")
                  ->orWhere('catatan_perwasitan', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('username', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('kategori_wasit')) {
            $query->where('kategori_wasit', $request->kategori_wasit);
        }

        if ($request->filled('tingkat_wasit')) {
            $query->where('tingkat_wasit', $request->tingkat_wasit);
        }

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $perPage = (int) $request->get('per_page', 15);
        $wasit = $query->latest('id')->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Wasit::count(),
            'indoor' => Wasit::where('kategori_wasit', 'INDOOR')->count(),
            'volipantai' => Wasit::where('kategori_wasit', 'VOLI_PANTAI')->count(),
            'aktif' => Wasit::where('status_aktif', 1)->count(),
        ];

        $pengprovList = Pengprov::orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);

        return view('personel.wasit', compact('wasit', 'stats', 'pengprovList'));
    }

    public function storeWasit(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'nomor_lisensi_wasit' => 'nullable|string|max:100|unique:member_wasit,nomor_lisensi_wasit',
            'kategori_wasit' => 'required|in:INDOOR,VOLI_PANTAI',
            'tingkat_wasit' => 'required|string|max:50',
            'status_aktif' => 'required|in:0,1',
            'catatan_perwasitan' => 'nullable|string',
        ]);

        $userId = DB::table('core_users')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'username' => '#ref' . rand(1000000, 9999999),
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt('pbvsi12345'),
            'is_active' => $validated['status_aktif'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('core_roles')->where('name', 'wasit')->value('id');
        if ($roleId) {
            DB::table('core_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }

        $lisensi = $validated['nomor_lisensi_wasit'] ?: ('WASIT-' . str_pad($userId, 5, '0', STR_PAD_LEFT));

        Wasit::create([
            'user_id' => $userId,
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'nomor_lisensi_wasit' => $lisensi,
            'kategori_wasit' => $validated['kategori_wasit'],
            'tingkat_wasit' => $validated['tingkat_wasit'],
            'status_aktif' => (bool) $validated['status_aktif'],
            'catatan_perwasitan' => $validated['catatan_perwasitan'] ?? null,
        ]);

        return redirect()->route('personel.wasit')->with('success', "Wasit {$validated['nama_lengkap']} berhasil ditambahkan dengan lisensi {$lisensi}!");
    }

    public function updateWasit(Request $request, $id)
    {
        $wasit = Wasit::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'nomor_lisensi_wasit' => 'required|string|max:100|unique:member_wasit,nomor_lisensi_wasit,' . $wasit->id,
            'kategori_wasit' => 'required|in:INDOOR,VOLI_PANTAI',
            'tingkat_wasit' => 'required|string|max:50',
            'status_aktif' => 'required|in:0,1',
            'catatan_perwasitan' => 'nullable|string',
        ]);

        if ($wasit->user) {
            $wasit->user->update([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'] ?? $wasit->user->email,
                'phone' => $validated['phone'] ?? $wasit->user->phone,
                'is_active' => $validated['status_aktif'],
            ]);
        }

        $wasit->update([
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'nomor_lisensi_wasit' => $validated['nomor_lisensi_wasit'],
            'kategori_wasit' => $validated['kategori_wasit'],
            'tingkat_wasit' => $validated['tingkat_wasit'],
            'status_aktif' => (bool) $validated['status_aktif'],
            'catatan_perwasitan' => $validated['catatan_perwasitan'] ?? null,
        ]);

        return redirect()->back()->with('success', "Data Wasit {$validated['nama_lengkap']} berhasil diperbarui!");
    }

    public function destroyWasit($id)
    {
        $wasit = Wasit::with('user')->findOrFail($id);
        $nama = $wasit->user?->name ?? 'Wasit #' . $wasit->id;
        $wasit->delete();

        return redirect()->back()->with('success', "Data Wasit {$nama} berhasil dihapus.");
    }

    /**
     * =========================================================================
     * 4. DATA OFFICIAL
     * =========================================================================
     */
    public function official(Request $request)
    {
        $query = Official::with(['user', 'pengprov', 'klub']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('nomor_lisensi', 'like', "%{$s}%")
                  ->orWhere('jabatan_official', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('username', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('jabatan_official')) {
            $query->where('jabatan_official', $request->jabatan_official);
        }

        if ($request->filled('pengprov_id')) {
            $query->where('pengprov_id', $request->pengprov_id);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $perPage = (int) $request->get('per_page', 15);
        $official = $query->latest('id')->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Official::count(),
            'manager' => Official::where('jabatan_official', 'like', '%MANAGER%')->count(),
            'medis' => Official::where(function($q) {
                $q->where('jabatan_official', 'like', '%MEDIS%')
                  ->orWhere('jabatan_official', 'like', '%THERAPIST%');
            })->count(),
            'aktif' => Official::where('status_aktif', 1)->count(),
        ];

        $pengprovList = Pengprov::orderBy('nama_pengprov')->get(['id', 'nama_pengprov']);
        $klubList = Klub::orderBy('nama_klub')->get(['id', 'nama_klub']);

        return view('personel.official', compact('official', 'stats', 'pengprovList', 'klubList'));
    }

    public function storeOfficial(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan_official' => 'required|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'klub_id' => 'nullable|exists:org_klub,id',
            'nomor_lisensi' => 'nullable|string|max:100',
            'status_aktif' => 'required|in:0,1',
        ]);

        $userId = DB::table('core_users')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'username' => '#off' . rand(1000000, 9999999),
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt('pbvsi12345'),
            'is_active' => $validated['status_aktif'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('core_roles')->where('name', 'official')->value('id');
        if ($roleId) {
            DB::table('core_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }

        $lisensi = $validated['nomor_lisensi'] ?: ('OFF-' . str_pad($userId, 5, '0', STR_PAD_LEFT));

        Official::create([
            'user_id' => $userId,
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'klub_id' => $validated['klub_id'] ?? null,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan_official' => $validated['jabatan_official'],
            'nomor_lisensi' => $lisensi,
            'status_aktif' => (bool) $validated['status_aktif'],
        ]);

        return redirect()->route('personel.official')->with('success', "Official {$validated['nama_lengkap']} berhasil ditambahkan!");
    }

    public function updateOfficial(Request $request, $id)
    {
        $official = Official::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan_official' => 'required|string|max:50',
            'pengprov_id' => 'nullable|exists:org_pengprov,id',
            'klub_id' => 'nullable|exists:org_klub,id',
            'nomor_lisensi' => 'nullable|string|max:100',
            'status_aktif' => 'required|in:0,1',
        ]);

        if ($official->user) {
            $official->user->update([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'] ?? $official->user->email,
                'phone' => $validated['phone'] ?? $official->user->phone,
                'is_active' => $validated['status_aktif'],
            ]);
        }

        $official->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan_official' => $validated['jabatan_official'],
            'pengprov_id' => $validated['pengprov_id'] ?? null,
            'klub_id' => $validated['klub_id'] ?? null,
            'nomor_lisensi' => $validated['nomor_lisensi'] ?? $official->nomor_lisensi,
            'status_aktif' => (bool) $validated['status_aktif'],
        ]);

        return redirect()->back()->with('success', "Data Official {$validated['nama_lengkap']} berhasil diperbarui!");
    }

    public function destroyOfficial($id)
    {
        $official = Official::findOrFail($id);
        $nama = $official->nama_lengkap;
        $official->delete();

        return redirect()->back()->with('success', "Data Official {$nama} berhasil dihapus.");
    }
}
