<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected static string $table;
    protected static array $fillable = [];

    public static function all(array $conditions = [], ?string $order = null, ?int $limit = null): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT * FROM ' . static::$table;
        $params = [];
        if ($conditions) {
            $clauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $clauses[] = $field;
                    $params = array_merge($params, $value);
                } else {
                    $clauses[] = "$field = ?";
                    $params[] = $value;
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        }
        if ($limit) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM ' . static::$table . ' WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $attributes): int
    {
        $pdo = Database::connection();
        $fields = array_intersect_key($attributes, array_flip(static::$fillable));
        $columns = array_keys($fields);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($fields));
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $attributes): void
    {
        $pdo = Database::connection();
        $fields = array_intersect_key($attributes, array_flip(static::$fillable));
        $columns = array_keys($fields);
        if (!$columns) {
            return;
        }
        $assignments = implode(',', array_map(fn($column) => "$column = ?", $columns));
        $sql = 'UPDATE ' . static::$table . ' SET ' . $assignments . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $params = array_values($fields);
        $params[] = $id;
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM ' . static::$table . ' WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function count(array $conditions = []): int
    {
        $pdo = Database::connection();
        $sql = 'SELECT COUNT(*) FROM ' . static::$table;
        $params = [];
        if ($conditions) {
            $clauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $clauses[] = $field;
                    $params = array_merge($params, $value);
                } else {
                    $clauses[] = "$field = ?";
                    $params[] = $value;
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function query(string $sql, array $params = []): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function raw(string $sql, array $params = []): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}
