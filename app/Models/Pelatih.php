<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatih extends Model
{
    protected $table = 'member_pelatih';

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
