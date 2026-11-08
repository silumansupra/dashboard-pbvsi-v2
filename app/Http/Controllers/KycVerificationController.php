<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class KycVerificationController extends Controller
{
    /**
     * Memastikan hanya user dengan role superadmin atau permission kyc.verify yang dapat mengakses
     */
    protected function authorizeKyc(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->hasRole('superadmin') && !$user->hasPermissionTo('kyc.verify'))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk memverifikasi KYC & KTP Pengguna.');
        }
    }

    /**
     * Tampilkan daftar pengajuan verifikasi KYC
     */
    public function index(Request $request)
    {
        $this->authorizeKyc();

        $query = UserProfile::with(['user.roles', 'verifiedByUser', 'provinsi', 'kabupaten'])
            ->whereHas('user');

        // Statistik Counter
        $counts = [
            'total' => UserProfile::whereHas('user')->count(),
            'pending' => UserProfile::whereHas('user')->where('verification_status', 'PENDING')->count(),
            'verified' => UserProfile::whereHas('user')->where('verification_status', 'VERIFIED')->count(),
            'rejected' => UserProfile::whereHas('user')->where('verification_status', 'REJECTED')->count(),
            'unverified' => UserProfile::whereHas('user')->where('verification_status', 'UNVERIFIED')->count(),
        ];

        // Filter Status
        $status = strtoupper($request->get('status', ''));
        if (in_array($status, ['PENDING', 'VERIFIED', 'REJECTED', 'UNVERIFIED'])) {
            $query->where('verification_status', $status);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting: Dahulukan yang PENDING agar segera diproses
        $profiles = $query->orderByRaw("
            CASE 
                WHEN verification_status = 'PENDING' THEN 1 
                WHEN verification_status = 'REJECTED' THEN 2 
                WHEN verification_status = 'UNVERIFIED' THEN 3 
                ELSE 4 
            END ASC
        ")->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('master.kyc_index', compact('profiles', 'counts', 'status'));
    }

    /**
     * Dapatkan data lengkap satu profil untuk modal review (JSON)
     */
    public function show($id)
    {
        $this->authorizeKyc();

        $profile = UserProfile::with(['user.roles', 'verifiedByUser', 'provinsi', 'kabupaten'])
            ->findOrFail($id);

        return response()->json([
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'name' => $profile->user->name,
            'username' => $profile->user->username,
            'email' => $profile->user->email,
            'phone' => $profile->user->phone,
            'roles' => $profile->user->roles->pluck('display_name')->implode(', ') ?: 'User',
            'avatar_url' => $profile->user->avatar_url,
            'nik' => $profile->nik,
            'ktp_url' => $profile->ktp_url,
            'selfie_ktp_url' => $profile->selfie_ktp_url,
            'verification_status' => $profile->verification_status,
            'verified_at' => $profile->verified_at ? $profile->verified_at->translatedFormat('d F Y H:i') : null,
            'verified_by_name' => $profile->verifiedByUser?->name,
            'rejection_reason' => $profile->rejection_reason,
            'terms_agreed_at' => $profile->terms_agreed_at ? $profile->terms_agreed_at->translatedFormat('d F Y H:i:s') : null,
            'terms_agreed_ip' => $profile->terms_agreed_ip,
            'tempat_lahir' => $profile->tempat_lahir,
            'tanggal_lahir' => $profile->tanggal_lahir ? $profile->tanggal_lahir->format('d/m/Y') : null,
            'jenis_kelamin' => $profile->jenis_kelamin == 'L' ? 'Laki-laki' : ($profile->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
            'alamat' => $profile->alamat,
            'wilayah' => ($profile->kabupaten?->nama_kabupaten ?? '') . ($profile->provinsi ? ', ' . $profile->provinsi->nama_provinsi : ''),
            'submitted_at' => $profile->updated_at ? $profile->updated_at->translatedFormat('d F Y H:i') : null,
        ]);
    }

    /**
     * Setujui berkas KYC pengguna
     */
    public function approve($id, Request $request)
    {
        $this->authorizeKyc();

        $profile = UserProfile::with('user')->findOrFail($id);
        $user = $profile->user;

        $profile->update([
            'verification_status' => 'VERIFIED',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        if ($user) {
            $user->update(['is_profile_completed' => true]);
        }

        AuditLogService::log(
            'kyc.approve',
            "Verifikator " . auth()->user()->name . " menyetujui verifikasi KYC/KTP akun {$user->name} ({$user->username}).",
            [
                'user' => auth()->user(),
                'metadata' => [
                    'profile_id' => $profile->id,
                    'target_user_id' => $user->id,
                    'nik' => $profile->nik,
                ]
            ]
        );

        return redirect()->back()->with('success', "Berkas KYC & KTP pengguna {$user->name} ({$user->username}) berhasil DISETUJUI.");
    }

    /**
     * Tolak berkas KYC pengguna dengan alasan wajib
     */
    public function reject($id, Request $request)
    {
        $this->authorizeKyc();

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Wajib menyertakan alasan penolakan berkas.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $profile = UserProfile::with('user')->findOrFail($id);
        $user = $profile->user;

        $profile->update([
            'verification_status' => 'REJECTED',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => trim($request->rejection_reason),
        ]);

        if ($user) {
            $user->update(['is_profile_completed' => false]);
        }

        AuditLogService::log(
            'kyc.reject',
            "Verifikator " . auth()->user()->name . " menolak berkas KYC/KTP akun {$user->name} ({$user->username}). Alasan: {$request->rejection_reason}",
            [
                'user' => auth()->user(),
                'metadata' => [
                    'profile_id' => $profile->id,
                    'target_user_id' => $user->id,
                    'reason' => $request->rejection_reason,
                ]
            ]
        );

        return redirect()->back()->with('warning', "Berkas KYC pengguna {$user->name} telah DITOLAK. Alasan: {$request->rejection_reason}");
    }
}
