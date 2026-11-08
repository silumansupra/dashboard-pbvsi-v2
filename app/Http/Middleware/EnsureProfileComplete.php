<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     * Memastikan seluruh pengguna yang login telah melengkapi profil dan menyetujui ketentuan PBVSI.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika user belum login, lanjutkan ke middleware berikutnya (auth akan menangani jika rute diproteksi)
        if (!$user) {
            return $next($request);
        }

        // Daftar rute yang dikecualikan agar tidak terjadi infinite redirect loop
        $exemptPatterns = [
            'lengkapi-profil*',
            'logout*',
            'kebijakan-privasi*',
            'syarat-ketentuan*',
            'api/*',
            'assets/*',
            'storage/*',
        ];

        foreach ($exemptPatterns as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Cek kelengkapan profil pengguna
        if (!$user->isProfileComplete()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'incomplete_profile',
                    'message' => 'Silakan lengkapi data profil, unggah KTP, dan setujui regulasi PBVSI terlebih dahulu.',
                    'redirect_url' => route('profil.lengkapi'),
                ], 403);
            }

            return redirect()->route('profil.lengkapi')->with('warning', 'Demi kepatuhan regulasi PBVSI dan verifikasi keabsahan akun, mohon lengkapi profil, berkas identitas KTP, serta persetujuan platform sebelum melanjutkan.');
        }

        return $next($request);
    }
}
