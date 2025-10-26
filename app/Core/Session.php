<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $flash = $_SESSION['_flash'] ?? [];
        $flash[$key] = $value;
        $_SESSION['_flash'] = $flash;
    }

    public static function getFlash(string $key, $default = null)
    {
        $flash = $_SESSION['_flash'] ?? [];
        return $flash[$key] ?? $default;
    }

    public static function pull(string $key, $default = null)
    {
        $value = $_SESSION[$key] ?? $default;
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return $value;
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}
