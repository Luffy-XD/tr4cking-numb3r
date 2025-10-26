<?php

use App\Core\Application;
use App\Support\Env;

require __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Env::load(__DIR__ . '/..');

$app = new Application();
$app->boot();

return $app;
