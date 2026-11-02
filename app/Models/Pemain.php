<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemain extends Model
{
    protected $table = 'member_pemain';

    protected $fillable = [
        'user_id',
        'klub_id',
        'pengprov_id',
        'pbvsi_player_id',
        'nik',
        'nisn',
        'nama_lengkap',
        'nama_punggung',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'posisi_utama',
        'status_kewarganegaraan',
        'foto_profil_path',
        'scan_akta_path',
        'scan_ijazah_path',
        'scan_kk_path',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
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

    public function rosters()
    {
        return $this->hasMany(Roster::class, 'pemain_id');
    }
}
