<?php

namespace App\Models;

use App\Core\Database;

class SuratMasuk extends BaseModel
{
    protected static string $table = 'surat_masuk';
    protected static array $fillable = [
        'no_surat',
        'pengirim',
        'tanggal_masuk',
        'perihal',
        'kategori_id',
        'file',
        'created_by',
        'updated_by',
    ];

    public static function withRelations(?int $userId = null, ?string $role = null, int $limit = 0): array
    {
        $sql = 'SELECT sm.*, k.nama_kategori, u.name as pembuat, "masuk" as jenis FROM surat_masuk sm '
            . 'LEFT JOIN kategori_surat k ON k.id = sm.kategori_id '
            . 'LEFT JOIN users u ON u.id = sm.created_by';
        $params = [];
        if ($role === 'staff' && $userId) {
            $sql .= ' WHERE sm.created_by = :uid';
            $params['uid'] = $userId;
        }
        $sql .= ' ORDER BY sm.tanggal_masuk DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        return Database::query($sql, $params);
    }

    public static function countByMonth(int $year): array
    {
        $sql = 'SELECT MONTH(tanggal_masuk) as bulan, COUNT(*) as total FROM surat_masuk '
            . 'WHERE YEAR(tanggal_masuk) = :year GROUP BY MONTH(tanggal_masuk) ORDER BY bulan';
        $rows = Database::query($sql, ['year' => $year]);
        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['bulan']] = (int) $row['total'];
        }
        return $result;
    }
}
