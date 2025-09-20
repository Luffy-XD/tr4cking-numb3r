<?php $pageTitle = $kategori ? 'Edit Kategori' : 'Tambah Kategori'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<?php $action = $kategori ? '/kategori/' . $kategori['id'] . '/update' : '/kategori'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-4"><?= $kategori ? 'Edit Kategori Surat' : 'Tambah Kategori Surat' ?></h4>
        <form method="POST" action="<?= $action ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control<?= !empty($errors['nama_kategori']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($kategori['nama_kategori'] ?? old('nama_kategori')) ?>">
                <?php if (!empty($errors['nama_kategori'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['nama_kategori'][0]) ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="form-control<?= !empty($errors['deskripsi']) ? ' is-invalid' : '' ?>"><?= htmlspecialchars($kategori['deskripsi'] ?? old('deskripsi')) ?></textarea>
                <?php if (!empty($errors['deskripsi'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['deskripsi'][0]) ?></div><?php endif; ?>
            </div>
            <div class="d-flex justify-content-between">
                <a href="/kategori" class="btn btn-outline-secondary">Kembali</a>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
