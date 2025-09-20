<?php

namespace App\Core;

class View
{
    public static function render(string $template, array $data = []): void
    {
        $file = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $template) . '.blade.php';
        if (!file_exists($file)) {
            http_response_code(404);
            echo 'View not found';
            return;
        }

        $flashBag = Session::pull('_flash', []);
        $oldInput = Session::pull('_old', []);
        $errors = Session::pull('_errors', []);

        $shared = [
            'appName' => env('APP_NAME', 'Aplikasi Arsip'),
            'authUser' => Auth::user(),
            'flash' => $flashBag,
            'errors' => $errors,
            'old' => $oldInput,
        ];

        $GLOBALS['__old'] = $oldInput;
        $GLOBALS['__errors'] = $errors;
        $GLOBALS['__flash'] = $flashBag;

        extract($shared);
        extract($data);

        include $file;
    }
}
