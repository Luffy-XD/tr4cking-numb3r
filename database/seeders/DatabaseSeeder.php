<?php

namespace Database\Seeders;

use App\Core\Database;

class DatabaseSeeder
{
    public function run(): void
    {
        $pdo = Database::connection();
        $existing = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($existing == 0) {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([
                'Administrator',
                'admin@example.com',
                password_hash('password123', PASSWORD_BCRYPT),
                'admin',
                'active',
            ]);
        }
    }
}
