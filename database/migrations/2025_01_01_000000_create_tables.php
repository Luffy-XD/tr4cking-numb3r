<?php

namespace Database\Migrations;

use App\Core\Database;

class CreateTables
{
    public function up(): void
    {
        $pdo = Database::connection();
        $queries = [
            'CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM("admin","staff") NOT NULL DEFAULT "staff",
                status VARCHAR(20) DEFAULT "active",
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS kategori_surat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama_kategori VARCHAR(150) NOT NULL,
                description TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS surat_masuk (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_surat VARCHAR(150) NOT NULL,
                sender VARCHAR(150) NOT NULL,
                tanggal_masuk DATE NOT NULL,
                subject VARCHAR(255) NOT NULL,
                kategori_id INT NOT NULL,
                file VARCHAR(255) NOT NULL,
                created_by INT NOT NULL,
                updated_by INT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (kategori_id) REFERENCES kategori_surat(id) ON DELETE CASCADE,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS surat_keluar (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_surat VARCHAR(150) NOT NULL,
                receiver VARCHAR(150) NOT NULL,
                tanggal_keluar DATE NOT NULL,
                subject VARCHAR(255) NOT NULL,
                kategori_id INT NOT NULL,
                file VARCHAR(255) NOT NULL,
                created_by INT NOT NULL,
                updated_by INT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (kategori_id) REFERENCES kategori_surat(id) ON DELETE CASCADE,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS system_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                institution_name VARCHAR(255) NOT NULL,
                institution_address TEXT NOT NULL,
                logo_path VARCHAR(255) NULL,
                max_upload_size INT DEFAULT 10240,
                file_mime VARCHAR(50) DEFAULT "application/pdf",
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            'CREATE TABLE IF NOT EXISTS audit_trails (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                created_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        ];

        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }
    }
}
