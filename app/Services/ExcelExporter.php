<?php

namespace App\Services;

class ExcelExporter
{
    public function generate(string $title, array $columns, array $rows): string
    {
        $lines = [];
        $lines[] = $title;
        $lines[] = implode(',', array_map([$this, 'escape'], array_column($columns, 'label')));
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $column) {
                $value = $row[$column['key']] ?? '';
                $line[] = $this->escape(is_scalar($value) ? (string) $value : json_encode($value));
            }
            $lines[] = implode(',', $line);
        }
        return implode("\r\n", $lines);
    }

    protected function escape(string $value): string
    {
        $needsQuotes = str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n");
        $value = str_replace('"', '""', $value);
        return $needsQuotes ? '"' . $value . '"' : $value;
    }
}
