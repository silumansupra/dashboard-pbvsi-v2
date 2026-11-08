<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTim extends Model
{
    protected $table = 'trn_user_tim';

    protected $fillable = [
        'user_id',
        'peserta_tim_id',
        'role_tim',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(PesertaTim::class, 'peserta_tim_id');
    }
}
