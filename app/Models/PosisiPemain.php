<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosisiPemain extends Model
{
    protected $table = 'core_master_posisi_pemain';
    protected $fillable = ['nama_posisi', 'kode_posisi', 'deskripsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
