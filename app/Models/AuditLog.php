<?php

namespace App\Models;

use App\Core\Database;
use DateTime;

class AuditLog
{
    public static function record(int $userId, string $action, string $module, string $description): void
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        Database::execute('INSERT INTO audit_logs (user_id, action, module, description, created_at) VALUES (:user_id, :action, :module, :description, :created_at)', [
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'created_at' => $now,
        ]);
    }

    public static function recent(int $limit = 10): array
    {
        return Database::query('SELECT al.*, u.name FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT ' . (int) $limit);
    }
}
