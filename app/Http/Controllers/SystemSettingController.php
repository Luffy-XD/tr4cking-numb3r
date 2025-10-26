<?php

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\SystemSetting;
use App\Services\BackupService;

class SystemSettingController
{
    public function index(): Response
    {
        $settings = SystemSetting::current();
        return Response::make(view('settings.index', compact('settings')));
    }

    public function save(Request $request): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/settings']);
        }
        $validator = Validator::make($data, [
            'institution_name' => 'required',
            'institution_address' => 'required',
            'max_upload_size' => 'required',
        ]);
        if ($validator->fails()) {
            session_flash('status', 'Please fill in all required fields.');
            return Response::make('', 302, ['Location' => '/settings']);
        }
        $logo = $request->file('logo');
        $logoPath = null;
        if ($logo && $logo['error'] === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'svg'];
            $ext = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                session_flash('status', 'Logo must be an image (PNG, JPG, SVG).');
                return Response::make('', 302, ['Location' => '/settings']);
            }
            $logoPath = 'logos/' . time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $logo['name']);
            $target = storage_path('app/' . $logoPath);
            @mkdir(dirname($target), 0777, true);
            move_uploaded_file($logo['tmp_name'], $target);
        }
        $attributes = [
            'institution_name' => $data['institution_name'],
            'institution_address' => $data['institution_address'],
            'max_upload_size' => (int) $data['max_upload_size'],
            'file_mime' => 'application/pdf',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($logoPath) {
            $attributes['logo_path'] = $logoPath;
        }
        $settings = SystemSetting::current();
        if ($settings) {
            SystemSetting::update($settings['id'], $attributes);
        } else {
            $attributes['created_at'] = date('Y-m-d H:i:s');
            SystemSetting::create($attributes);
        }
        session_flash('status', 'Settings saved successfully.');
        return Response::make('', 302, ['Location' => '/settings']);
    }

    public function backup(): Response
    {
        $service = new BackupService();
        $path = $service->createBackup();
        $content = file_get_contents($path);
        return new Response($content, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }

    public function restore(Request $request): Response
    {
        $data = $request->all();
        if (!verify_csrf($data['_token'] ?? '')) {
            session_flash('status', 'Invalid session token.');
            return Response::make('', 302, ['Location' => '/settings']);
        }
        $file = $request->file('backup');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            session_flash('status', 'Backup file is required.');
            return Response::make('', 302, ['Location' => '/settings']);
        }
        $sql = file_get_contents($file['tmp_name']);
        $service = new BackupService();
        try {
            $service->restore($sql);
            session_flash('status', 'Database restored successfully.');
        } catch (\Throwable $e) {
            session_flash('status', 'Restore failed: ' . $e->getMessage());
        }
        return Response::make('', 302, ['Location' => '/settings']);
    }
}
