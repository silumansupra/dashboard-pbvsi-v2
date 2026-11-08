<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Klub extends Model
{
    use HasEncryptedId;

    protected $table = 'org_klub';
    protected string $encryptionContext = 'klub';

    protected $fillable = [
        'pengprov_id',
        'pengkot_id',
        'kode_klub',
        'nama_klub',
        'pemilik_klub',
        'sekretaris_klub',
        'alamat',
        'logo_path',
        'status_verifikasi',
    ];

    protected $appends = ['logo_url', 'encrypted_id'];

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return asset('images/no-logo.svg');
        }

        return app(\App\Services\BunnyCDNService::class)->getUrl($this->logo_path, fallback: asset('images/no-logo.svg'));
    }

    public function pengprov()
    {
        return $this->belongsTo(Pengprov::class, 'pengprov_id');
    }

    public function pengkot()
    {
        return $this->belongsTo(Pengkot::class, 'pengkot_id');
    }

    public function pesertaTurnamen()
    {
        return $this->hasMany(PesertaTim::class, 'klub_id');
    }
}
