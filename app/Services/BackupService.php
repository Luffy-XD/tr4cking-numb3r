<?php

namespace App\Services;

use App\Core\Database;

class BackupService
{
    private array $tables = ['users', 'kategori_surat', 'surat_masuk', 'surat_keluar', 'settings', 'audit_logs'];

    public function createSql(): string
    {
        $sql = "-- Backup Database Arsip --\n";
        $sql .= 'SET FOREIGN_KEY_CHECKS=0;' . "\n";
        foreach ($this->tables as $table) {
            $create = Database::queryOne('SHOW CREATE TABLE `' . $table . '`');
            if (!$create) {
                continue;
            }
            $definition = $create['Create Table'] ?? '';
            if ($definition === '') {
                $values = array_values($create);
                $definition = $values[1] ?? '';
            }
            if ($definition === '') {
                continue;
            }
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $definition . ";\n\n";
            $rows = Database::query('SELECT * FROM `' . $table . '`');
            foreach ($rows as $row) {
                $columns = array_keys($row);
                $values = array_map(fn($value) => $this->escapeValue($value), array_values($row));
                $sql .= 'INSERT INTO `' . $table . '` (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', $values) . ');' . "\n";
            }
            $sql .= "\n";
        }
        $sql .= 'SET FOREIGN_KEY_CHECKS=1;' . "\n";
        return $sql;
    }

    public function save(string $path): string
    {
        $sql = $this->createSql();
        file_put_contents($path, $sql);
        return $path;
    }

    public function restore(string $sql): void
    {
        $statements = $this->splitStatements($sql);
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed === '') {
                continue;
            }
            Database::execute($trimmed);
        }
    }

    private function escapeValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        $escaped = addslashes((string) $value);
        return "'{$escaped}'";
    }

    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inString = false;
        $len = strlen($sql);
        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            if ($char === "'" && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inString = !$inString;
            }
            if ($char === ';' && !$inString) {
                $statements[] = $buffer;
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }
        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }
        return $statements;
    }
}
