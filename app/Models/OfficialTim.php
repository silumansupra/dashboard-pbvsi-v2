<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialTim extends Model
{
    protected $table = 'trn_official';

    protected $fillable = [
        'peserta_tim_id',
        'user_id',
        'nama_lengkap',
        'jabatan',
        'nomor_lisensi',
        'scan_lisensi_path',
        'status_verifikasi',
    ];

    public function pesertaTim()
    {
        return $this->belongsTo(PesertaTim::class, 'peserta_tim_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Label jabatan yang mudah dibaca
     */
    public function getLabelJabatanAttribute(): string
    {
        return match ($this->jabatan) {
            'HEAD_COACH' => 'Pelatih Kepala (Head Coach)',
            'AST_COACH_1' => 'Asisten Pelatih 1',
            'AST_COACH_2' => 'Asisten Pelatih 2',
            'MANAGER' => 'Manajer Tim',
            'AST_MANAGER' => 'Asisten Manajer',
            'TRAINER' => 'Pelatih Fisik / Trainer',
            'THERAPIST' => 'Terapis / Fisioterapis',
            'MEDIS' => 'Dokter / Medis',
            'TRANSLATOR' => 'Penerjemah',
            default => str_replace('_', ' ', $this->jabatan),
        };
    }
}
