<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        if (!$user) {
            redirect('/login');
        }

        if ($user['role'] === 'admin') {
            $totalMasuk = SuratMasuk::count();
            $totalKeluar = SuratKeluar::count();
            $grafikMasuk = SuratMasuk::countByMonth((int) date('Y'));
            $grafikKeluar = SuratKeluar::countByMonth((int) date('Y'));
            $terbaruMasuk = SuratMasuk::withRelations(null, null, 5);
            $terbaruKeluar = SuratKeluar::withRelations(null, null, 5);
            $audit = AuditLog::recent();
            view('dashboard.admin', compact('totalMasuk', 'totalKeluar', 'grafikMasuk', 'grafikKeluar', 'terbaruMasuk', 'terbaruKeluar', 'audit'));
            return;
        }

        $userId = (int) $user['id'];
        $totalMasuk = SuratMasuk::count('created_by = :uid', ['uid' => $userId]);
        $totalKeluar = SuratKeluar::count('created_by = :uid', ['uid' => $userId]);
        $suratMasuk = SuratMasuk::withRelations($userId, 'staff');
        $suratKeluar = SuratKeluar::withRelations($userId, 'staff');
        $gabungan = array_merge($suratMasuk, $suratKeluar);
        usort($gabungan, function ($a, $b) {
            $tanggalA = $a['jenis'] === 'keluar' ? $a['tanggal_keluar'] : $a['tanggal_masuk'];
            $tanggalB = $b['jenis'] === 'keluar' ? $b['tanggal_keluar'] : $b['tanggal_masuk'];
            return strcmp($tanggalB, $tanggalA);
        });
        $terbaru = array_slice($gabungan, 0, 5);
        view('dashboard.staff', compact('totalMasuk', 'totalKeluar', 'terbaru'));
    }
}
