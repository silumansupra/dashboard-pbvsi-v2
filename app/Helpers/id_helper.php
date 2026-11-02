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
