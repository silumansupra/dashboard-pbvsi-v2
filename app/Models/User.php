<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasEncryptedId;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasEncryptedId;

    protected string $encryptionContext = 'user';

    protected $appends = ['encrypted_id', 'avatar_url'];

    protected $table = 'core_users';

    protected $fillable = [
        'uuid',
        'username',
        'name',
        'email',
        'phone',
        'password',
        'raw_password',
        'is_active',
        'avatar_path',
        'is_profile_completed',
    ];

    protected $hidden = [
        'password',
        'raw_password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_profile_completed' => 'boolean',
        ];
    }

    /**
     * Mutator otomatis untuk standarisasi nama akun user ke UPPERCASE
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value)),
        );
    }

    public function getAvatarUrlAttribute(): string
    {
        return app(\App\Services\BunnyCDNService::class)->getUrl($this->avatar_path, $this->id);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'core_model_has_roles', 'model_id', 'role_id')
            ->wherePivot('model_type', 'App\\Models\\User');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    public function isProfileComplete(): bool
    {
        if ($this->is_profile_completed) {
            return true;
        }

        // Pengecekan riil pada data akun & profil
        $hasAvatar = !empty($this->avatar_path);
        $hasName = !empty($this->name);
        $hasEmail = !empty($this->email);
        $hasPhone = !empty($this->phone);

        $profile = $this->profile;
        $hasKtp = $profile && !empty($profile->nik) && !empty($profile->ktp_path) && !empty($profile->selfie_ktp_path);
        $hasAgreedTerms = $profile && !empty($profile->terms_agreed_at);

        if ($hasAvatar && $hasName && $hasEmail && $hasPhone && $hasKtp && $hasAgreedTerms) {
            $this->forceFill(['is_profile_completed' => true])->saveQuietly();
            return true;
        }

        return false;
    }

    public function teams()
    {
        return $this->belongsToMany(PesertaTim::class, 'trn_user_tim', 'user_id', 'peserta_tim_id')
            ->withPivot('role_tim', 'status_aktif')
            ->withTimestamps();
    }
}
