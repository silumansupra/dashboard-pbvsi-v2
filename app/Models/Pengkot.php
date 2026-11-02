<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Pengkot extends Model
{
    use HasEncryptedId;

    protected $table = 'org_pengkot';
    protected string $encryptionContext = 'pengkot';

    protected $appends = ['encrypted_id'];
    protected $fillable = [
        'pengprov_id',
        'kabupaten_id',
        'nama_pengkot',
        'ketum',
        'sekum',
        'nohp_ketum',
        'nohp_sekum',
        'email',
        'no_telp',
        'alamat',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function pengprov()
    {
        return $this->belongsTo(Pengprov::class, 'pengprov_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }

    public function klub()
    {
        return $this->hasMany(Klub::class, 'pengkot_id');
    }
}
