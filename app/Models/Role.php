<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'core_roles';
    protected $fillable = ['name', 'guard_name', 'display_name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'core_model_has_roles', 'role_id', 'model_id');
    }
}
