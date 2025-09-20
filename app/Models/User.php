<?php

namespace App\Models;

use App\Core\Database;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email', 'password', 'role', 'status'];

    public static function findByLogin(string $login): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :login OR name = :login LIMIT 1';
        return Database::queryOne($sql, ['login' => $login]);
    }

    public static function allStaff(): array
    {
        return self::all('role = :role', ['role' => 'staff'], 'name ASC');
    }
}
