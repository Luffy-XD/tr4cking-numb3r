<?php $pageTitle = 'Detail Surat Keluar'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-4">Detail Surat Keluar</h4>
        <dl class="row">
            <dt class="col-sm-3">Nomor Surat</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($surat['no_surat']) ?></dd>
            <dt class="col-sm-3">Penerima</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($surat['penerima']) ?></dd>
            <dt class="col-sm-3">Tanggal Keluar</dt>
            <dd class="col-sm-9"><?= format_date($surat['tanggal_keluar']) ?></dd>
            <dt class="col-sm-3">Kategori</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($kategori['nama_kategori'] ?? '-') ?></dd>
            <dt class="col-sm-3">Perihal</dt>
            <dd class="col-sm-9"><?= nl2br(htmlspecialchars($surat['perihal'])) ?></dd>
        </dl>
        <div class="ratio ratio-4x3 border rounded">
            <iframe src="<?= asset('uploads/surat_keluar/' . $surat['file']) ?>" title="Preview Surat" allowfullscreen></iframe>
        </div>
        <div class="mt-3">
            <a href="/surat-keluar" class="btn btn-outline-secondary">Kembali</a>
            <a href="/surat-keluar/<?= $surat['id'] ?>/download" class="btn btn-primary"><i class="bi bi-download"></i> Download</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
