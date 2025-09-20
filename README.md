# Sistem Informasi Arsip Surat BPSDM Aceh

Aplikasi web berbasis PHP yang menerapkan pola arsitektur sederhana menyerupai Laravel untuk mengelola surat masuk dan surat keluar dengan pengendalian akses berbasis peran (Admin dan Staff).

## Fitur Utama

- Autentikasi dengan email/username dan password.
- Dashboard berbeda untuk Admin dan Staff (statistik, grafik, surat terbaru).
- Manajemen surat masuk dan surat keluar lengkap dengan unggah berkas PDF (maks. 10 MB).
- Manajemen kategori surat (Admin saja).
- Modul laporan dengan filter periode dan ekspor PDF/Excel.
- Manajemen user (Admin) beserta reset password.
- Pengaturan sistem: identitas instansi, logo, serta backup & restore database.
- Audit log untuk pencatatan aktivitas penting.

## Struktur Proyek

```
app/
  Controllers/    # Controller modular untuk setiap fitur
  Core/           # Komponen inti (Router, Auth, Database, dsb.)
  Models/         # Model aktif untuk entitas utama
  Services/       # Layanan utilitas (laporan, ekspor, backup)
bootstrap/
config/
database/
  migrations/     # Skrip SQL pembuatan tabel dan seeding awal
public/
  index.php       # Entry point aplikasi (php -S)
resources/views/  # Tampilan berbasis Blade-like template
storage/          # Direktori backup
```

## Persiapan Lingkungan

1. **Salin file `.env`**

   ```bash
   cp .env.example .env
   ```

   Sesuaikan kredensial database MySQL Anda di file `.env`.

2. **Import basis data**

   Jalankan skrip migrasi SQL yang tersedia:

   ```bash
   mysql -u root -p arsip_bpsdm < database/migrations/2025_01_01_000000_create_tables.sql
   ```

   Skrip tersebut akan membuat tabel dan menambahkan akun admin default.

3. **Menjalankan server pengembangan**

   Gunakan PHP built-in server dan arahkan dokumen root ke folder `public`:

   ```bash
   php -S localhost:8000 -t public
   ```

   Aplikasi dapat diakses melalui `http://localhost:8000`.

## Akun Default

| Role  | Email                         | Password     |
|-------|------------------------------|--------------|
| Admin | admin@bpsdm.aceh.go.id       | password123  |

Admin dapat membuat akun staff baru melalui menu **Manajemen User**.

## Catatan Teknis

- Seluruh unggahan surat disimpan pada `public/uploads/` dan diabaikan oleh Git.
- Validasi berkas memastikan format PDF dan ukuran tidak melebihi 10 MB.
- Ekspor PDF dibuat secara programatis tanpa dependensi eksternal.
- Ekspor Excel menggunakan format HTML yang kompatibel dengan spreadsheet.
- Backup database menghasilkan file SQL siap diimpor kembali melalui modul pengaturan.

## Lisensi

Proyek ini dibuat untuk kebutuhan simulasi pengembangan aplikasi internal BPSDM Aceh.
