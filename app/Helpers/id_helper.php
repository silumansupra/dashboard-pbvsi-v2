<?php

use App\Services\SecureId;

if (!function_exists('encrypt_id')) {
    /**
     * Encrypt an integer ID with optional context.
     */
    function encrypt_id($id, string $context = ''): string
    {
        return SecureId::encrypt($id, $context);
    }
}

if (!function_exists('decrypt_id')) {
    /**
     * Decrypt an ID token with optional context. Returns null on failure.
     */
    function decrypt_id(?string $token, string $context = ''): ?int
    {
        return SecureId::decrypt($token, $context);
    }
}

if (!function_exists('kirimEmailVerifikasi')) {
    /**
     * Kirim email verifikasi akun baru (Kompatibel dengan sistem CI3 & Platform PBVSI v2)
     */
    function kirimEmailVerifikasi($data): bool
    {
        return \App\Services\EmailService::kirimEmailVerifikasi($data);
    }
}

if (!function_exists('enurl')) {
    /**
     * Helper URL encode string kompatibilitas sistem lama CI3
     */
    function enurl($data): string
    {
        return urlencode(base64_encode((string)$data));
    }
}

if (!function_exists('deurl')) {
    /**
     * Helper URL decode string kompatibilitas sistem lama CI3
     */
    function deurl($data): ?string
    {
        $decoded = base64_decode(urldecode((string)$data), true);
        return $decoded !== false ? $decoded : null;
    }
}
