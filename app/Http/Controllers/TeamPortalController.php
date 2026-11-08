<?php

namespace App\Http\Controllers;

use App\Models\PesertaTim;
use App\Models\Roster;
use App\Models\OfficialTim;
use App\Models\Pemain;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AuditLogService;
use App\Services\BunnyCDNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamPortalController extends Controller
{
    /**
     * Resolves user teams and active team context
     */
    protected function resolveTeamContext(Request $request): array
    {
        $user = Auth::user();
        $teams = $user->teams()->with(['edisi.turnamen', 'pengprov', 'klub'])->get();

        // Jika superadmin tetapi belum di-assign tim, sediakan tim untuk keperluan preview/administrasi
        if ($teams->isEmpty() && $user->hasRole('superadmin')) {
            $teams = PesertaTim::with(['edisi.turnamen', 'pengprov', 'klub'])->take(15)->get();
        }

        if ($teams->isEmpty()) {
            return [$teams, null];
        }

        $activeTeamId = session('active_team_id');
        $activeTeam = $teams->where('id', $activeTeamId)->first() ?? $teams->first();

        // Pastikan session aktif tersimpan
        session(['active_team_id' => $activeTeam->id]);

        return [$teams, $activeTeam];
    }

    /**
     * Switch Active Team
     */
    public function switchTeam(Request $request, $id)
    {
        $user = Auth::user();
        
        // Verifikasi kepemilikan tim kecuali superadmin
        if (!$user->hasRole('superadmin')) {
            $hasAccess = $user->teams()->where('trn_peserta_tim.id', $id)->exists();
            if (!$hasAccess) {
                return redirect()->route('dashboard')->withErrors(['error' => 'Anda tidak memiliki hak akses ke tim tersebut.']);
            }
        }

        $team = PesertaTim::findOrFail($id);
        session(['active_team_id' => $team->id]);

        $genderLabel = $team->kategori_gender == 'PA' ? 'Putra (PA)' : 'Putri (PI)';

        AuditLogService::log(
            'team.switch',
            "Pengguna {$user->name} beralih ke pengelolaan tim {$team->nama_tim} ({$genderLabel}).",
            ['user' => $user, 'metadata' => ['team_id' => $team->id, 'team_name' => $team->nama_tim]]
        );

        return redirect()->back()->with('success', "Berhasil beralih ke tim {$team->nama_tim} - {$genderLabel}.");
    }

    /**
     * Profil & Jersey Tim
     */
    public function profil(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return view('dashboard.admin_tim_no_team', compact('user'));
        }

        return view('tim.profil', compact('user', 'teams', 'activeTeam'));
    }

    /**
     * Update Profil & Jersey Tim
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'warna_jersey_1' => 'nullable|string|max:50',
            'warna_jersey_2' => 'nullable|string|max:50',
            'warna_jersey_3' => 'nullable|string|max:50',
            'warna_jersey_libero_1' => 'nullable|string|max:50',
            'warna_jersey_libero_2' => 'nullable|string|max:50',
            'logo_tim' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foto_skuad' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $activeTeam->warna_jersey_1 = $request->warna_jersey_1;
        $activeTeam->warna_jersey_2 = $request->warna_jersey_2;
        $activeTeam->warna_jersey_3 = $request->warna_jersey_3;
        $activeTeam->warna_jersey_libero_1 = $request->warna_jersey_libero_1;
        $activeTeam->warna_jersey_libero_2 = $request->warna_jersey_libero_2;

        // Handle upload logo lokal jika ada
        if ($request->hasFile('logo_tim')) {
            $path = $request->file('logo_tim')->store('teams/logos', 'public');
            $activeTeam->logo_tim_path = $path;
        }

        // Handle upload foto skuad jika ada
        if ($request->hasFile('foto_skuad')) {
            $path = $request->file('foto_skuad')->store('teams/skuad', 'public');
            $activeTeam->foto_skuad_path = $path;
        }

        $activeTeam->save();

        AuditLogService::log(
            'team.update_profile',
            "Pengguna {$user->name} memperbarui profil dan identitas jersey tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'team_name' => $activeTeam->nama_tim]]
        );

        return redirect()->back()->with('success', 'Profil dan identitas jersey tim berhasil diperbarui!');
    }

    /**
     * Daftar Pemain Tim & Penambahan
     */
    public function pemain(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return view('dashboard.admin_tim_no_team', compact('user'));
        }

        $roster = Roster::where('peserta_tim_id', $activeTeam->id)
            ->with(['pemain.klub', 'pemain.pengprov'])
            ->orderBy('nomor_punggung', 'asc')
            ->get();

        return view('tim.pemain', compact('user', 'teams', 'activeTeam', 'roster'));
    }

    /**
     * Tambah Pemain Baru ke Roster Tim
     */
    public function storePemain(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $currentCount = Roster::where('peserta_tim_id', $activeTeam->id)->count();
        if ($currentCount >= 14) {
            return redirect()->back()->withErrors(['error' => 'Batas maksimal roster telah tercapai (Maksimal 14 Pemain sesuai Regulasi PBVSI).']);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nomor_punggung' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                Rule::unique('trn_roster', 'nomor_punggung')->where('peserta_tim_id', $activeTeam->id),
            ],
            'posisi_bermain' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'nik' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'tinggi_cm' => 'nullable|integer|min:120|max:230',
            'berat_kg' => 'nullable|integer|min:30|max:150',
        ], [
            'nomor_punggung.unique' => 'Nomor punggung tersebut sudah dipakai oleh pemain lain di tim ini.',
        ]);

        $gender = $activeTeam->kategori_gender == 'PA' ? 'L' : 'P';
        $nik = trim($request->nik ?? '');
        $namaUpper = strtoupper(trim($request->nama_lengkap));

        // Pencarian pemain yang ada:
        // Khusus pemain usia dini, NIK '00000000000' atau kosong tidak memblokir pendaftaran,
        // dilakukan verifikasi silang via Nama Lengkap + Tanggal Lahir.
        $pemain = null;
        if (!empty($nik) && !str_starts_with($nik, '00000000')) {
            $pemain = Pemain::where('nik', $nik)->first();
        }

        if (!$pemain) {
            $pemain = Pemain::where('nama_lengkap', $namaUpper)
                ->where('tanggal_lahir', $request->tanggal_lahir)
                ->first();
        }

        if (!$pemain) {
            $pemain = Pemain::create([
                'nama_lengkap' => $namaUpper,
                'nik' => !empty($nik) ? $nik : '0000000000000000',
                'nisn' => $request->nisn,
                'jenis_kelamin' => $gender,
                'tempat_lahir' => $request->tempat_lahir ? strtoupper($request->tempat_lahir) : null,
                'tanggal_lahir' => $request->tanggal_lahir,
                'posisi_utama' => $request->posisi_bermain,
                'pengprov_id' => $activeTeam->pengprov_id,
                'klub_id' => $activeTeam->klub_id,
                'status_verifikasi' => 'SUBMITTED',
            ]);
        }

        // Cek apakah pemain ini sudah terdaftar di roster tim aktif
        $existsInRoster = Roster::where('peserta_tim_id', $activeTeam->id)
            ->where('pemain_id', $pemain->id)
            ->exists();

        if ($existsInRoster) {
            return redirect()->back()->withErrors(['error' => "Pemain {$pemain->nama_lengkap} sudah ada di dalam roster tim ini."]);
        }

        $isLibero = ($request->posisi_bermain === 'LIBERO') ? 1 : 0;

        Roster::create([
            'peserta_tim_id' => $activeTeam->id,
            'pemain_id' => $pemain->id,
            'nomor_punggung' => $request->nomor_punggung,
            'nama_jersey' => $pemain->nama_punggung ?: explode(' ', $namaUpper)[0],
            'posisi_bermain' => $request->posisi_bermain,
            'tinggi_cm' => $request->tinggi_cm,
            'berat_kg' => $request->berat_kg,
            'is_captain' => 0,
            'is_libero' => $isLibero,
            'status_verifikasi' => 'SUBMITTED',
        ]);

        AuditLogService::log(
            'team.add_player',
            "Mendaftarkan pemain {$pemain->nama_lengkap} (No. #{$request->nomor_punggung}) ke tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'pemain_id' => $pemain->id, 'nomor_punggung' => $request->nomor_punggung]]
        );

        return redirect()->back()->with('success', "Pemain {$pemain->nama_lengkap} (No. #{$request->nomor_punggung}) berhasil didaftarkan ke roster!");
    }

    /**
     * Hapus Pemain dari Roster
     */
    public function destroyPemain(Request $request, $id)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $roster = Roster::where('peserta_tim_id', $activeTeam->id)->where('id', $id)->firstOrFail();
        $namaPemain = $roster->pemain ? $roster->pemain->nama_lengkap : 'Pemain';
        $roster->delete();

        AuditLogService::log(
            'team.remove_player',
            "Menghapus pemain {$namaPemain} dari roster tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'pemain_name' => $namaPemain]]
        );

        return redirect()->back()->with('success', "Pemain {$namaPemain} telah dihapus dari susunan roster.");
    }

    /**
     * Manajemen Official & Pelatih Tim
     */
    public function official(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return view('dashboard.admin_tim_no_team', compact('user'));
        }

        $officials = OfficialTim::where('peserta_tim_id', $activeTeam->id)
            ->orderBy('id', 'asc')
            ->get();

        return view('tim.official', compact('user', 'teams', 'activeTeam', 'officials'));
    }

    /**
     * Tambah Official Tim
     */
    public function storeOfficial(Request $request)
    {
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'jabatan' => 'required|string',
            'nomor_lisensi' => 'nullable|string|max:100',
        ]);

        OfficialTim::create([
            'peserta_tim_id' => $activeTeam->id,
            'user_id' => null,
            'nama_lengkap' => strtoupper(trim($request->nama_lengkap)),
            'jabatan' => $request->jabatan,
            'nomor_lisensi' => $request->nomor_lisensi ? trim($request->nomor_lisensi) : null,
            'status_verifikasi' => 'SUBMITTED',
        ]);

        AuditLogService::log(
            'team.add_official',
            "Mendaftarkan official {$request->nama_lengkap} ({$request->jabatan}) ke tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'official_name' => $request->nama_lengkap, 'jabatan' => $request->jabatan]]
        );

        return redirect()->back()->with('success', "Official {$request->nama_lengkap} berhasil ditambahkan!");
    }

    /**
     * Hapus Official Tim
     */
    public function destroyOfficial(Request $request, $id)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $official = OfficialTim::where('peserta_tim_id', $activeTeam->id)->where('id', $id)->firstOrFail();
        $nama = $official->nama_lengkap;
        $official->delete();

        AuditLogService::log(
            'team.remove_official',
            "Menghapus official {$nama} dari kontingen tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'official_name' => $nama]]
        );

        return redirect()->back()->with('success', "Official {$nama} berhasil dihapus dari kontingen.");
    }

    /**
     * Roster O-2 Builder (Kapten, Libero, No Punggung)
     */
    public function roster(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return view('dashboard.admin_tim_no_team', compact('user'));
        }

        $roster = Roster::where('peserta_tim_id', $activeTeam->id)
            ->with(['pemain.klub', 'pemain.pengprov'])
            ->orderBy('nomor_punggung', 'asc')
            ->get();

        $captain = $roster->where('is_captain', 1)->first();
        $liberos = $roster->where('is_libero', 1);

        return view('tim.roster', compact('user', 'teams', 'activeTeam', 'roster', 'captain', 'liberos'));
    }

    /**
     * Update Penugasan O-2 Roster (Kapten & Libero)
     */
    public function updateRoster(Request $request)
    {
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'captain_id' => 'nullable|integer',
            'libero_ids' => 'nullable|array|max:2',
            'libero_ids.*' => 'integer',
        ], [
            'libero_ids.max' => 'Sesuai regulasi PBVSI/FIVB, maksimal 2 pemain yang boleh didaftarkan sebagai Libero.',
        ]);

        $captainId = $request->captain_id;
        $liberoIds = $request->libero_ids ?? [];

        // VALIDASI REGULASI PBVSI / FIVB: KAPTEN TIDAK BOLEH MERANGKAP SEBAGAI LIBERO
        if ($captainId && in_array($captainId, $liberoIds)) {
            return redirect()->back()->withErrors([
                'captain_id' => 'Regulasi PBVSI & FIVB: Pemain yang bertindak sebagai KAPTEN TIM TIDAK BOLEH merangkap sebagai LIBERO!'
            ]);
        }

        // Reset semua captain dan libero untuk tim ini
        Roster::where('peserta_tim_id', $activeTeam->id)->update([
            'is_captain' => 0,
            'is_libero' => 0,
        ]);

        // Set Captain
        if ($captainId) {
            Roster::where('peserta_tim_id', $activeTeam->id)
                ->where('id', $captainId)
                ->update(['is_captain' => 1]);
        }

        // Set Libero(s)
        if (!empty($liberoIds)) {
            Roster::where('peserta_tim_id', $activeTeam->id)
                ->whereIn('id', $liberoIds)
                ->update([
                    'is_libero' => 1,
                    'posisi_bermain' => 'LIBERO'
                ]);
        }

        $user = Auth::user();
        AuditLogService::log(
            'team.update_roster',
            "Memperbarui penetapan kapten dan libero pada susunan roster O-2 tim {$activeTeam->nama_tim}.",
            ['user' => $user, 'metadata' => ['team_id' => $activeTeam->id, 'captain_id' => $captainId, 'libero_ids' => $liberoIds]]
        );

        return redirect()->back()->with('success', 'Penugasan Kapten dan Libero untuk Roster O-2 berhasil disimpan!');
    }

    /**
     * Lembar Cetak Formulir O-2 Resmi PBVSI
     */
    public function printO2(Request $request)
    {
        $user = Auth::user();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        if (!$activeTeam) {
            return redirect()->route('dashboard');
        }

        $roster = Roster::where('peserta_tim_id', $activeTeam->id)
            ->with(['pemain.klub', 'pemain.pengprov'])
            ->orderBy('nomor_punggung', 'asc')
            ->get();

        $captain = $roster->where('is_captain', 1)->first();
        $liberos = $roster->where('is_libero', 1);

        $officials = OfficialTim::where('peserta_tim_id', $activeTeam->id)
            ->orderBy('id', 'asc')
            ->get();

        return view('tim.o2_print', compact('user', 'teams', 'activeTeam', 'roster', 'captain', 'liberos', 'officials'));
    }

    /**
     * Halaman Profil & Akun Saya
     */
    public function akun(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?: new UserProfile();
        [$teams, $activeTeam] = $this->resolveTeamContext($request);

        return view('tim.akun', compact('user', 'profile', 'teams', 'activeTeam'));
    }

    /**
     * Update Akun Saya & Password
     */
    public function updateAkun(Request $request, BunnyCDNService $bunnyService)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:core_users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $bunnyService->uploadOptimizedImage(
                $request->file('avatar'),
                'uploads/avatars',
                maxWidth: 800,
                maxHeight: 800,
                quality: 85
            );
            if ($avatarPath) {
                if (!empty($user->avatar_path)) {
                    $bunnyService->delete($user->avatar_path);
                }
                $user->avatar_path = $avatarPath;
            }
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            if (array_key_exists('raw_password', $user->getAttributes())) {
                $user->raw_password = $request->password;
            }
        }

        $user->save();

        AuditLogService::log(
            'profile.update',
            "Admin tim {$user->name} ({$user->username}) memperbarui profil akun / kata sandi.",
            ['user' => $user]
        );

        return redirect()->back()->with('success', 'Data profil akun Anda berhasil diperbarui!');
    }
}
