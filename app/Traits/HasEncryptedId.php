<?php

namespace App\Traits;

use App\Services\SecureId;

trait HasEncryptedId
{
    /**
     * Get encryption context for this model (defaults to table name).
     */
    public function getEncryptionContext(): string
    {
        return property_exists($this, 'encryptionContext') ? $this->encryptionContext : $this->getTable();
    }

    /**
     * Accessor for encrypted_id ($model->encrypted_id).
     */
    public function getEncryptedIdAttribute(): string
    {
        return SecureId::encrypt($this->getKey(), $this->getEncryptionContext());
    }

    /**
     * Use encrypted ID as the default route key in views and URLs.
     */
    public function getRouteKey(): string
    {
        return $this->encrypted_id;
    }

    /**
     * Resolve route binding accepting both encrypted token and guarded fallback.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        // Try decrypting with model's context
        $decryptedId = SecureId::decrypt($value, $this->getEncryptionContext());
        if ($decryptedId !== null) {
            return $this->where($this->getKeyName(), $decryptedId)->first();
        }

        // Guard: check if raw integer is allowed
        if (is_numeric($value)) {
            return $this->where($this->getKeyName(), (int) $value)->first();
        }

        return null;
    }
}
