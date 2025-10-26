<?php

use App\Core\View;
use App\Core\Session;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = [])
    {
        return View::render($template, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path)
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        Session::start();
        $token = Session::get('_token');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            Session::put('_token', $token);
        }
        return $token;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        $data = $GLOBALS['__old'] ?? [];
        return $data[$key] ?? $default;
    }
}

if (!function_exists('error')) {
    function error(string $key)
    {
        $errors = $GLOBALS['__errors'] ?? [];
        return $errors[$key] ?? [];
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'd/m/Y')
    {
        if (!$date) {
            return '-';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        $base = __DIR__ . '/../storage';
        if ($path) {
            $base .= '/' . ltrim($path, '/');
        }
        $directory = dirname($base);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        return $base;
    }
}
