<?php

namespace App\Core\Middleware;

use App\Core\Auth;
use App\Core\Session;

class RoleMiddleware
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user['role'], $this->roles, true)) {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini');
            redirect('/dashboard');
        }
    }
}
