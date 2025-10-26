<?php

namespace App\Core;

class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            $example = $path . '.example';
            if (file_exists($example)) {
                $path = $example;
            } else {
                return;
            }
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
            $name = trim($name);
            $value = trim($value);
            if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}
