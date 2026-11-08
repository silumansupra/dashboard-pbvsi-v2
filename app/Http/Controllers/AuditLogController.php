<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Tampilkan daftar audit log platform dengan filter interaktif
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'module' => $request->query('module'),
            'action' => $request->query('action'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        $query = AuditLog::with('user')->filter($filters)->orderByDesc('id');

        $logs = $query->paginate(25)->withQueryString();

        // Statistik audit log
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', Carbon::today())->count(),
            'auth' => AuditLog::where('module', 'auth')->count(),
            'distinct_users' => AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        // Daftar modul untuk dropdown filter
        $availableModules = [
            'auth' => 'Autentikasi & Keamanan',
            'team' => 'Manajemen Tim',
            'personel' => 'Database Personel',
            'master' => 'Master Data',
            'profile' => 'Profil Pengguna',
            'system' => 'Sistem & Konfigurasi',
        ];

        return view('audit_logs.index', compact('logs', 'stats', 'filters', 'availableModules'));
    }

    /**
     * Ambil detail satu audit log (JSON untuk modal popup)
     */
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $log->id,
                'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
                'module' => $log->module,
                'action' => $log->action,
                'description' => $log->description,
                'user' => [
                    'id' => $log->user_id,
                    'name' => $log->user_name ?? ($log->user ? $log->user->name : 'Sistem / Tamu'),
                    'username' => $log->user_username ?? ($log->user ? $log->user->username : '-'),
                    'role' => $log->user_role ?? '-',
                ],
                'ip_address' => $log->ip_address ?? '-',
                'user_agent' => $log->user_agent ?? '-',
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'metadata' => $log->metadata,
            ]
        ]);
    }
}
