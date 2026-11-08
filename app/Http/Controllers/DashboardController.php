<?php

namespace App\Http\Controllers;

use App\Models\Turnamen;
use App\Models\Edisi;
use App\Models\Pemain;
use App\Models\Pengprov;
use App\Models\User;
use App\Models\PesertaTim;
use App\Models\Roster;
use App\Models\OfficialTim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Guard: Jika pengguna belum login, arahkan langsung ke halaman login
        if (!$user) {
            return redirect()->route('login');
        }

        // Jika user adalah Admin Tim (dan bukan Superadmin), arahkan ke Dashboard Tim
        if ($user && $user->hasRole('admin_tim') && !$user->hasRole('superadmin')) {
            return $this->dashboardAdminTim($request, $user);
        }

        // Jika user login dan BUKAN Superadmin (Pemain, Wasit, Pelatih, Pengprov, Klub, dll), arahkan ke Portal Anggota dalam pengembangan
        if ($user && !$user->hasRole('superadmin')) {
            return $this->dashboardPortalUnderDev($request, $user);
        }

        // Tampilan Dashboard Superadmin / Pengurus Pusat
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

    /**
     * Dashboard Khusus Administrator Tim / Kontingen
     */
    protected function dashboardAdminTim(Request $request, User $user)
    {
        $teams = $user->teams()->with(['edisi.turnamen', 'pengprov'])->get();

        if ($teams->isEmpty()) {
            return view('dashboard.admin_tim_no_team', compact('user'));
        }

        $activeTeamId = session('active_team_id', $teams->first()->id);
        $activeTeam = $teams->where('id', $activeTeamId)->first() ?? $teams->first();
        session(['active_team_id' => $activeTeam->id]);

        // Ambil Data Roster O-2 & Official Tim Aktif
        $roster = Roster::where('peserta_tim_id', $activeTeam->id)
            ->with('pemain')
            ->orderBy('nomor_punggung', 'asc')
            ->get();

        $captain = $roster->where('is_captain', 1)->first();
        $liberos = $roster->where('is_libero', 1);

        $officials = OfficialTim::where('peserta_tim_id', $activeTeam->id)
            ->orderBy('id', 'asc')
            ->get();

        // Simpan hitungan di session untuk badge sidebar
        session([
            'active_team_roster_count' => $roster->count(),
            'active_team_official_count' => $officials->count()
        ]);

        return view('dashboard.admin_tim', compact('user', 'teams', 'activeTeam', 'roster', 'captain', 'liberos', 'officials'));
    }

    /**
     * Dashboard Portal Anggota yang Sedang Dalam Tahap Pengembangan (Pemain, Pelatih, Wasit, Pengprov, dll.)
     */
    protected function dashboardPortalUnderDev(Request $request, User $user)
    {
        $role = $user->roles->first();

        return view('dashboard.portal_under_dev', compact('user', 'role'));
    }
}
