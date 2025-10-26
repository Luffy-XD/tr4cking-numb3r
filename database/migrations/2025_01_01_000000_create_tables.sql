CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE kategori_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_surat VARCHAR(150) NOT NULL,
    pengirim VARCHAR(150) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    perihal TEXT NOT NULL,
    kategori_id INT NULL,
    file VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    updated_by INT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_surat_masuk_kategori FOREIGN KEY (kategori_id) REFERENCES kategori_surat(id) ON DELETE SET NULL,
    CONSTRAINT fk_surat_masuk_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_surat VARCHAR(150) NOT NULL,
    penerima VARCHAR(150) NOT NULL,
    tanggal_keluar DATE NOT NULL,
    perihal TEXT NOT NULL,
    kategori_id INT NULL,
    file VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    updated_by INT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_surat_keluar_kategori FOREIGN KEY (kategori_id) REFERENCES kategori_surat(id) ON DELETE SET NULL,
    CONSTRAINT fk_surat_keluar_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(150) NOT NULL UNIQUE,
    `value` TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, role, status, created_at, updated_at)
VALUES ('Administrator', 'admin@bpsdm.aceh.go.id', '$2y$12$pIGn9kBPjj/yic3QdLOQy.6svuWB9XmU8UYfpp8UIa/vNZgUqWfJS', 'admin', 'active', NOW(), NOW());
