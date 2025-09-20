<?php

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\AuditTrail;
use App\Models\User;

class UserController
{
    public function index(): Response
    {
        $users = User::all(order: 'created_at DESC');
        return Response::make(view('users.index', compact('users')));
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            session_flash('status', 'Please check the form fields.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? 'staff',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        AuditTrail::log($_SESSION['user_id'], 'create', 'Created staff ' . $data['name']);
        session_flash('status', 'User created successfully.');
        return Response::make('', 302, ['Location' => '/users']);
    }

    public function update(Request $request, int $id): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            session_flash('status', 'Please check the form fields.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        User::update($id, [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        AuditTrail::log($_SESSION['user_id'], 'update', 'Updated user ' . $data['name']);
        session_flash('status', 'User updated successfully.');
        return Response::make('', 302, ['Location' => '/users']);
    }

    public function resetPassword(Request $request, int $id): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        $password = $data['password'] ?? 'password123';
        User::update($id, [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        AuditTrail::log($_SESSION['user_id'], 'update', 'Reset password for user ID ' . $id);
        session_flash('status', 'Password reset successfully.');
        return Response::make('', 302, ['Location' => '/users']);
    }

    public function destroy(Request $request, int $id): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/users']);
        }
        User::delete($id);
        AuditTrail::log($_SESSION['user_id'], 'delete', 'Deleted user ID ' . $id);
        session_flash('status', 'User removed successfully.');
        return Response::make('', 302, ['Location' => '/users']);
    }
}
