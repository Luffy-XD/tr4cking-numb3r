<?php

namespace App\Core\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Silakan login terlebih dahulu');
            redirect('/login');
        }
    }
}
