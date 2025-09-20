<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Services\BackupService;
use App\Models\Setting;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index(): void
    {
        $settings = Setting::all();
        view('settings.index', compact('settings'));
    }

    public function updateIdentity(): void
    {
        $data = $this->validate([
            'instansi_nama' => 'required',
            'instansi_alamat' => 'required',
        ]);

        Setting::set('instansi_nama', $data['instansi_nama']);
        Setting::set('instansi_alamat', $data['instansi_alamat']);

        $logo = $this->request->file('instansi_logo');
        if ($logo && $logo['error'] !== UPLOAD_ERR_NO_FILE) {
            $filename = $this->uploadLogo($logo);
            Setting::set('instansi_logo', $filename);
        }

        AuditLog::record((int) Auth::id(), 'update', 'settings', 'Memperbarui identitas instansi');
        $this->success('Pengaturan instansi diperbarui', '/settings');
    }

    public function backup(): void
    {
        $service = new BackupService();
        $path = storage_path('backups/backup_' . date('Ymd_His') . '.sql');
        $service->save($path);
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        readfile($path);
        unlink($path);
        exit;
    }

    public function restore(): void
    {
        $file = $this->request->file('backup_file');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->error('File backup tidak valid', '/settings');
        }
        $content = file_get_contents($file['tmp_name']);
        $service = new BackupService();
        $service->restore($content);
        AuditLog::record((int) Auth::id(), 'update', 'settings', 'Melakukan restore database');
        $this->success('Restore database berhasil', '/settings');
    }

    private function uploadLogo(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->error('Gagal mengunggah logo', '/settings');
        }
        $allowed = ['image/png', 'image/jpeg', 'image/svg+xml'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed, true)) {
            $this->error('Format logo harus PNG, JPG, atau SVG', '/settings');
        }
        $ext = $mime === 'image/png' ? 'png' : ($mime === 'image/svg+xml' ? 'svg' : 'jpg');
        $directory = __DIR__ . '/../../public/uploads/logo';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $filename = 'logo_instansi.' . $ext;
        $destination = $directory . '/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return $filename;
    }
}
