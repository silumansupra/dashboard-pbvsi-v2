<?php

namespace App\Services;

class SecureId
{
    /**
     * Get encryption key derived from app.key
     */
    protected static function getKey(): string
    {
        $appKey = config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }
        return hash('sha256', $appKey, true);
    }

    /**
     * Encrypt an integer ID into a compact URL-safe token.
     *
     * @param int|string $id
     * @param string $context Model or scope context (e.g. 'klub', 'pengprov')
     * @return string
     */
    public static function encrypt($id, string $context = ''): string
    {
        if (empty($id)) {
            return '';
        }

        $plaintext = (string) $id;
        $key = self::getKey();
        $contextKey = $context !== '' ? hash_hmac('sha256', $context, $key, true) : $key;

        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $contextKey, OPENSSL_RAW_DATA, $iv);

        // 8-byte HMAC tag for cryptographic integrity
        $mac = substr(hash_hmac('sha256', $iv . $ciphertext, $contextKey, true), 0, 8);

        $packed = $mac . $iv . $ciphertext;
        return rtrim(strtr(base64_encode($packed), '+/', '-_'), '=');
    }

    /**
     * Decrypt an encrypted token back into its original integer ID.
     * Returns null if token is invalid, tampered, or wrong context.
     *
     * @param string|null $token
     * @param string $context Model or scope context
     * @return int|null
     */
    public static function decrypt(?string $token, string $context = ''): ?int
    {
        if (empty($token) || !is_string($token)) {
            return null;
        }

        // Restore padding and standard base64 characters
        $b64 = strtr($token, '-_', '+/');
        $raw = base64_decode($b64, true);

        // Header must be at least 8 (MAC) + 16 (IV) + 16 (1 AES block) = 40 bytes
        if ($raw === false || strlen($raw) < 40) {
            return null;
        }

        $key = self::getKey();
        $contextKey = $context !== '' ? hash_hmac('sha256', $context, $key, true) : $key;

        $mac = substr($raw, 0, 8);
        $iv = substr($raw, 8, 16);
        $ciphertext = substr($raw, 24);

        $expectedMac = substr(hash_hmac('sha256', $iv . $ciphertext, $contextKey, true), 0, 8);
        if (!hash_equals($mac, $expectedMac)) {
            return null;
        }

        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $contextKey, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false || !ctype_digit($plaintext)) {
            return null;
        }

        return (int) $plaintext;
    }

    /**
     * Determine if a value is an encrypted token.
     */
    public static function isValid(?string $token, string $context = ''): bool
    {
        return self::decrypt($token, $context) !== null;
    }
}
