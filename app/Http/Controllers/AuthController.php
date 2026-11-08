<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        if (!$user) {
            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun dengan Username atau Email tersebut tidak ditemukan.']);
        }

        // Cek status aktif
        if (!$user->is_active) {
            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator PP PBVSI.']);
        }

        // Cek kecocokan password (Hash bcrypt, raw_password, atau dev master password di local)
        $passwordMatches = Hash::check($password, $user->password) 
            || ($user->raw_password && $user->raw_password === $password)
            || (app()->isLocal() && in_array($password, ['pbvsi12345', 'admin123']));

        if (!$passwordMatches) {
            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['password' => 'Password yang Anda masukkan salah.']);
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

        return redirect()->intended('/dashboard')
            ->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Proses logout user dari sistem
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda telah berhasil logout dari sistem PBVSI.');
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

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->raw_password = $request->password;
        }

        $user->save();

        return redirect()->back()->with('success', 'Data profil akun Anda berhasil diperbarui!');
    }
}
