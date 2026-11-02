<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'core_user_profiles';
    protected $fillable = [
        'user_id',
        'nik',
        'npwp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'alamat',
        'provinsi_id',
        'kabupaten_id',
        'kodepos',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }
}
