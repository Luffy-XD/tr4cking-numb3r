<?php

namespace App\Models;

class AuditTrail extends Model
{
    protected static string $table = 'audit_trails';
    protected static array $fillable = [
        'user_id',
        'action',
        'description',
        'created_at',
    ];

    public static function log(int $userId, string $action, string $description): void
    {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
