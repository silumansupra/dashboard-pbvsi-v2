<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        $rolesLabel = implode(' / ', array_map(fn($r) => strtoupper(str_replace('_', ' ', $r)), $roles));

        return redirect()->route('dashboard')->withErrors([
            'access_denied' => "Akses Ditolak: Anda tidak memiliki izin untuk membuka modul tersebut. Fitur ini dibatasi khusus untuk {$rolesLabel}."
        ]);
    }
}
