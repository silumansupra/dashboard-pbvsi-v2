<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasEncryptedId;

class Pemain extends Model
{
    use HasEncryptedId;

    protected $table = 'member_pemain';
    protected string $encryptionContext = 'pemain';

    protected $appends = ['foto_url', 'encrypted_id'];

    public function getFotoUrlAttribute(): string
    {
        if (!empty($this->foto_profil_path)) {
            return app(\App\Services\BunnyCDNService::class)->getUrl($this->foto_profil_path, $this->user_id);
        }

        if ($this->user && !empty($this->user->avatar_path)) {
            return $this->user->avatar_url;
        }

        return asset('images/no-user.svg');
    }

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

    /**
     * Mutator otomatis untuk standarisasi UPPERCASE nama & identitas
     */
    protected function namaLengkap(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value)),
        );
    }

    protected function namaPunggung(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    protected function tempatLahir(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? strtoupper(trim($value)) : null,
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

    public function rosters()
    {
        return $this->hasMany(Roster::class, 'pemain_id');
    }
}
