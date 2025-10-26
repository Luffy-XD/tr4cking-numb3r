<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        view('auth.login');
    }

    public function login(): void
    {
        $data = $this->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($data['login'], $data['password'])) {
            Session::flash('success', 'Selamat datang kembali');
            redirect('/dashboard');
        }

        Session::flash('error', 'Kredensial tidak valid');
        Session::put('_old', ['login' => $data['login']]);
        redirect('/login');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::start();
        Session::flash('success', 'Berhasil logout');
        redirect('/login');
    }
}
