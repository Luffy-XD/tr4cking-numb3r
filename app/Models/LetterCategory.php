<?php

namespace App\Models;

class LetterCategory extends Model
{
    protected static string $table = 'kategori_surat';
    protected static array $fillable = [
        'nama_kategori',
        'description',
        'created_at',
        'updated_at',
    ];

    public static function withCount(): array
    {
        $sql = 'SELECT k.*, (
                    SELECT COUNT(*) FROM surat_masuk WHERE kategori_id = k.id
                ) as incoming_count,
                (
                    SELECT COUNT(*) FROM surat_keluar WHERE kategori_id = k.id
                ) as outgoing_count
                FROM kategori_surat k ORDER BY k.nama_kategori ASC';
        return static::query($sql);
    }
}
