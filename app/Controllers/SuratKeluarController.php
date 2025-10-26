<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Models\SuratKeluar;
use App\Models\Kategori;
use App\Models\AuditLog;

class SuratKeluarController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $surat = SuratKeluar::withRelations($user['id'] ?? null, $user['role'] ?? null);
        view('surat.keluar-index', compact('surat'));
    }

    public function create(): void
    {
        $kategori = Kategori::all();
        view('surat.keluar-form', ['kategori' => $kategori, 'surat' => null]);
    }

    public function store(): void
    {
        $data = $this->validate([
            'no_surat' => 'required',
            'penerima' => 'required',
            'tanggal_keluar' => 'required',
            'perihal' => 'required',
            'kategori_id' => 'required',
        ]);

        $file = $this->request->file('file');
        if (!$file) {
            Session::put('_errors', ['file' => ['File PDF wajib diunggah']]);
            Session::put('_old', $data);
            redirect($_SERVER['HTTP_REFERER'] ?? '/surat-keluar/create');
        }

        $filename = $this->uploadFile($file, 'surat_keluar');
        $userId = (int) Auth::id();
        $id = SuratKeluar::create([
            'no_surat' => $data['no_surat'],
            'penerima' => $data['penerima'],
            'tanggal_keluar' => $data['tanggal_keluar'],
            'perihal' => $data['perihal'],
            'kategori_id' => (int) $data['kategori_id'],
            'file' => $filename,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        AuditLog::record($userId, 'create', 'surat_keluar', 'Menambah surat keluar #' . $id);
        $this->success('Surat keluar berhasil ditambahkan', '/surat-keluar');
    }

    public function show(int $id): void
    {
        $surat = SuratKeluar::find($id);
        if (!$surat) {
            $this->error('Surat tidak ditemukan', '/surat-keluar');
        }
        $kategori = Kategori::find($surat['kategori_id']);
        view('surat.keluar-show', compact('surat', 'kategori'));
    }

    public function edit(int $id): void
    {
        $surat = SuratKeluar::find($id);
        $this->authorizeOwner($surat);
        $kategori = Kategori::all();
        view('surat.keluar-form', compact('surat', 'kategori'));
    }

    public function update(int $id): void
    {
        $surat = SuratKeluar::find($id);
        $this->authorizeOwner($surat);

        $data = $this->validate([
            'no_surat' => 'required',
            'penerima' => 'required',
            'tanggal_keluar' => 'required',
            'perihal' => 'required',
            'kategori_id' => 'required',
        ]);

        $payload = [
            'no_surat' => $data['no_surat'],
            'penerima' => $data['penerima'],
            'tanggal_keluar' => $data['tanggal_keluar'],
            'perihal' => $data['perihal'],
            'kategori_id' => (int) $data['kategori_id'],
            'updated_by' => Auth::id(),
        ];

        $file = $this->request->file('file');
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $payload['file'] = $this->uploadFile($file, 'surat_keluar', $surat['file']);
        }

        SuratKeluar::update($id, $payload);
        AuditLog::record((int) Auth::id(), 'update', 'surat_keluar', 'Memperbarui surat keluar #' . $id);
        $this->success('Surat keluar diperbarui', '/surat-keluar');
    }

    public function destroy(int $id): void
    {
        $surat = SuratKeluar::find($id);
        $this->authorizeOwner($surat);
        if ($surat && !empty($surat['file'])) {
            $path = __DIR__ . '/../../public/uploads/surat_keluar/' . $surat['file'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
        SuratKeluar::delete($id);
        AuditLog::record((int) Auth::id(), 'delete', 'surat_keluar', 'Menghapus surat keluar #' . $id);
        $this->success('Surat keluar dihapus', '/surat-keluar');
    }

    public function download(int $id): void
    {
        $surat = SuratKeluar::find($id);
        $this->authorizeOwner($surat, false);
        if (!$surat) {
            $this->error('Surat tidak ditemukan', '/surat-keluar');
        }
        $path = __DIR__ . '/../../public/uploads/surat_keluar/' . $surat['file'];
        if (!file_exists($path)) {
            $this->error('File tidak tersedia', '/surat-keluar');
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($surat['file']) . '"');
        readfile($path);
        exit;
    }

    private function authorizeOwner(?array $surat, bool $strict = true): void
    {
        if (!$surat) {
            $this->error('Data tidak ditemukan', '/surat-keluar');
        }
        $user = Auth::user();
        if ($user['role'] === 'admin') {
            return;
        }
        if ($surat['created_by'] != $user['id']) {
            $this->error('Anda tidak memiliki akses', '/surat-keluar');
        }
    }

    private function uploadFile(array $file, string $folder, ?string $existing = null): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->error('Gagal mengunggah file', '/surat-keluar');
        }

        $max = (int) env('UPLOAD_MAX_SIZE', 10485760);
        if ($file['size'] > $max) {
            $this->error('Ukuran file melebihi batas 10MB', '/surat-keluar');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($mime !== 'application/pdf') {
            $this->error('File harus berformat PDF', '/surat-keluar');
        }

        $directory = __DIR__ . '/../../public/uploads/' . $folder;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filename = uniqid('surat_', true) . '.pdf';
        $destination = $directory . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->error('Tidak dapat menyimpan file', '/surat-keluar');
        }

        if ($existing) {
            $oldPath = $directory . '/' . $existing;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        return $filename;
    }
}
