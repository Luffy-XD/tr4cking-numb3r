<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    protected static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (static::$connection === null) {
            $config = config('database');
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $config['database']);
            try {
                static::$connection = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return static::$connection;
    }
}
