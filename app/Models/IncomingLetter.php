<?php

namespace App\Models;

class IncomingLetter extends Model
{
    protected static string $table = 'surat_masuk';
    protected static array $fillable = [
        'no_surat',
        'sender',
        'tanggal_masuk',
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
        $sql = 'SELECT sm.*, k.nama_kategori, u.name as creator FROM surat_masuk sm
                JOIN kategori_surat k ON k.id = sm.kategori_id
                JOIN users u ON u.id = sm.created_by';
        $params = [];
        if ($userId) {
            $sql .= ' WHERE sm.created_by = ?';
            $params[] = $userId;
        }
        $sql .= ' ORDER BY sm.tanggal_masuk DESC, sm.created_at DESC LIMIT ' . (int) $limit;
        return static::query($sql, $params);
    }

    public static function paginate(?int $userId = null, ?string $search = null): array
    {
        $sql = 'SELECT sm.*, k.nama_kategori, u.name as creator FROM surat_masuk sm
                JOIN kategori_surat k ON k.id = sm.kategori_id
                JOIN users u ON u.id = sm.created_by';
        $params = [];
        $conditions = [];
        if ($userId) {
            $conditions[] = 'sm.created_by = ?';
            $params[] = $userId;
        }
        if ($search) {
            $conditions[] = '(sm.no_surat LIKE ? OR sm.subject LIKE ? OR sm.sender LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY sm.tanggal_masuk DESC';
        return static::query($sql, $params);
    }
}
