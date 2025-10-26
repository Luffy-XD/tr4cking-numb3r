<?php $pageTitle = 'Dashboard'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Surat Masuk Saya</h6>
                <h2 class="fw-bold text-primary"><?= $totalMasuk ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Surat Keluar Saya</h6>
                <h2 class="fw-bold text-success"><?= $totalKeluar ?></h2>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">Aktivitas Surat Terbaru</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Jenis</th>
                    <th>No Surat</th>
                    <th>Tujuan/Pengirim</th>
                    <th>Tanggal</th>
                    <th>Perihal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($terbaru as $item): ?>
                    <tr>
                        <td><span class="badge <?= $item['jenis'] === 'masuk' ? 'bg-primary' : 'bg-success' ?>"><?= strtoupper($item['jenis']) ?></span></td>
                        <td><?= htmlspecialchars($item['no_surat']) ?></td>
                        <td><?= htmlspecialchars($item['jenis'] === 'masuk' ? $item['pengirim'] : $item['penerima']) ?></td>
                        <td><?= format_date($item['jenis'] === 'masuk' ? $item['tanggal_masuk'] : $item['tanggal_keluar']) ?></td>
                        <td><?= htmlspecialchars($item['perihal']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($terbaru)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Belum ada data surat</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
