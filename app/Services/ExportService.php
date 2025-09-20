<?php

namespace App\Services;

class ExportService
{
    public function toPdf(string $filename, string $title, array $headers, array $rows): void
    {
        $pdf = $this->buildPdf($title, $headers, $rows);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function toExcel(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        echo "<table border='1'>";
        echo '<tr>';
        foreach ($headers as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars((string) $value) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    private function buildPdf(string $title, array $headers, array $rows): string
    {
        $contentLines = [];
        $contentLines[] = 'BT';
        $contentLines[] = '/F1 16 Tf';
        $contentLines[] = '72 770 Td';
        $contentLines[] = '(' . $this->escapeText($title) . ') Tj';
        $contentLines[] = '0 -24 Td';
        $contentLines[] = '/F1 10 Tf';
        $contentLines[] = '(' . $this->escapeText(implode(' | ', $headers)) . ') Tj';
        foreach ($rows as $row) {
            $contentLines[] = '0 -14 Td';
            $contentLines[] = '(' . $this->escapeText(implode(' | ', array_map('strval', $row))) . ') Tj';
        }
        $contentLines[] = 'ET';
        $content = implode("\n", $contentLines);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        $objects = [];

        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj';
        $objects[] = '4 0 obj << /Length ' . strlen($content) . ' >> stream\n' . $content . '\nendstream endobj';
        $objects[] = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= 'xref\n';
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= '0000000000 65535 f \n';
        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>\n';
        $pdf .= 'startxref\n' . $xrefPosition . "\n";
        $pdf .= '%%EOF';

        return $pdf;
    }

    private function escapeText(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $text = preg_replace('/[\r\n]+/', ' ', $text);
        return $text;
    }
}
