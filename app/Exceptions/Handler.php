<?php

namespace App\Exceptions;

use Throwable;

class Handler
{
    public function report(Throwable $e): void
    {
        error_log($e->getMessage());
    }

    public function render(): void
    {
        http_response_code(500);
        echo 'An unexpected error occurred.';
    }
}
