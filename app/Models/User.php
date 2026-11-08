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

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'core_model_has_permissions', 'model_id', 'permission_id')
            ->wherePivot('model_type', 'App\\Models\\User');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Cek hak akses granular: Superadmin bypass, Direct User Permission, atau Role Permission
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // 1. Superadmin selalu memiliki bypass hak akses penuh
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // 2. Cek permission direct user (individual permission)
        if ($this->directPermissions->contains('name', $permissionName)) {
            return true;
        }

        // 3. Cek permission turunan dari roles yang dimiliki user
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user memiliki hak akses membuka menu tertentu
     */
    public function canAccessMenu(Menu $menu): bool
    {
        if (empty($menu->permission_key)) {
            return true;
        }

        return $this->hasPermissionTo($menu->permission_key);
    }

    public function isProfileComplete(): bool
    {
        // Pengguna dengan peran Superadmin dikecualikan dari validasi kelengkapan profil & KYC
        if ($this->hasRole('superadmin')) {
            return true;
        }

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
