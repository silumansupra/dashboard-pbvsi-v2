<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    protected $table = 'trn_roster';
    protected $fillable = [
        'peserta_tim_id',
        'pemain_id',
        'nomor_punggung',
        'nama_jersey',
        'posisi_bermain',
        'tinggi_cm',
        'berat_kg',
        'spike_reach_cm',
        'block_reach_cm',
        'is_captain',
        'is_libero',
        'status_verifikasi',
    ];

    public function tim()
    {
        return $this->belongsTo(PesertaTim::class, 'peserta_tim_id');
    }

    public function pemain()
    {
        return $this->belongsTo(Pemain::class, 'pemain_id');
    }
}
