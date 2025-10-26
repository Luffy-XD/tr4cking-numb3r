<?php $pageTitle = 'Kategori Surat'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Kategori Surat</h4>
    <a href="/kategori/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Kategori</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Surat</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($kategori as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                        <td><?= htmlspecialchars($item['deskripsi']) ?></td>
                        <td><?= ($item['jumlah_masuk'] + $item['jumlah_keluar']) ?></td>
                        <td>
                            <a href="/kategori/<?= $item['id'] ?>/edit" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/kategori/<?= $item['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($kategori)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada kategori</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
