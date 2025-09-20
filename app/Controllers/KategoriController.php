<?php

namespace App\Controllers;

use App\Models\Kategori;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\AuditLog;
use App\Core\Auth;

class KategoriController extends Controller
{
    public function index(): void
    {
        $kategori = Kategori::withCount();
        view('kategori.index', compact('kategori'));
    }

    public function create(): void
    {
        view('kategori.form', ['kategori' => null]);
    }

    public function store(): void
    {
        $data = $this->validate([
            'nama_kategori' => 'required',
            'deskripsi' => 'required',
        ]);

        $id = Kategori::create($data);
        AuditLog::record((int) Auth::id(), 'create', 'kategori', 'Menambah kategori #' . $id);
        $this->success('Kategori berhasil ditambahkan', '/kategori');
    }

    public function edit(int $id): void
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            $this->error('Kategori tidak ditemukan', '/kategori');
        }
        view('kategori.form', compact('kategori'));
    }

    public function update(int $id): void
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            $this->error('Kategori tidak ditemukan', '/kategori');
        }
        $data = $this->validate([
            'nama_kategori' => 'required',
            'deskripsi' => 'required',
        ]);
        Kategori::update($id, $data);
        AuditLog::record((int) Auth::id(), 'update', 'kategori', 'Memperbarui kategori #' . $id);
        $this->success('Kategori diperbarui', '/kategori');
    }

    public function destroy(int $id): void
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            $this->error('Kategori tidak ditemukan', '/kategori');
        }
        $terpakai = SuratMasuk::count('kategori_id = :id', ['id' => $id]) + SuratKeluar::count('kategori_id = :id', ['id' => $id]);
        if ($terpakai > 0) {
            $this->error('Kategori tidak dapat dihapus karena masih digunakan', '/kategori');
        }
        Kategori::delete($id);
        AuditLog::record((int) Auth::id(), 'delete', 'kategori', 'Menghapus kategori #' . $id);
        $this->success('Kategori dihapus', '/kategori');
    }
}
