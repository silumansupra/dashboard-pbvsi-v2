<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfile;
use App\Services\BunnyCDNService;
use App\Services\AuditLogService;

class ProfileCompletionController extends Controller
{
    /**
     * Tampilkan formulir kelengkapan profil akun
     */
    public function showForm()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $user->profile ?: new UserProfile();

        return view('auth.lengkapi_profil', compact('user', 'profile'));
    }

    /**
     * Simpan kelengkapan data profil akun, berkas KTP, dan bukti persetujuan regulasi
     */
    public function store(Request $request, BunnyCDNService $bunnyService)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $user->profile ?: new UserProfile(['user_id' => $user->id]);

        // Aturan validasi dinamis (wajib jika belum ada berkas sebelumnya)
        $rules = [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:core_users,email,' . $user->id,
            'phone' => 'required|string|max:25|regex:/^[0-9+ -]{9,25}$/',
            'nik'   => 'required|digits:16',
            'agree_terms' => 'required|accepted',
        ];

        // Validasi foto profil
        if (empty($user->avatar_path)) {
            $rules['avatar'] = 'required|image|mimes:jpeg,jpg,png,webp|max:10240';
        } else {
            $rules['avatar'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        // Validasi foto KTP
        if (empty($profile->ktp_path)) {
            $rules['ktp'] = 'required|image|mimes:jpeg,jpg,png,webp|max:10240';
        } else {
            $rules['ktp'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        // Validasi foto selfie KTP
        if (empty($profile->selfie_ktp_path)) {
            $rules['selfie_ktp'] = 'required|image|mimes:jpeg,jpg,png,webp|max:10240';
        } else {
            $rules['selfie_ktp'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        $messages = [
            'name.required' => 'Nama lengkap sesuai KTP wajib diisi.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
            'avatar.required' => 'Foto profil akun wajib diunggah.',
            'avatar.image' => 'Berkas foto profil harus berupa gambar (JPG, PNG, atau WebP).',
            'avatar.max' => 'Ukuran foto profil maksimal 10 MB.',
            'ktp.required' => 'Foto KTP wajib diunggah untuk verifikasi identitas resmi.',
            'ktp.image' => 'Berkas KTP harus berupa gambar (JPG, PNG, atau WebP).',
            'ktp.max' => 'Ukuran berkas KTP maksimal 10 MB.',
            'selfie_ktp.required' => 'Foto selfie bersama KTP wajib diunggah.',
            'selfie_ktp.image' => 'Berkas selfie KTP harus berupa gambar (JPG, PNG, atau WebP).',
            'selfie_ktp.max' => 'Ukuran berkas selfie KTP maksimal 10 MB.',
            'agree_terms.required' => 'Anda wajib menyetujui Ketentuan Penggunaan dan Kebijakan Privasi PBVSI.',
            'agree_terms.accepted' => 'Anda wajib menyetujui Ketentuan Penggunaan dan Kebijakan Privasi PBVSI.',
        ];

        $validated = $request->validate($rules, $messages);

        // 1. Upload & Kompresi Foto Profil ke BunnyCDN
        if ($request->hasFile('avatar')) {
            $avatarPath = $bunnyService->uploadOptimizedImage(
                $request->file('avatar'),
                'uploads/avatars',
                maxWidth: 800,
                maxHeight: 800,
                quality: 85
            );
            if ($avatarPath) {
                // Hapus avatar lama jika ada
                if (!empty($user->avatar_path)) {
                    $bunnyService->delete($user->avatar_path);
                }
                $user->avatar_path = $avatarPath;
            }
        }

        // 2. Upload & Kompresi Foto KTP ke BunnyCDN
        if ($request->hasFile('ktp')) {
            $ktpPath = $bunnyService->uploadOptimizedImage(
                $request->file('ktp'),
                'uploads/kyc',
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 85
            );
            if ($ktpPath) {
                if (!empty($profile->ktp_path)) {
                    $bunnyService->delete($profile->ktp_path);
                }
                $profile->ktp_path = $ktpPath;
            }
        }

        // 3. Upload & Kompresi Foto Selfie KTP ke BunnyCDN
        if ($request->hasFile('selfie_ktp')) {
            $selfiePath = $bunnyService->uploadOptimizedImage(
                $request->file('selfie_ktp'),
                'uploads/kyc',
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 85
            );
            if ($selfiePath) {
                if (!empty($profile->selfie_ktp_path)) {
                    $bunnyService->delete($profile->selfie_ktp_path);
                }
                $profile->selfie_ktp_path = $selfiePath;
            }
        }

        // Update data pada core_users
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->is_profile_completed = true;
        $user->save();

        // Update data pada core_user_profiles
        $profile->user_id = $user->id;
        $profile->nik = $validated['nik'];
        if (empty($profile->verification_status) || $profile->verification_status === 'UNVERIFIED') {
            $profile->verification_status = 'PENDING';
        }
        $profile->terms_agreed_at = now();
        $profile->terms_agreed_ip = $request->ip();
        $profile->terms_agreed_version = 'v1.0';
        $profile->save();

        // Catat Audit Log
        AuditLogService::log(
            'user.profile_completed',
            "Pengguna {$user->name} ({$user->username}) telah melengkapi data profil, mengunggah berkas KTP, dan menyetujui Ketentuan Platform PBVSI.",
            [
                'user' => $user,
                'metadata' => [
                    'nik' => substr($validated['nik'], 0, 6) . '******' . substr($validated['nik'], -4),
                    'terms_agreed_version' => 'v1.0',
                    'terms_agreed_at' => now()->toDateTimeString(),
                    'ip_address' => $request->ip(),
                ]
            ]
        );

        return redirect()->route('dashboard')->with('success', 'Data profil dan berkas identitas Anda berhasil disimpan. Selamat datang di Platform PBVSI!');
    }
}
