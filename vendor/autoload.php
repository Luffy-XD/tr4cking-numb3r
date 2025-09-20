<?php

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
        'Database\\Seeders\\' => __DIR__ . '/../database/seeders/',
        'Database\\Migrations\\' => __DIR__ . '/../database/migrations/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return;
    }
});

require_once __DIR__ . '/../app/Support/helpers.php';
