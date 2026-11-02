<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengkot extends Model
{
    protected $table = 'org_pengkot';
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
