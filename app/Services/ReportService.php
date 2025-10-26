<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;

class ReportService
{
    public function getData(string $type, string $period, ?string $date = null): array
    {
        $user = Auth::user();
        $conditions = [];
        $params = [];
        $date = $date ?? date('Y-m-d');

        if ($type === 'outgoing') {
            $table = 'surat_keluar sk';
            $dateColumn = 'sk.tanggal_keluar';
            $baseSql = 'SELECT sk.*, k.nama_kategori, u.name as creator FROM surat_keluar sk
                        JOIN kategori_surat k ON k.id = sk.kategori_id
                        JOIN users u ON u.id = sk.created_by';
        } else {
            $table = 'surat_masuk sm';
            $dateColumn = 'sm.tanggal_masuk';
            $baseSql = 'SELECT sm.*, k.nama_kategori, u.name as creator FROM surat_masuk sm
                        JOIN kategori_surat k ON k.id = sm.kategori_id
                        JOIN users u ON u.id = sm.created_by';
        }

        if ($user && $user['role'] === 'staff') {
            $conditions[] = 'u.id = ?';
            $params[] = $user['id'];
        }

        if ($period === 'daily') {
            $conditions[] = "$dateColumn = ?";
            $params[] = $date;
        } elseif ($period === 'monthly') {
            $conditions[] = "MONTH($dateColumn) = ? AND YEAR($dateColumn) = ?";
            $params[] = date('m', strtotime($date));
            $params[] = date('Y', strtotime($date));
        } elseif ($period === 'yearly') {
            $conditions[] = "YEAR($dateColumn) = ?";
            $params[] = date('Y', strtotime($date));
        }

        $sql = $baseSql;
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ' . $dateColumn . ' DESC';

        return $type === 'outgoing'
            ? OutgoingLetter::query($sql, $params)
            : IncomingLetter::query($sql, $params);
    }
}
