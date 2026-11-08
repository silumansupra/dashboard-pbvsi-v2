<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Services\AuditLogService;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login (Username / Email + Password)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Silakan masukkan Username atau Email Anda.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];

        $withHash = '#' . ltrim($loginInput, '#');
        $withoutHash = ltrim($loginInput, '#');

        // Cari user berdasarkan username (fleksibel dengan/tanpa prefix #) atau email
        $user = User::where(function ($q) use ($loginInput, $withHash, $withoutHash) {
            $q->where('username', $loginInput)
              ->orWhere('username', $withHash)
              ->orWhere('username', $withoutHash)
              ->orWhere('email', $loginInput);
        })->first();

        // Pesan seragam untuk mencegah User Enumeration (OWASP Security Standard)
        $invalidCredentialsMessage = 'Kombinasi Username/Email atau Kata Sandi yang Anda masukkan tidak sesuai.';

        if (!$user) {
            AuditLogService::log(
                'auth.failed_login',
                "Upaya login gagal: Akun '{$loginInput}' tidak ditemukan di sistem PBVSI.",
                ['metadata' => ['input' => $loginInput]]
            );

            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => $invalidCredentialsMessage]);
        }

        // Cek kecocokan password terlebih dahulu (Hash bcrypt, raw_password, atau dev master password di local)
        $passwordMatches = Hash::check($password, $user->password) 
            || ($user->raw_password && $user->raw_password === $password)
            || (app()->isLocal() && in_array($password, ['pbvsi12345', 'admin123']));

        if (!$passwordMatches) {
            AuditLogService::log(
                'auth.failed_login',
                "Upaya login gagal untuk pengguna {$user->name} ({$user->username}): Kata sandi salah.",
                ['user' => $user]
            );

            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => $invalidCredentialsMessage]);
        }

        // Cek status aktif setelah password diverifikasi (mencegah penyerang mengetahui eksistensi akun)
        if (!$user->is_active) {
            AuditLogService::log(
                'auth.failed_login',
                "Upaya login ditolak untuk pengguna {$user->name} ({$user->username}): Status akun nonaktif.",
                ['user' => $user]
            );

            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun Anda saat ini berstatus nonaktif. Silakan hubungi Administrator PP PBVSI.']);
        }

        // Jika cocok dengan raw_password/master password tapi hash belum sinkron, update hash-nya
        if (!Hash::check($password, $user->password)) {
            $user->password = Hash::make($password);
            if (empty($user->raw_password)) {
                $user->raw_password = $password;
            }
            $user->save();
        }

        // Lakukan login session
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Catat audit log keberhasilan login
        AuditLogService::log(
            'auth.login',
            "Pengguna {$user->name} ({$user->username}) berhasil masuk ke Platform PBVSI.",
            ['user' => $user]
        );

        return redirect()->intended('/dashboard')
            ->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Proses logout user dari sistem
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLogService::log(
                'auth.logout',
                "Pengguna {$user->name} ({$user->username}) telah logout dari sesi aktif.",
                ['user' => $user]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda telah berhasil logout dari sistem PBVSI.');
    }

    /**
     * Tampilkan formulir permintaan Lupa Kata Sandi
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.forgot_password');
    }

    /**
     * Kirim email verifikasi reset kata sandi
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
        ], [
            'identity.required' => 'Silakan masukkan Username atau Email akun Anda.',
        ]);

        $input = trim($request->input('identity'));
        $withHash = '#' . ltrim($input, '#');
        $withoutHash = ltrim($input, '#');

        $user = User::where(function ($q) use ($input, $withHash, $withoutHash) {
            $q->where('username', $input)
              ->orWhere('username', $withHash)
              ->orWhere('username', $withoutHash)
              ->orWhere('email', $input);
        })->first();

        // Pesan status seragam sesuai standar keamanan OWASP (mencegah User Enumeration / Account Harvesting)
        $genericStatusMessage = 'Jika data akun (Username atau Email) terdaftar di sistem PBVSI, tautan verifikasi pengaturan ulang kata sandi telah dikirimkan ke alamat email terkait. Silakan periksa kotak masuk atau folder spam Anda.';

        if (!$user || empty($user->email)) {
            AuditLogService::log(
                'auth.forgot_password_failed',
                "Permintaan reset kata sandi gagal untuk identitas '{$input}': Akun tidak ditemukan atau tidak memiliki email.",
                ['metadata' => ['identity_input' => $input]]
            );

            // Selalu kembalikan respon sukses yang identik agar penyerang tidak dapat menebak eksistensi akun
            return back()->with('status', $genericStatusMessage);
        }

        // Generate token aman 64 karakter
        $token = Str::random(64);
        $hashedToken = hash('sha256', $token);
        $expiresInMinutes = 60;

        // Bersihkan token lama untuk email ini & masukkan token baru
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'created_at' => now(),
        ]);

        // URL Reset
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // Kirim email
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl, $expiresInMinutes, $request->ip()));
        } catch (\Throwable $e) {
            // Jika mail driver gagal, tetap catat di log
            AuditLogService::log(
                'auth.forgot_password_mail_error',
                "Gagal mengirim email reset password ke {$user->email}: " . $e->getMessage(),
                ['user' => $user]
            );
        }

        // Catat audit log permintaan reset password
        AuditLogService::log(
            'auth.forgot_password',
            "Tautan verifikasi reset kata sandi dikirimkan ke email {$user->email} untuk pengguna {$user->name} ({$user->username}). Tautan kedaluwarsa dalam {$expiresInMinutes} menit.",
            [
                'user' => $user,
                'metadata' => [
                    'email' => $user->email,
                    'expires_at' => now()->addMinutes($expiresInMinutes)->toDateTimeString(),
                    'ip_address' => $request->ip(),
                ]
            ]
        );

        return back()->with('status', $genericStatusMessage);
    }

    /**
     * Tampilkan formulir atur ulang kata sandi baru
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Permintaan tidak valid. Parameter email tidak ditemukan.']);
        }

        // Cek token di tabel password_reset_tokens
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || $record->token !== hash('sha256', $token)) {
            AuditLogService::log(
                'auth.reset_password_invalid',
                "Upaya akses form reset kata sandi ditolak: Token tidak cocok untuk email {$email}.",
                ['metadata' => ['email' => $email]]
            );

            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Tautan reset kata sandi tidak valid atau telah digunakan sebelumnya. Silakan ajukan permintaan baru.']);
        }

        // Cek kedaluwarsa (60 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            AuditLogService::log(
                'auth.reset_password_expired',
                "Upaya akses form reset kata sandi gagal: Token telah kedaluwarsa (>60 menit) untuk email {$email}.",
                ['metadata' => ['email' => $email]]
            );

            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Tautan reset kata sandi telah kedaluwarsa (melebihi batas waktu 60 menit). Silakan ajukan permintaan baru.']);
        }

        return view('auth.reset_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Eksekusi penyimpanan kata sandi baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $email = $request->email;
        $token = $request->token;

        // Validasi ulang record token
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || $record->token !== hash('sha256', $token)) {
            AuditLogService::log(
                'auth.reset_password_failed',
                "Proses reset kata sandi gagal: Token tidak valid untuk email {$email}.",
                ['metadata' => ['email' => $email]]
            );

            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Tautan reset kata sandi tidak valid. Silakan ajukan ulang.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            AuditLogService::log(
                'auth.reset_password_expired',
                "Proses reset kata sandi gagal: Token kedaluwarsa untuk email {$email}.",
                ['metadata' => ['email' => $email]]
            );

            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Tautan reset kata sandi telah kedaluwarsa. Silakan ajukan ulang.']);
        }

        // Update kata sandi pengguna
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['identity' => 'Permintaan tidak valid atau pengguna tidak ditemukan. Silakan ajukan ulang reset kata sandi.']);
        }

        $user->password = Hash::make($request->password);
        if (array_key_exists('raw_password', $user->getAttributes())) {
            $user->raw_password = $request->password;
        }
        $user->save();

        // Hapus token yang telah berhasil digunakan
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Catat keberhasilan ke audit log
        AuditLogService::log(
            'auth.reset_password_success',
            "Kata sandi untuk pengguna {$user->name} ({$user->username}) berhasil diperbarui melalui verifikasi email reset password.",
            ['user' => $user]
        );

        return redirect()->route('login')
            ->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }

    /**
     * Halaman Profil Akun Pengguna Terdaftar (Semua Role)
     */
    public function profilSaya(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first();

        return view('auth.profil_saya', compact('user', 'role'));
    }

    /**
     * Update Profil Akun Pengguna Terdaftar
     */
    public function updateProfilSaya(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:core_users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $oldValues = [
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
        ];

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        $passwordChanged = false;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            if (array_key_exists('raw_password', $user->getAttributes())) {
                $user->raw_password = $request->password;
            }
            $passwordChanged = true;
        }

        $user->save();

        AuditLogService::log(
            'profile.update',
            "Pengguna {$user->name} ({$user->username}) memperbarui profil akunnya" . ($passwordChanged ? " beserta kata sandi baru." : "."),
            [
                'user' => $user,
                'old_values' => $oldValues,
                'new_values' => [
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'password_changed' => $passwordChanged,
                ]
            ]
        );

        return redirect()->back()->with('success', 'Data profil akun Anda berhasil diperbarui!');
    }

    /**
     * Verifikasi dan Aktivasi Akun Pengguna Baru (Kompatibel dengan Tautan CI3 & Platform PBVSI v2)
     */
    public function verifikasiAkun(Request $request)
    {
        $sessionParam = $request->query('session');
        $token = $request->query('token');

        if (!$sessionParam && !$token) {
            return redirect()->route('login')->withErrors(['login' => 'Tautan aktivasi tidak valid. Parameter tidak lengkap.']);
        }

        // Resolusi User ID (mendukung format hash encrypt_id maupun deurl/base64 CI3)
        $userId = decrypt_id($sessionParam, 'user');
        if (!$userId && function_exists('deurl')) {
            $rawDecoded = deurl($sessionParam);
            if (is_numeric($rawDecoded)) {
                $userId = (int)$rawDecoded;
            }
        }
        if (!$userId && is_numeric($sessionParam)) {
            $userId = (int)$sessionParam;
        }

        $user = null;
        if ($userId) {
            $user = User::find($userId);
        }

        // Fallback cari berdasarkan token jika belum ditemukan
        if (!$user && $token) {
            $user = User::where('remember_token', $token)->first();
        }

        if (!$user) {
            AuditLogService::log(
                'auth.verification_failed',
                "Upaya verifikasi akun gagal: Pengguna tidak ditemukan untuk session={$sessionParam}."
            );

            return redirect()->route('login')->withErrors(['login' => 'Data pengguna untuk verifikasi akun tidak ditemukan.']);
        }

        // Aktivasi akun
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();

        AuditLogService::log(
            'auth.account_verified',
            "Akun {$user->name} ({$user->username}) berhasil diverifikasi dan diaktifkan melalui tautan email registrasi.",
            ['user' => $user]
        );

        return view('auth.verifikasi_sukses', compact('user'));
    }

    /**
     * Helper masking email untuk privasi (contoh: ha****@gmail.com)
     */
    private static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];
        $len = strlen($name);

        if ($len <= 2) {
            $masked = $name[0] . '***';
        } else {
            $masked = substr($name, 0, 2) . str_repeat('*', min(4, max(2, $len - 3))) . substr($name, -1);
        }

        return $masked . '@' . $domain;
    }
}
