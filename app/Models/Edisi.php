<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edisi extends Model
{
    protected $table = 'trn_edisi';
    protected $fillable = [
        'turnamen_id',
        'tahun',
        'slug_edisi',
        'nama_edisi',
        'tanggal_mulai',
        'tanggal_selesai',
        'kota_tuan_rumah',
        'venue_utama',
        'min_tanggal_lahir',
        'max_tanggal_lahir',
        'max_kuota_pemain',
        'status_edisi',
    ];

    public function turnamen()
    {
        return $this->belongsTo(Turnamen::class, 'turnamen_id');
    }

    public function timPeserta()
    {
        return $this->hasMany(PesertaTim::class, 'edisi_id');
    }

    public function jadwalPertandingan()
    {
        return $this->hasMany(Jadwal::class, 'edisi_id');
    }
}
