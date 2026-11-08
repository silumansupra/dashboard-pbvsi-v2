<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use App\Services\MenuService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class MenuManagerController extends Controller
{
    /**
     * Memastikan hanya user dengan role superadmin atau permission menu.manage yang dapat mengakses
     */
    protected function authorizeMenu(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->hasRole('superadmin') && !$user->hasPermissionTo('menu.manage'))) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola Menu Navigasi.');
        }
    }

    /**
     * Tampilkan halaman manajemen menu navigasi
     */
    public function index(Request $request, MenuService $menuService)
    {
        $this->authorizeMenu();

        $menus = Menu::roots()
            ->with(['children' => function ($q) {
                $q->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();

        $parentOptions = Menu::roots()->orderBy('order', 'asc')->get();
        $permissions = Permission::orderBy('name', 'asc')->get();

        return view('master.menus_index', compact('menus', 'parentOptions', 'permissions'));
    }

    /**
     * Simpan menu atau submenu baru
     */
    public function store(Request $request)
    {
        $this->authorizeMenu();

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:core_menus,id',
            'route_name' => 'nullable|string|max:100',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'permission_key' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|in:0,1',
            'badge_text' => 'nullable|string|max:50',
            'badge_class' => 'nullable|string|max:50',
            'target' => 'required|in:_self,_blank',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $validated['order'] = $validated['order'] ?? (Menu::where('parent_id', $validated['parent_id'])->max('order') + 1);

        $menu = Menu::create($validated);

        MenuService::clearCache();

        AuditLogService::log(
            'menu.create',
            "Membuat menu baru '{$menu->title}' (#{$menu->id})",
            ['user' => auth()->user(), 'metadata' => $menu->toArray()]
        );

        return redirect()->route('master.menus.index')
            ->with('success', "Menu '{$menu->title}' berhasil ditambahkan ke sistem.");
    }

    /**
     * Dapatkan detail satu menu (JSON untuk form edit modal)
     */
    public function show($id)
    {
        $this->authorizeMenu();

        $menu = Menu::findOrFail($id);

        return response()->json($menu);
    }

    /**
     * Perbarui data menu
     */
    public function update(Request $request, $id)
    {
        $this->authorizeMenu();

        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:core_menus,id|not_in:' . $menu->id,
            'route_name' => 'nullable|string|max:100',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'permission_key' => 'nullable|string|max:100',
            'order' => 'required|integer',
            'is_active' => 'required|in:0,1',
            'badge_text' => 'nullable|string|max:50',
            'badge_class' => 'nullable|string|max:50',
            'target' => 'required|in:_self,_blank',
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        $oldValues = $menu->toArray();
        $menu->update($validated);

        MenuService::clearCache();

        AuditLogService::log(
            'menu.update',
            "Memperbarui menu '{$menu->title}' (#{$menu->id})",
            [
                'user' => auth()->user(),
                'old_values' => $oldValues,
                'new_values' => $menu->toArray()
            ]
        );

        return redirect()->route('master.menus.index')
            ->with('success', "Menu '{$menu->title}' berhasil diperbarui.");
    }

    /**
     * Hapus menu beserta submenunya
     */
    public function destroy($id)
    {
        $this->authorizeMenu();

        $menu = Menu::findOrFail($id);
        $title = $menu->title;
        $menu->delete();

        MenuService::clearCache();

        AuditLogService::log(
            'menu.delete',
            "Menghapus menu '{$title}' (#{$id})",
            ['user' => auth()->user()]
        );

        return redirect()->route('master.menus.index')
            ->with('success', "Menu '{$title}' dan seluruh submenunya telah dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif menu secara instan via AJAX
     */
    public function toggle($id)
    {
        $this->authorizeMenu();

        $menu = Menu::findOrFail($id);
        $menu->is_active = !$menu->is_active;
        $menu->save();

        MenuService::clearCache();

        AuditLogService::log(
            'menu.toggle',
            "Mengubah status menu '{$menu->title}' menjadi " . ($menu->is_active ? 'AKTIF' : 'NONAKTIF'),
            ['user' => auth()->user()]
        );

        return response()->json([
            'success' => true,
            'is_active' => $menu->is_active,
            'message' => "Menu '{$menu->title}' berhasil " . ($menu->is_active ? 'diaktifkan' : 'dinonaktifkan') . ".",
        ]);
    }
}
