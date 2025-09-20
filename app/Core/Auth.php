<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_KEY = 'auth_user_id';

    public static function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        Session::start();
        Session::put(self::SESSION_KEY, $user['id']);
        Session::put('auth_role', $user['role']);
        return true;
    }

    public static function user(): ?array
    {
        Session::start();
        $id = Session::get(self::SESSION_KEY);
        if (!$id) {
            return null;
        }
        return User::find($id);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        Session::start();
        Session::forget(self::SESSION_KEY);
        Session::forget('auth_role');
        Session::destroy();
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    public static function role(): ?string
    {
        Session::start();
        return Session::get('auth_role');
    }
}
