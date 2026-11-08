<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'core_user_profiles';
    protected $fillable = [
        'user_id',
        'nik',
        'ktp_path',
        'selfie_ktp_path',
        'verification_status',
        'terms_agreed_at',
        'terms_agreed_ip',
        'terms_agreed_version',
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

    protected $casts = [
        'tanggal_lahir' => 'date',
        'terms_agreed_at' => 'datetime',
    ];

    public function getKtpUrlAttribute(): ?string
    {
        return $this->ktp_path ? app(\App\Services\BunnyCDNService::class)->getUrl($this->ktp_path) : null;
    }

    public function getSelfieKtpUrlAttribute(): ?string
    {
        return $this->selfie_ktp_path ? app(\App\Services\BunnyCDNService::class)->getUrl($this->selfie_ktp_path) : null;
    }

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
