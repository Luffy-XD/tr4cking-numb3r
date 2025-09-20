<?php

namespace App\Models;

use App\Core\Database;

class Kategori extends BaseModel
{
    protected static string $table = 'kategori_surat';
    protected static array $fillable = ['nama_kategori', 'deskripsi'];

    public static function withCount(): array
    {
        $sql = 'SELECT k.*, '
            . '(SELECT COUNT(*) FROM surat_masuk WHERE kategori_id = k.id) as jumlah_masuk, '
            . '(SELECT COUNT(*) FROM surat_keluar WHERE kategori_id = k.id) as jumlah_keluar '
            . 'FROM kategori_surat k ORDER BY k.nama_kategori ASC';
        return Database::query($sql);
    }
}
