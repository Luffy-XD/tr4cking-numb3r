<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\AuditTrail;
use App\Models\OutgoingLetter;
use App\Models\LetterCategory;

class OutgoingLetterController
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $user = Auth::user();
        $letters = OutgoingLetter::paginate($user['role'] === 'staff' ? $user['id'] : null, $search);
        $categories = LetterCategory::all(order: 'nama_kategori ASC');
        return Response::make(view('letters.outgoing', compact('letters', 'categories', 'user')));
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $validator = Validator::make($data, [
            'no_surat' => 'required',
            'receiver' => 'required',
            'tanggal_keluar' => 'required|date',
            'subject' => 'required',
            'kategori_id' => 'required',
        ]);
        $file = $request->file('file');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            session_flash('status', 'File upload is required.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            session_flash('status', 'Only PDF files are allowed.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        if ($file['size'] > 10 * 1024 * 1024) {
            session_flash('status', 'File size must not exceed 10 MB.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        if ($validator->fails()) {
            session_flash('status', 'Please check the form fields.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }

        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file['name']);
        $target = storage_path('uploads/outgoing/' . $fileName);
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            session_flash('status', 'Failed to store uploaded file.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $userId = Auth::id();
        OutgoingLetter::create([
            'no_surat' => $data['no_surat'],
            'receiver' => $data['receiver'],
            'tanggal_keluar' => $data['tanggal_keluar'],
            'subject' => $data['subject'],
            'kategori_id' => $data['kategori_id'],
            'file' => $fileName,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        AuditTrail::log($userId, 'create', 'Created outgoing letter ' . $data['no_surat']);
        session_flash('status', 'Outgoing letter created successfully.');
        return Response::make('', 302, ['Location' => '/outgoing-letters']);
    }

    public function show(int $id): Response
    {
        $letter = OutgoingLetter::find($id);
        if (!$letter) {
            return Response::make('Letter not found', 404);
        }
        $category = LetterCategory::find($letter['kategori_id']);
        return Response::make(view('letters.outgoing_show', compact('letter', 'category')));
    }

    public function download(Request $request, int $id): Response
    {
        $letter = OutgoingLetter::find($id);
        if (!$letter) {
            return Response::make('File not found', 404);
        }
        $filePath = storage_path('uploads/outgoing/' . $letter['file']);
        if (!file_exists($filePath)) {
            return Response::make('File missing', 404);
        }
        $content = file_get_contents($filePath);
        $disposition = $request->input('preview') ? 'inline' : 'attachment';
        return new Response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . basename($filePath) . '"',
        ]);
    }

    public function destroy(int $id): Response
    {
        $letter = OutgoingLetter::find($id);
        if (!$letter) {
            session_flash('status', 'Letter not found.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $user = Auth::user();
        if ($user['role'] !== 'admin' && $letter['created_by'] !== $user['id']) {
            session_flash('status', 'You are not allowed to delete this record.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        OutgoingLetter::delete($id);
        $filePath = storage_path('uploads/outgoing/' . $letter['file']);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        AuditTrail::log($user['id'], 'delete', 'Deleted outgoing letter ' . $letter['no_surat']);
        session_flash('status', 'Outgoing letter deleted.');
        return Response::make('', 302, ['Location' => '/outgoing-letters']);
    }

    public function update(Request $request, int $id): Response
    {
        $letter = OutgoingLetter::find($id);
        if (!$letter) {
            session_flash('status', 'Letter not found.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $user = Auth::user();
        if ($user['role'] !== 'admin' && $letter['created_by'] !== $user['id']) {
            session_flash('status', 'You are not allowed to edit this record.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $validator = Validator::make($data, [
            'no_surat' => 'required',
            'receiver' => 'required',
            'tanggal_keluar' => 'required|date',
            'subject' => 'required',
            'kategori_id' => 'required',
        ]);
        if ($validator->fails()) {
            session_flash('status', 'Please check the form fields.');
            return Response::make('', 302, ['Location' => '/outgoing-letters']);
        }
        $file = $request->file('file');
        $fileName = $letter['file'];
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
                session_flash('status', 'Only PDF files are allowed.');
                return Response::make('', 302, ['Location' => '/outgoing-letters']);
            }
            if ($file['size'] > 10 * 1024 * 1024) {
                session_flash('status', 'File size must not exceed 10 MB.');
                return Response::make('', 302, ['Location' => '/outgoing-letters']);
            }
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file['name']);
            $target = storage_path('uploads/outgoing/' . $fileName);
            move_uploaded_file($file['tmp_name'], $target);
            $oldFile = storage_path('uploads/outgoing/' . $letter['file']);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        OutgoingLetter::update($id, [
            'no_surat' => $data['no_surat'],
            'receiver' => $data['receiver'],
            'tanggal_keluar' => $data['tanggal_keluar'],
            'subject' => $data['subject'],
            'kategori_id' => $data['kategori_id'],
            'file' => $fileName,
            'updated_by' => $user['id'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        AuditTrail::log($user['id'], 'update', 'Updated outgoing letter ' . $data['no_surat']);
        session_flash('status', 'Outgoing letter updated.');
        return Response::make('', 302, ['Location' => '/outgoing-letters']);
    }
}
