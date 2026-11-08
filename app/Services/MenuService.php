<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Cache version key name
     */
    const CACHE_VER_KEY = 'menu_cache_ver';

    /**
     * Dapatkan versi cache menu saat ini
     */
    public static function getCacheVersion(): int
    {
        return (int) Cache::get(self::CACHE_VER_KEY, 1);
    }

    /**
     * Hapus dan invalidasi seluruh cache navigasi menu di semua user
     */
    public static function clearCache(): void
    {
        $current = (int) Cache::get(self::CACHE_VER_KEY, 1);
        Cache::forever(self::CACHE_VER_KEY, $current + 1);
    }

    /**
     * Ambil struktur navigasi menu terotorisasi untuk user tertentu dengan High-Performance Cache
     */
    public function getMenusForUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $version = self::getCacheVersion();
        $cacheKey = "user_menus_{$user->id}_v{$version}";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($user) {
            // Eager load active children dan permissions user
            $user->loadMissing(['roles.permissions', 'directPermissions']);

            $rootMenus = Menu::roots()
                ->active()
                ->with(['activeChildren'])
                ->ordered()
                ->get();

            $menuTree = [];

            foreach ($rootMenus as $root) {
                $filteredChildren = [];

                if ($root->activeChildren->isNotEmpty()) {
                    foreach ($root->activeChildren as $child) {
                        if ($user->canAccessMenu($child)) {
                            $filteredChildren[] = [
                                'id' => $child->id,
                                'parent_id' => $child->parent_id,
                                'title' => $child->title,
                                'route_name' => $child->route_name,
                                'url' => $child->resolved_url,
                                'icon' => $child->icon,
                                'badge_text' => $child->badge_text,
                                'badge_class' => $child->badge_class,
                                'target' => $child->target,
                            ];
                        }
                    }
                }

                // Menu parent ditampilkan jika:
                // 1. Memiliki anak yang lolos filter hak akses, ATAU
                // 2. Tidak punya anak, tetapi root menu itu sendiri lolos filter hak akses
                $canAccessRoot = $user->canAccessMenu($root);

                if (count($filteredChildren) > 0 || ($root->activeChildren->isEmpty() && $canAccessRoot)) {
                    $menuTree[] = [
                        'id' => $root->id,
                        'parent_id' => null,
                        'title' => $root->title,
                        'route_name' => $root->route_name,
                        'url' => $root->resolved_url,
                        'icon' => $root->icon,
                        'badge_text' => $root->badge_text,
                        'badge_class' => $root->badge_class,
                        'target' => $root->target,
                        'children' => $filteredChildren,
                    ];
                }
            }

            return $menuTree;
        });
    }

    /**
     * Ambil seluruh menu untuk admin interface (termasuk inactive)
     */
    public function getAllMenusTree(): array
    {
        $rootMenus = Menu::roots()
            ->with(['children'])
            ->ordered()
            ->get();

        $tree = [];

        foreach ($rootMenus as $root) {
            $children = [];
            foreach ($root->children as $child) {
                $children[] = [
                    'id' => $child->id,
                    'parent_id' => $child->parent_id,
                    'title' => $child->title,
                    'route_name' => $child->route_name,
                    'url' => $child->url,
                    'icon' => $child->icon,
                    'permission_key' => $child->permission_key,
                    'order' => $child->order,
                    'is_active' => $child->is_active,
                    'badge_text' => $child->badge_text,
                    'badge_class' => $child->badge_class,
                    'target' => $child->target,
                ];
            }

            $tree[] = [
                'id' => $root->id,
                'parent_id' => null,
                'title' => $root->title,
                'route_name' => $root->route_name,
                'url' => $root->url,
                'icon' => $root->icon,
                'permission_key' => $root->permission_key,
                'order' => $root->order,
                'is_active' => $root->is_active,
                'badge_text' => $root->badge_text,
                'badge_class' => $root->badge_class,
                'target' => $root->target,
                'children' => $children,
            ];
        }

        return $tree;
    }
}
