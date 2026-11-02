<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnamen extends Model
{
    protected $table = 'trn_master_turnamen';
    protected $fillable = ['slug', 'nama_turnamen', 'kategori', 'kelompok_umur', 'jenis_lapangan', 'deskripsi', 'is_active'];

    public function edisi()
    {
        return $this->hasMany(Edisi::class, 'turnamen_id');
    }
}
