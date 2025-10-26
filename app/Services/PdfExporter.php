<?php

namespace App\Services;

class PdfExporter
{
    public function generate(string $title, array $columns, array $rows): string
    {
        $streamLines = [];
        $streamLines[] = 'BT';
        $streamLines[] = '/F1 18 Tf';
        $streamLines[] = '72 820 Td';
        $streamLines[] = '(' . $this->escape($title) . ') Tj';
        $streamLines[] = '/F1 12 Tf';
        $streamLines[] = 'T*';
        $streamLines[] = '(' . $this->escape(implode(' | ', array_column($columns, 'label'))) . ') Tj';
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $column) {
                $value = $row[$column['key']] ?? '';
                $line[] = is_scalar($value) ? (string) $value : json_encode($value);
            }
            $streamLines[] = 'T*';
            $streamLines[] = '(' . $this->escape(implode(' | ', $line)) . ') Tj';
        }
        $streamLines[] = 'ET';
        $streamContent = implode("\n", $streamLines);

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj';
        $objects[] = '4 0 obj << /Length ' . strlen($streamContent) . ' >> stream\n' . $streamContent . '\nendstream\nendobj';
        $objects[] = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }
        $xrefOffset = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= 'xref\n';
        $pdf .= '0 ' . $count . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf('%010d 00000 n \n', $offsets[$i]);
        }
        $pdf .= 'trailer << /Size ' . $count . ' /Root 1 0 R >>\n';
        $pdf .= 'startxref\n' . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    protected function escape(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}
