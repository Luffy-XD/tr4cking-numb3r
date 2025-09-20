<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Core\Auth;
use App\Core\Session;

class UserController extends Controller
{
    public function index(): void
    {
        $users = User::all('', [], 'name ASC');
        view('users.index', compact('users'));
    }

    public function create(): void
    {
        view('users.form', ['user' => null]);
    }

    public function store(): void
    {
        $data = $this->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ]);
        $password = $this->request->input('password') ?: 'password123';
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $data['status'] = $this->request->input('status', 'active');
        $id = User::create($data);
        AuditLog::record((int) Auth::id(), 'create', 'user', 'Menambah user #' . $id);
        Session::flash('success', 'User berhasil dibuat dengan password default: ' . $password);
        redirect('/users');
    }

    public function edit(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            $this->error('User tidak ditemukan', '/users');
        }
        view('users.form', compact('user'));
    }

    public function update(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            $this->error('User tidak ditemukan', '/users');
        }
        $data = $this->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ]);
        $data['status'] = $this->request->input('status', 'active');
        User::update($id, $data);
        AuditLog::record((int) Auth::id(), 'update', 'user', 'Memperbarui user #' . $id);
        $this->success('User diperbarui', '/users');
    }

    public function resetPassword(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            $this->error('User tidak ditemukan', '/users');
        }
        $default = 'password123';
        User::update($id, ['password' => password_hash($default, PASSWORD_DEFAULT)]);
        AuditLog::record((int) Auth::id(), 'update', 'user', 'Reset password user #' . $id);
        Session::flash('success', 'Password berhasil direset ke: ' . $default);
        redirect('/users');
    }

    public function destroy(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            $this->error('User tidak ditemukan', '/users');
        }
        if ($user['id'] == Auth::id()) {
            $this->error('Tidak dapat menghapus akun sendiri', '/users');
        }
        User::delete($id);
        AuditLog::record((int) Auth::id(), 'delete', 'user', 'Menghapus user #' . $id);
        $this->success('User dihapus', '/users');
    }
}
