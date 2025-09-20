<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        Session::put('user_id', $user['id']);
        Session::put('user_role', $user['role']);
        Session::put('user_name', $user['name']);
        return true;
    }

    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }
        return User::find($id);
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function check(): bool
    {
        return Session::get('user_id') !== null;
    }

    public static function logout(): void
    {
        Session::forget('user_id');
        Session::forget('user_role');
        Session::forget('user_name');
    }

    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }
}
