<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Provinsi extends Model
{
    use HasEncryptedId;

    protected $table = 'core_wilayah_provinsi';
    protected string $encryptionContext = 'provinsi';

    protected $appends = ['encrypted_id'];
    protected $fillable = ['kode_provinsi', 'nama_provinsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'provinsi_id');
    }

    public function pengprov()
    {
        return $this->hasOne(Pengprov::class, 'provinsi_id');
    }
}
