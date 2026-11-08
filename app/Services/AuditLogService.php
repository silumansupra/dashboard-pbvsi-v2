<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Catat aktivitas audit ke sistem PBVSI
     *
     * @param string $action Contoh: 'auth.login', 'auth.forgot_password', 'team.update_profile'
     * @param string $description Penjelasan ringkas aktivitas
     * @param array $options Opsi tambahan: module, user, old_values, new_values, metadata
     * @return AuditLog|null
     */
    public static function log(string $action, string $description, array $options = []): ?AuditLog
    {
        try {
            // Tentukan modul dari opsi atau dari prefix aksi (e.g. 'auth.login' => 'auth')
            $module = $options['module'] ?? explode('.', $action)[0] ?? 'system';

            // Dapatkan subjek pengguna
            /** @var User|null $user */
            $user = $options['user'] ?? auth()->user();

            $userId = null;
            $userName = null;
            $userUsername = null;
            $userRole = null;

            if ($user instanceof User) {
                $userId = $user->id;
                $userName = $user->name;
                $userUsername = $user->username;
                $userRole = $user->roles->first()?->name ?? ($user->role ?? 'user');
            } elseif (isset($options['user_data'])) {
                // Untuk aksi anonim / belum login tapi ada identitas target (misal reset password)
                $userId = $options['user_data']['id'] ?? null;
                $userName = $options['user_data']['name'] ?? null;
                $userUsername = $options['user_data']['username'] ?? null;
                $userRole = $options['user_data']['role'] ?? null;
            }

            $request = request();

            return AuditLog::create([
                'user_id' => $userId,
                'user_name' => $userName,
                'user_username' => $userUsername,
                'user_role' => $userRole,
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'ip_address' => $request ? $request->ip() : null,
                'user_agent' => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
                'old_values' => $options['old_values'] ?? null,
                'new_values' => $options['new_values'] ?? null,
                'metadata' => array_merge([
                    'url' => $request ? $request->fullUrl() : null,
                    'method' => $request ? $request->method() : null,
                ], $options['metadata'] ?? []),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Log ke file laravel.log agar tidak mengganggu alur proses utama
            Log::error("Gagal mencatat audit log [{$action}]: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
}
