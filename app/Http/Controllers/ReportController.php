<?php

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ExcelExporter;
use App\Services\PdfExporter;
use App\Services\ReportService;

class ReportController
{
    protected ReportService $reports;

    public function __construct()
    {
        $this->reports = new ReportService();
    }

    public function index(Request $request): Response
    {
        $type = $request->input('type', 'incoming');
        $period = $request->input('period', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $rows = $this->reports->getData($type, $period, $date);
        return Response::make(view('reports.index', compact('type', 'period', 'date', 'rows')));
    }

    public function exportPdf(Request $request): Response
    {
        $type = $request->input('type', 'incoming');
        $period = $request->input('period', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $rows = $this->reports->getData($type, $period, $date);
        $columns = $this->columns($type);
        $pdf = (new PdfExporter())->generate(strtoupper($type) . ' Letters Report', $columns, $rows);
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="report-' . $type . '.pdf"',
        ]);
    }

    public function exportExcel(Request $request): Response
    {
        $type = $request->input('type', 'incoming');
        $period = $request->input('period', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $rows = $this->reports->getData($type, $period, $date);
        $columns = $this->columns($type);
        $csv = (new ExcelExporter())->generate(strtoupper($type) . ' Letters Report', $columns, $rows);
        return new Response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-' . $type . '.csv"',
        ]);
    }

    protected function columns(string $type): array
    {
        if ($type === 'outgoing') {
            return [
                ['label' => 'Letter Number', 'key' => 'no_surat'],
                ['label' => 'Receiver', 'key' => 'receiver'],
                ['label' => 'Date Sent', 'key' => 'tanggal_keluar'],
                ['label' => 'Subject', 'key' => 'subject'],
                ['label' => 'Category', 'key' => 'nama_kategori'],
                ['label' => 'Created By', 'key' => 'creator'],
            ];
        }
        return [
            ['label' => 'Letter Number', 'key' => 'no_surat'],
            ['label' => 'Sender', 'key' => 'sender'],
            ['label' => 'Date Received', 'key' => 'tanggal_masuk'],
            ['label' => 'Subject', 'key' => 'subject'],
            ['label' => 'Category', 'key' => 'nama_kategori'],
            ['label' => 'Created By', 'key' => 'creator'],
        ];
    }
}
