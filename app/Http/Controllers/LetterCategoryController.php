<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\LetterCategory;

class LetterCategoryController
{
    public function index(): Response
    {
        $categories = LetterCategory::withCount();
        return Response::make(view('categories.index', compact('categories')));
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/letter-categories']);
        }
        $validator = Validator::make($data, [
            'nama_kategori' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            session_flash('status', 'Please fill all fields.');
            return Response::make('', 302, ['Location' => '/letter-categories']);
        }
        if (isset($data['id']) && $data['id']) {
            LetterCategory::update((int) $data['id'], [
                'nama_kategori' => $data['nama_kategori'],
                'description' => $data['description'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            session_flash('status', 'Category updated successfully.');
        } else {
            LetterCategory::create([
                'nama_kategori' => $data['nama_kategori'],
                'description' => $data['description'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            session_flash('status', 'Category created successfully.');
        }
        return Response::make('', 302, ['Location' => '/letter-categories']);
    }

    public function destroy(int $id): Response
    {
        LetterCategory::delete($id);
        session_flash('status', 'Category deleted.');
        return Response::make('', 302, ['Location' => '/letter-categories']);
    }
}
