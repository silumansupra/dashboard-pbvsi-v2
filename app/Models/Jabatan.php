<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'core_master_jabatan';
    protected $fillable = ['nama_jabatan', 'is_official_tim', 'is_active'];

    protected $casts = [
        'is_official_tim' => 'boolean',
        'is_active' => 'boolean',
    ];
}
