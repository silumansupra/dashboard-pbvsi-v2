<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Menu extends Model
{
    protected $table = 'core_menus';

    protected $fillable = [
        'parent_id',
        'title',
        'route_name',
        'url',
        'icon',
        'permission_key',
        'order',
        'is_active',
        'badge_text',
        'badge_class',
        'target',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function activeChildren()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order', 'asc');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getResolvedUrlAttribute(): string
    {
        if (!empty($this->route_name) && Route::has($this->route_name)) {
            try {
                return route($this->route_name);
            } catch (\Throwable $e) {
                // If route requires parameters, fallback to url or #
            }
        }

        if (!empty($this->url)) {
            return $this->url;
        }

        return '#!';
    }

    public function isCurrentRoute(): bool
    {
        if (!empty($this->route_name)) {
            // Wildcard matching for subroutes
            $baseRoute = explode('.', $this->route_name)[0];
            return request()->routeIs($this->route_name) || request()->routeIs($baseRoute . '.*');
        }

        if (!empty($this->url) && $this->url !== '#!') {
            return request()->is(ltrim($this->url, '/') . '*');
        }

        return false;
    }
}
