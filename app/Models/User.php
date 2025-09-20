<?php

namespace App\Models;

class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'created_at',
        'updated_at',
    ];

    public static function findByLogin(string $login): ?array
    {
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR name = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function staff(): array
    {
        return static::all(['role = ?' => ['staff']], 'name ASC');
    }
}
