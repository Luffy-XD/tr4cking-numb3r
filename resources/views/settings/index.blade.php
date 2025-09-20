<?php $pageTitle = 'Pengaturan Sistem'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Identitas Instansi</h4>
                <form method="POST" action="/settings/identity" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Instansi</label>
                        <input type="text" name="instansi_nama" class="form-control<?= !empty($errors['instansi_nama']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($settings['instansi_nama'] ?? '') ?>">
                        <?php if (!empty($errors['instansi_nama'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['instansi_nama'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Instansi</label>
                        <textarea name="instansi_alamat" rows="3" class="form-control<?= !empty($errors['instansi_alamat']) ? ' is-invalid' : '' ?>"><?= htmlspecialchars($settings['instansi_alamat'] ?? '') ?></textarea>
                        <?php if (!empty($errors['instansi_alamat'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['instansi_alamat'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Instansi (PNG/JPG/SVG)</label>
                        <input type="file" name="instansi_logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml">
                        <?php if (!empty($settings['instansi_logo'])): ?>
                            <div class="mt-2">
                                <img src="<?= asset('uploads/logo/' . $settings['instansi_logo']) ?>" alt="Logo" class="img-thumbnail" style="max-height:120px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-3">Pengaturan Upload</h4>
                <p class="mb-0">Format file: <strong>PDF</strong></p>
                <p class="mb-0">Ukuran maksimum: <strong>10 MB</strong></p>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Backup &amp; Restore Database</h4>
                <p class="text-muted">Gunakan fitur ini untuk melakukan backup data ke file SQL dan restore kembali bila diperlukan.</p>
                <div class="d-flex gap-2">
                    <a href="/settings/backup" class="btn btn-outline-primary"><i class="bi bi-download"></i> Backup Database</a>
                    <form method="POST" action="/settings/restore" enctype="multipart/form-data" class="d-flex align-items-center gap-2" onsubmit="return confirm('Lanjutkan restore database? Data saat ini akan diganti.');">
                        <?= csrf_field() ?>
                        <input type="file" name="backup_file" class="form-control form-control-sm" accept=".sql" required>
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-upload"></i> Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
