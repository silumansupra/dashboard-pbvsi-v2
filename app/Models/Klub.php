<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klub extends Model
{
    protected $table = 'org_klub';
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

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        return app(\App\Services\BunnyCDNService::class)->getUrl($this->logo_path);
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
