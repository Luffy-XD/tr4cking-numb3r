<?php

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class BackupService
{
    protected array $tables = [
        'users',
        'kategori_surat',
        'surat_masuk',
        'surat_keluar',
        'system_settings',
        'audit_trails',
    ];

    public function createBackup(): string
    {
        $pdo = Database::connection();
        $lines = [];
        $lines[] = '-- Backup generated at ' . date('Y-m-d H:i:s');
        foreach ($this->tables as $table) {
            $lines[] = "-- Table {$table}";
            $lines[] = "DELETE FROM `{$table}`;";
            $stmt = $pdo->query('SELECT * FROM ' . $table);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $columns = array_map(fn($col) => "`{$col}`", array_keys($row));
                $values = array_map(fn($value) => $value === null ? 'NULL' : $pdo->quote($value), array_values($row));
                $lines[] = 'INSERT INTO `' . $table . '` (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ');';
            }
        }
        $content = implode("\n", $lines);
        $fileName = 'backup_' . date('Ymd_His') . '.sql';
        $path = storage_path('backups/' . $fileName);
        file_put_contents($path, $content);
        return $path;
    }

    public function restore(string $sql): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
                if ($statement === '' || str_starts_with($statement, '--')) {
                    continue;
                }
                $pdo->exec($statement);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException('Restore failed: ' . $e->getMessage());
        }
    }
}
