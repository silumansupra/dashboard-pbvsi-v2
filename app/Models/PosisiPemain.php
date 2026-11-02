<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class PosisiPemain extends Model
{
    use HasEncryptedId;

    protected $table = 'core_master_posisi_pemain';
    protected string $encryptionContext = 'posisi';

    protected $appends = ['encrypted_id'];
    protected $fillable = ['nama_posisi', 'kode_posisi', 'deskripsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
