<?php

namespace App\Http\Controllers;

use App\Models\Turnamen;
use App\Models\Edisi;
use App\Models\Pemain;
use App\Models\Pengprov;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pemain' => Pemain::count(),
            'total_pengprov' => Pengprov::count(),
            'total_users' => User::count(),
            'turnamen_aktif' => Edisi::whereIn('status_edisi', ['REGISTRASI', 'RUNNING'])->count(),
        ];

        // Edisi Kejurnas Umur
        $kejurnasEdisi = Edisi::with('turnamen')
            ->whereHas('turnamen', function ($q) {
                $q->where('kategori', 'KEJURNAS_UMUR');
            })
            ->orderBy('tahun', 'desc')
            ->get();

        // Turnamen Lainnya (Livoli, Kapolri Cup, Voli Pantai)
        $turnamenLain = Edisi::with('turnamen')
            ->whereHas('turnamen', function ($q) {
                $q->where('kategori', '!=', 'KEJURNAS_UMUR');
            })
            ->orderBy('tahun', 'desc')
            ->get();

        return view('dashboard.index', compact('stats', 'kejurnasEdisi', 'turnamenLain'));
    }
}
