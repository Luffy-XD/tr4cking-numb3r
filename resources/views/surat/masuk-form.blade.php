<?php $pageTitle = $surat ? 'Edit Surat Masuk' : 'Tambah Surat Masuk'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<?php $action = $surat ? '/surat-masuk/' . $surat['id'] . '/update' : '/surat-masuk'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-4"><?= $surat ? 'Edit Surat Masuk' : 'Tambah Surat Masuk' ?></h4>
        <form method="POST" action="<?= $action ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control<?= !empty($errors['no_surat']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($surat['no_surat'] ?? old('no_surat')) ?>">
                    <?php if (!empty($errors['no_surat'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['no_surat'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pengirim</label>
                    <input type="text" name="pengirim" class="form-control<?= !empty($errors['pengirim']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($surat['pengirim'] ?? old('pengirim')) ?>">
                    <?php if (!empty($errors['pengirim'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['pengirim'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control<?= !empty($errors['tanggal_masuk']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($surat['tanggal_masuk'] ?? old('tanggal_masuk')) ?>">
                    <?php if (!empty($errors['tanggal_masuk'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['tanggal_masuk'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select<?= !empty($errors['kategori_id']) ? ' is-invalid' : '' ?>">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= (isset($surat['kategori_id']) && $surat['kategori_id'] == $kat['id']) || old('kategori_id') == $kat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['kategori_id'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['kategori_id'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">File Surat (PDF)</label>
                    <input type="file" name="file" class="form-control" accept="application/pdf">
                    <?php if (!empty($errors['file'])): ?><div class="text-danger small mt-1"><?= htmlspecialchars($errors['file'][0]) ?></div><?php endif; ?>
                    <?php if ($surat && !empty($surat['file'])): ?>
                        <small class="text-muted">File saat ini: <a href="/surat-masuk/<?= $surat['id'] ?>/download">Download</a></small>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label">Perihal</label>
                    <textarea name="perihal" rows="3" class="form-control<?= !empty($errors['perihal']) ? ' is-invalid' : '' ?>"><?= htmlspecialchars($surat['perihal'] ?? old('perihal')) ?></textarea>
                    <?php if (!empty($errors['perihal'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['perihal'][0]) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/surat-masuk" class="btn btn-outline-secondary">Kembali</a>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
