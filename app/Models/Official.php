<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Official extends Model
{
    protected $table = 'member_official';

    protected $fillable = [
        'user_id',
        'pengprov_id',
        'klub_id',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'jabatan_official',
        'nomor_lisensi',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    /**
     * Mutator otomatis untuk standarisasi UPPERCASE
     */
    protected function namaLengkap(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value)),
        );
    }

    protected function jabatanOfficial(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value)),
        );
    }

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
}
