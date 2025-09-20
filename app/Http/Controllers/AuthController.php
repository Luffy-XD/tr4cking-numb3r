<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

class AuthController
{
    public function showLoginForm(Request $request): Response
    {
        if (Auth::check()) {
            return Response::make('', 302, ['Location' => '/dashboard']);
        }
        $error = session_flash('auth_error');
        $success = session_flash('status');
        return Response::make(view('auth.login', compact('error', 'success')));
    }

    public function login(Request $request): Response
    {
        $data = $request->all();
        $_SESSION['_old_input'] = $data;
        $validator = Validator::make($data, [
            'login' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            Session::flash('auth_error', 'Please fill in your credentials.');
            return Response::make('', 302, ['Location' => '/login']);
        }

        if (!verify_csrf($data['_token'] ?? '')) {
            Session::flash('auth_error', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/login']);
        }

        if (!Auth::attempt($data['login'], $data['password'])) {
            Session::flash('auth_error', 'Invalid credentials.');
            return Response::make('', 302, ['Location' => '/login']);
        }

        unset($_SESSION['_old_input']);
        return Response::make('', 302, ['Location' => '/dashboard']);
    }

    public function logout(): Response
    {
        Auth::logout();
        Session::flash('status', 'You have been logged out.');
        return Response::make('', 302, ['Location' => '/login']);
    }
}
