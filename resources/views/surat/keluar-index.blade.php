<?php $pageTitle = 'Surat Keluar'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Data Surat Keluar</h4>
    <a href="/surat-keluar/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Surat</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle">
                <thead class="table-light">
                <tr>
                    <th>No Surat</th>
                    <th>Penerima</th>
                    <th>Tanggal Keluar</th>
                    <th>Perihal</th>
                    <th>Kategori</th>
                    <th>Dibuat Oleh</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($surat as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['no_surat']) ?></td>
                        <td><?= htmlspecialchars($item['penerima']) ?></td>
                        <td><?= format_date($item['tanggal_keluar']) ?></td>
                        <td><?= htmlspecialchars($item['perihal']) ?></td>
                        <td><?= htmlspecialchars($item['nama_kategori'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['pembuat'] ?? '-') ?></td>
                        <td class="table-actions">
                            <a href="/surat-keluar/<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="/surat-keluar/<?= $item['id'] ?>/download" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a>
                            <a href="/surat-keluar/<?= $item['id'] ?>/edit" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/surat-keluar/<?= $item['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Hapus surat ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($surat)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
