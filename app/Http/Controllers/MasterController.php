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
     * 1. Master Wilayah (Provinsi & Kabupaten)
     */
    public function wilayah(Request $request)
    {
        $provinsi = Provinsi::withCount('kabupaten')->orderBy('nama_provinsi')->get();
        
        $selectedProvId = $request->get('provinsi_id', $provinsi->first()?->id);
        $kabupaten = Kabupaten::where('provinsi_id', $selectedProvId)
            ->orderBy('nama_kabupaten')
            ->paginate(15)
            ->withQueryString();

        return view('master.wilayah', compact('provinsi', 'kabupaten', 'selectedProvId'));
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
