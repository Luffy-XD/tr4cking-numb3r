<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return rtrim(__DIR__ . '/../..', '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return base_path('resources' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        if ($value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            default:
                return $value;
        }
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        static $configs = [];

        if (!$configs) {
            foreach (glob(config_path('*.php')) as $file) {
                $name = basename($file, '.php');
                $configs[$name] = require $file;
            }
        }

        [$file, $item] = array_pad(explode('.', $key, 2), 2, null);
        if ($item === null) {
            return $configs[$file] ?? $default;
        }

        return $configs[$file][$item] ?? $default;
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = []): string
    {
        $path = resource_path('views/' . str_replace('.', '/', $template) . '.blade.php');
        if (!file_exists($path)) {
            throw new RuntimeException("View {$template} not found.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return rtrim($scheme . '://' . $host, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url($path);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        if (!isset($_SESSION['_old_input'][$key])) {
            return $default;
        }
        $value = $_SESSION['_old_input'][$key];
        unset($_SESSION['_old_input'][$key]);
        return $value;
    }
}

if (!function_exists('session_flash')) {
    function session_flash(string $key, $value = null)
    {
        if ($value === null) {
            $data = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $data;
        }
        $_SESSION['_flash'][$key] = $value;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(string $token): bool
    {
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
    }
}
