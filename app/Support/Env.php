<?php

namespace App\Support;

class Env
{
    public static function load(string $basePath): void
    {
        $envFile = rtrim($basePath, '/') . '/.env';
        if (!is_readable($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));
            $value = self::sanitizeValue($value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    protected static function sanitizeValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }
        return $value;
    }
}
