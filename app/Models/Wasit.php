<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Wasit extends Model
{
    use HasEncryptedId;

    protected $table = 'member_wasit';
    protected string $encryptionContext = 'wasit';

    protected $appends = ['encrypted_id'];

    protected $fillable = [
        'user_id',
        'pengprov_id',
        'nomor_lisensi_wasit',
        'kategori_wasit',
        'tingkat_wasit',
        'status_aktif',
        'catatan_perwasitan',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengprov()
    {
        return $this->belongsTo(Pengprov::class, 'pengprov_id');
    }
}
