<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengprov extends Model
{
    protected $table = 'org_pengprov';
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
}
