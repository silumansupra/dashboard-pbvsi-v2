<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedId;

class Jabatan extends Model
{
    use HasEncryptedId;

    protected $table = 'core_master_jabatan';
    protected string $encryptionContext = 'jabatan';

    protected $appends = ['encrypted_id'];
    protected $fillable = ['nama_jabatan', 'is_official_tim', 'is_active'];

    protected $casts = [
        'is_official_tim' => 'boolean',
        'is_active' => 'boolean',
    ];
}
