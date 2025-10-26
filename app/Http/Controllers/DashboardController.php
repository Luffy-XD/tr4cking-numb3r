<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\LetterCategory;

class DashboardController
{
    public function index(): Response
    {
        $user = Auth::user();
        if (!$user) {
            return Response::make('', 302, ['Location' => '/login']);
        }

        if ($user['role'] === 'admin') {
            $incomingCount = IncomingLetter::count();
            $outgoingCount = OutgoingLetter::count();
            $recentIncoming = IncomingLetter::latestWithRelations(5);
            $recentOutgoing = OutgoingLetter::latestWithRelations(5);
            $categories = LetterCategory::withCount();
            return Response::make(view('dashboard.admin', compact(
                'user',
                'incomingCount',
                'outgoingCount',
                'recentIncoming',
                'recentOutgoing',
                'categories'
            )));
        }

        $incomingCount = IncomingLetter::count(['created_by = ?' => [$user['id']]]);
        $outgoingCount = OutgoingLetter::count(['created_by = ?' => [$user['id']]]);
        $recentIncoming = IncomingLetter::latestWithRelations(5, $user['id']);
        $recentOutgoing = OutgoingLetter::latestWithRelations(5, $user['id']);

        return Response::make(view('dashboard.staff', compact(
            'user',
            'incomingCount',
            'outgoingCount',
            'recentIncoming',
            'recentOutgoing'
        )));
    }

    public function chartData(Request $request): Response
    {
        $user = Auth::user();
        if (!$user) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }

        $year = $request->input('year', date('Y'));
        $incomingData = $this->buildMonthlyData('surat_masuk', 'tanggal_masuk', $user);
        $outgoingData = $this->buildMonthlyData('surat_keluar', 'tanggal_keluar', $user);

        return Response::json([
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'incoming' => $incomingData,
            'outgoing' => $outgoingData,
        ]);
    }

    protected function buildMonthlyData(string $table, string $dateColumn, array $user): array
    {
        $pdo = \App\Core\Database::connection();
        $sql = "SELECT MONTH($dateColumn) as month, COUNT(*) as total FROM {$table}";
        $params = [];
        $conditions = [];
        if ($user['role'] === 'staff') {
            $conditions[] = 'created_by = ?';
            $params[] = $user['id'];
        }
        $conditions[] = "$dateColumn BETWEEN ? AND ?";
        $params[] = date('Y-01-01');
        $params[] = date('Y-12-31');
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' GROUP BY MONTH($dateColumn)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        $data = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $data[(int) $row['month']] = (int) $row['total'];
        }
        return array_values($data);
    }
}
