<?php

namespace App\Models;

use App\Core\Database;

class SuratKeluar extends BaseModel
{
    protected static string $table = 'surat_keluar';
    protected static array $fillable = [
        'no_surat',
        'penerima',
        'tanggal_keluar',
        'perihal',
        'kategori_id',
        'file',
        'created_by',
        'updated_by',
    ];

    public static function withRelations(?int $userId = null, ?string $role = null, int $limit = 0): array
    {
        $sql = 'SELECT sk.*, k.nama_kategori, u.name as pembuat, "keluar" as jenis FROM surat_keluar sk '
            . 'LEFT JOIN kategori_surat k ON k.id = sk.kategori_id '
            . 'LEFT JOIN users u ON u.id = sk.created_by';
        $params = [];
        if ($role === 'staff' && $userId) {
            $sql .= ' WHERE sk.created_by = :uid';
            $params['uid'] = $userId;
        }
        $sql .= ' ORDER BY sk.tanggal_keluar DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        return Database::query($sql, $params);
    }

    public static function countByMonth(int $year): array
    {
        $sql = 'SELECT MONTH(tanggal_keluar) as bulan, COUNT(*) as total FROM surat_keluar '
            . 'WHERE YEAR(tanggal_keluar) = :year GROUP BY MONTH(tanggal_keluar) ORDER BY bulan';
        $rows = Database::query($sql, ['year' => $year]);
        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['bulan']] = (int) $row['total'];
        }
        return $result;
    }
}
