<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Kategori;
use App\Services\ReportService;
use App\Services\ExportService;

class LaporanController extends Controller
{
    private ReportService $service;
    private ExportService $export;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->service = new ReportService();
        $this->export = new ExportService();
    }

    public function index(): void
    {
        $jenis = $this->request->query('jenis', 'masuk');
        $filters = [
            'periode' => $this->request->query('periode', 'bulanan'),
            'tanggal' => $this->request->query('tanggal'),
            'bulan' => $this->request->query('bulan'),
            'tahun' => $this->request->query('tahun', date('Y')),
            'start_date' => $this->request->query('start_date'),
            'end_date' => $this->request->query('end_date'),
            'kategori_id' => $this->request->query('kategori_id'),
        ];

        $report = $this->service->generate($jenis, $filters, Auth::id(), Auth::role());
        $rows = $report['rows'];
        $export = $this->request->query('export');

        if ($export === 'pdf') {
            $this->exportPdf($jenis, $report);
            return;
        }
        if ($export === 'excel') {
            $this->exportExcel($jenis, $report);
            return;
        }

        $kategori = Kategori::all();
        view('laporan.index', compact('rows', 'filters', 'kategori', 'jenis'));
    }

    private function exportPdf(string $jenis, array $report): void
    {
        $headers = ['No Surat', $jenis === 'masuk' ? 'Pengirim' : 'Penerima', 'Tanggal', 'Perihal', 'Kategori', 'Petugas'];
        $dateColumn = $report['date_column'];
        $partyColumn = $report['party_column'];
        $rows = [];
        foreach ($report['rows'] as $item) {
            $rows[] = [
                $item['no_surat'],
                $item[$partyColumn],
                $item[$dateColumn],
                $item['perihal'],
                $item['nama_kategori'],
                $item['pembuat'] ?? '-',
            ];
        }
        $title = 'Laporan Surat ' . ucfirst($jenis);
        $this->export->toPdf('laporan_' . $jenis . '_' . date('Ymd_His'), $title, $headers, $rows);
    }

    private function exportExcel(string $jenis, array $report): void
    {
        $headers = ['No Surat', $jenis === 'masuk' ? 'Pengirim' : 'Penerima', 'Tanggal', 'Perihal', 'Kategori', 'Petugas'];
        $dateColumn = $report['date_column'];
        $partyColumn = $report['party_column'];
        $rows = [];
        foreach ($report['rows'] as $item) {
            $rows[] = [
                $item['no_surat'],
                $item[$partyColumn],
                $item[$dateColumn],
                $item['perihal'],
                $item['nama_kategori'],
                $item['pembuat'] ?? '-',
            ];
        }
        $this->export->toExcel('laporan_' . $jenis . '_' . date('Ymd_His'), $headers, $rows);
    }
}
