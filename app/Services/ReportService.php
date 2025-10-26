<?php

namespace App\Services;

use App\Core\Database;

class ReportService
{
    public function generate(string $type, array $filters, ?int $userId, ?string $role): array
    {
        $table = $type === 'keluar' ? 'surat_keluar' : 'surat_masuk';
        $dateColumn = $type === 'keluar' ? 'tanggal_keluar' : 'tanggal_masuk';
        $partyColumn = $type === 'keluar' ? 'penerima' : 'pengirim';
        $sql = "SELECT s.*, k.nama_kategori, u.name as petugas FROM {$table} s "
            . 'LEFT JOIN kategori_surat k ON k.id = s.kategori_id '
            . 'LEFT JOIN users u ON u.id = s.created_by WHERE 1=1';
        $params = [];

        if ($role === 'staff' && $userId) {
            $sql .= ' AND s.created_by = :uid';
            $params['uid'] = $userId;
        }

        $periode = $filters['periode'] ?? 'bulanan';
        if ($periode === 'harian' && !empty($filters['tanggal'])) {
            $sql .= " AND DATE(s.{$dateColumn}) = :tanggal";
            $params['tanggal'] = $filters['tanggal'];
        } elseif ($periode === 'bulanan' && !empty($filters['bulan']) && !empty($filters['tahun'])) {
            $sql .= " AND MONTH(s.{$dateColumn}) = :bulan AND YEAR(s.{$dateColumn}) = :tahun";
            $params['bulan'] = (int) $filters['bulan'];
            $params['tahun'] = (int) $filters['tahun'];
        } elseif ($periode === 'tahunan' && !empty($filters['tahun'])) {
            $sql .= " AND YEAR(s.{$dateColumn}) = :tahun";
            $params['tahun'] = (int) $filters['tahun'];
        } elseif ($periode === 'rentang' && !empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND DATE(s.{$dateColumn}) BETWEEN :start AND :end";
            $params['start'] = $filters['start_date'];
            $params['end'] = $filters['end_date'];
        }

        if (!empty($filters['kategori_id'])) {
            $sql .= ' AND s.kategori_id = :kategori';
            $params['kategori'] = (int) $filters['kategori_id'];
        }

        $sql .= " ORDER BY s.{$dateColumn} DESC";
        $rows = Database::query($sql, $params);

        return [
            'type' => $type,
            'date_column' => $dateColumn,
            'party_column' => $partyColumn,
            'rows' => $rows,
            'filters' => $filters,
        ];
    }
}
