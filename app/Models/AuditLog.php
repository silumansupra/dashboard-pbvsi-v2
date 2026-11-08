<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'core_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_username',
        'user_role',
        'module',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope query filter untuk modul, aksi, tanggal, dan pencarian teks
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_username', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        });

        $query->when($filters['module'] ?? null, function ($q, $module) {
            if ($module !== 'all') {
                $q->where('module', $module);
            }
        });

        $query->when($filters['action'] ?? null, function ($q, $action) {
            if ($action !== 'all') {
                $q->where('action', $action);
            }
        });

        $query->when($filters['date_from'] ?? null, function ($q, $dateFrom) {
            $q->whereDate('created_at', '>=', $dateFrom);
        });

        $query->when($filters['date_to'] ?? null, function ($q, $dateTo) {
            $q->whereDate('created_at', '<=', $dateTo);
        });

        return $query;
    }
}
