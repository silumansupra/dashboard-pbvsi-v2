<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'core_permissions';

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'core_role_has_permissions', 'permission_id', 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'core_model_has_permissions', 'permission_id', 'model_id')
            ->wherePivot('model_type', User::class);
    }
}
