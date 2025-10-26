<?php

namespace App\Models;

use App\Core\Database;
use DateTime;

abstract class BaseModel
{
    protected static string $table;
    protected static array $fillable = [];

    public static function all(string $where = '', array $params = [], string $order = ''): array
    {
        $sql = 'SELECT * FROM ' . static::$table;
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        }
        return Database::query($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE id = :id LIMIT 1';
        return Database::queryOne($sql, ['id' => $id]);
    }

    public static function create(array $data): int
    {
        $filtered = self::filterData($data);
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $filtered['created_at'] = $now;
        $filtered['updated_at'] = $now;
        $columns = array_keys($filtered);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        Database::execute($sql, $filtered);
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $filtered = self::filterData($data);
        $filtered['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
        $columns = array_keys($filtered);
        $assignments = implode(',', array_map(fn($col) => $col . ' = :' . $col, $columns));
        $filtered['id'] = $id;
        $sql = 'UPDATE ' . static::$table . ' SET ' . $assignments . ' WHERE id = :id';
        return Database::execute($sql, $filtered);
    }

    public static function delete(int $id): bool
    {
        $sql = 'DELETE FROM ' . static::$table . ' WHERE id = :id';
        return Database::execute($sql, ['id' => $id]);
    }

    public static function count(string $where = '', array $params = []): int
    {
        $sql = 'SELECT COUNT(*) as aggregate FROM ' . static::$table;
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }
        $row = Database::queryOne($sql, $params);
        return (int) ($row['aggregate'] ?? 0);
    }

    protected static function filterData(array $data): array
    {
        $filtered = [];
        foreach (static::$fillable as $field) {
            if (array_key_exists($field, $data)) {
                $filtered[$field] = $data[$field];
            }
        }
        return $filtered;
    }
}
