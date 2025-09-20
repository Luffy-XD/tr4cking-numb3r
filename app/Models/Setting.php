<?php

namespace App\Models;

use App\Core\Database;
use DateTime;

class Setting
{
    public static function get(string $key, $default = null)
    {
        $row = Database::queryOne('SELECT * FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
        return $row['value'] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $existing = Database::queryOne('SELECT id FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
        $now = (new DateTime())->format('Y-m-d H:i:s');
        if ($existing) {
            Database::execute('UPDATE settings SET value = :value, updated_at = :updated_at WHERE id = :id', [
                'value' => $value,
                'updated_at' => $now,
                'id' => $existing['id'],
            ]);
        } else {
            Database::execute('INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (:key, :value, :created_at, :updated_at)', [
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public static function all(): array
    {
        $rows = Database::query('SELECT `key`, `value` FROM settings');
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }
}
