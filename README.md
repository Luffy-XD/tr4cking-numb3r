# Sistem Informasi Arsip Surat BPSDM Aceh

Aplikasi web statis untuk mendukung pengarsipan surat masuk dan keluar di lingkungan BPSDM Aceh. Sistem ini menyediakan panel admin dan staf lengkap dengan pengelolaan surat, kategori, laporan, pengaturan pengguna, hingga fitur cadangan data lokal.

## Fitur Utama

- **Halaman Login** dengan informasi kredensial contoh.
- **Dashboard Admin** menampilkan ringkasan statistik, grafik surat bulanan, dan daftar surat terbaru.
- **Dashboard Staf** berisi statistik aktivitas pribadi serta 5 surat terakhir yang dibuat.
- **Manajemen Surat Masuk & Keluar** lengkap dengan formulir unggah PDF dan pratinjau berkas.
- **Pengelolaan Kategori Surat** (admin) dengan perhitungan total surat per kategori.
- **Laporan Surat** dengan filter jenis & periode (harian, bulanan, tahunan) serta ekspor PDF/Excel.
- **Manajemen Pengguna** (admin) untuk menambah staf, mengatur status, dan menghapus akun.
- **Pengaturan Sistem** untuk identitas instansi, pengaturan unggah, serta cadangkan/pulihkan data.

## Cara Menjalankan

1. Buka berkas `index.html` langsung melalui peramban modern (disarankan Chrome/Edge/Firefox terbaru).
2. Gunakan kredensial contoh berikut untuk masuk:
   - **Admin**: `admin` / `admin123`
   - **Staf**: `staff` / `staff123`
3. Data akan tersimpan secara lokal di `localStorage` peramban. Gunakan fitur cadangan untuk mengekspor/mengimpor data.

## Dependensi Front-end

Aplikasi memanfaatkan pustaka CDN berikut:

- [Chart.js](https://www.chartjs.org/) untuk grafik surat bulanan.
- [jsPDF](https://github.com/parallax/jsPDF) dan [jsPDF-AutoTable](https://github.com/simonbengtsson/jsPDF-AutoTable) untuk ekspor laporan ke PDF.
- [SheetJS](https://sheetjs.com/) untuk ekspor laporan ke Excel.
- [Font Awesome](https://fontawesome.com/) untuk ikon navigasi dan aksi.

## Struktur Proyek

```
.
├── assets
│   ├── css
│   │   └── styles.css
│   ├── img
│   │   └── logo.svg
│   └── js
│       └── app.js
└── index.html
```

Seluruh logika aplikasi berada di `assets/js/app.js` sehingga mudah dikustomisasi untuk integrasi backend di masa mendatang.
