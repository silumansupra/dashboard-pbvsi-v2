<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Pengprov extends Model
{
    use HasEncryptedId;

    protected $table = 'org_pengprov';
    protected string $encryptionContext = 'pengprov';

    protected $appends = ['encrypted_id'];
    protected $fillable = [
        'provinsi_id',
        'nama_pengprov',
        'ketum',
        'sekum',
        'nohp_ketum',
        'nohp_sekum',
        'email',
        'no_telp',
        'website',
        'alamat',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function pengkot()
    {
        return $this->hasMany(Pengkot::class, 'pengprov_id');
    }

    public function klub()
    {
        return $this->hasMany(Klub::class, 'pengprov_id');
    }

    public function pesertaKontingen()
    {
        return $this->hasMany(PesertaTim::class, 'pengprov_id')->whereNull('klub_id');
    }
}
