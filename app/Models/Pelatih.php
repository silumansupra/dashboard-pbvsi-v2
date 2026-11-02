<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Pelatih extends Model
{
    use HasEncryptedId;

    protected $table = 'member_pelatih';
    protected string $encryptionContext = 'pelatih';

    protected $appends = ['foto_url', 'encrypted_id'];

    public function getFotoUrlAttribute(): string
    {
        if ($this->user && !empty($this->user->avatar_path)) {
            return $this->user->avatar_url;
        }

        return asset('images/no-user.svg');
    }

    protected $fillable = [
        'user_id',
        'pengprov_id',
        'nomor_lisensi_pelatih',
        'tingkat_pelatih',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengprov()
    {
        return $this->belongsTo(Pengprov::class, 'pengprov_id');
    }
}
