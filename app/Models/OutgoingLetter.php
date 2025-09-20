<?php

namespace App\Models;

class OutgoingLetter extends Model
{
    protected static string $table = 'surat_keluar';
    protected static array $fillable = [
        'no_surat',
        'receiver',
        'tanggal_keluar',
        'subject',
        'kategori_id',
        'file',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public static function latestWithRelations(int $limit = 5, ?int $userId = null): array
    {
        $sql = 'SELECT sk.*, k.nama_kategori, u.name as creator FROM surat_keluar sk
                JOIN kategori_surat k ON k.id = sk.kategori_id
                JOIN users u ON u.id = sk.created_by';
        $params = [];
        if ($userId) {
            $sql .= ' WHERE sk.created_by = ?';
            $params[] = $userId;
        }
        $sql .= ' ORDER BY sk.tanggal_keluar DESC, sk.created_at DESC LIMIT ' . (int) $limit;
        return static::query($sql, $params);
    }

    public static function paginate(?int $userId = null, ?string $search = null): array
    {
        $sql = 'SELECT sk.*, k.nama_kategori, u.name as creator FROM surat_keluar sk
                JOIN kategori_surat k ON k.id = sk.kategori_id
                JOIN users u ON u.id = sk.created_by';
        $params = [];
        $conditions = [];
        if ($userId) {
            $conditions[] = 'sk.created_by = ?';
            $params[] = $userId;
        }
        if ($search) {
            $conditions[] = '(sk.no_surat LIKE ? OR sk.subject LIKE ? OR sk.receiver LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY sk.tanggal_keluar DESC';
        return static::query($sql, $params);
    }
}
