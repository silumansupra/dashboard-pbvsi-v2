<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaTim extends Model
{
    protected $table = 'trn_peserta_tim';
    protected $fillable = [
        'edisi_id',
        'klub_id',
        'pengprov_id',
        'nama_tim',
        'kode_tim',
        'kategori_gender',
        'pool_grup',
        'warna_jersey_1',
        'warna_jersey_2',
        'warna_jersey_3',
        'warna_jersey_libero_1',
        'warna_jersey_libero_2',
        'file_jersey_1',
        'file_jersey_2',
        'file_jersey_3',
        'logo_tim_path',
        'foto_skuad_path',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    public function edisi()
    {
        return $this->belongsTo(Edisi::class, 'edisi_id');
    }

    public function roster()
    {
        return $this->hasMany(Roster::class, 'peserta_tim_id');
    }

    public function klub()
    {
        return $this->belongsTo(Klub::class, 'klub_id');
    }

    public function pengprov()
    {
        return $this->belongsTo(Pengprov::class, 'pengprov_id');
    }
}
