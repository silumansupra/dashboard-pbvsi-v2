<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    protected $table = 'member_official';

    protected $fillable = [
        'user_id',
        'pengprov_id',
        'klub_id',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'jabatan_official',
        'nomor_lisensi',
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

    public function klub()
    {
        return $this->belongsTo(Klub::class, 'klub_id');
    }
}
