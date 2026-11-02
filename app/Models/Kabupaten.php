<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'core_wilayah_kabupaten';
    protected $fillable = ['provinsi_id', 'kode_kabupaten', 'nama_kabupaten', 'tipe'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}
