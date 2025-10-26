<?php $pageTitle = 'Dashboard Admin'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Surat Masuk</h6>
                <h2 class="fw-bold text-primary"><?= $totalMasuk ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Surat Keluar</h6>
                <h2 class="fw-bold text-success"><?= $totalKeluar ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Grafik Surat Per Bulan (<?= date('Y') ?>)</h6>
                <canvas id="chartSurat" height="160"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Surat Masuk Terbaru</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>No Surat</th>
                            <th>Pengirim</th>
                            <th>Tanggal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($terbaruMasuk as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['no_surat']) ?></td>
                                <td><?= htmlspecialchars($item['pengirim']) ?></td>
                                <td><?= format_date($item['tanggal_masuk']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($terbaruMasuk)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Surat Keluar Terbaru</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>No Surat</th>
                            <th>Penerima</th>
                            <th>Tanggal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($terbaruKeluar as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['no_surat']) ?></td>
                                <td><?= htmlspecialchars($item['penerima']) ?></td>
                                <td><?= format_date($item['tanggal_keluar']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($terbaruKeluar)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mt-1">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Aktivitas Terbaru</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($audit as $log): ?>
                            <tr>
                                <td><?= format_date($log['created_at'], 'd/m/Y H:i') ?></td>
                                <td><?= htmlspecialchars($log['name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(strtoupper($log['action'])) ?> - <?= htmlspecialchars($log['module']) ?> (<?= htmlspecialchars($log['description']) ?>)</td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($audit)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Belum ada aktivitas</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartSurat');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [
                    {
                        label: 'Surat Masuk',
                        backgroundColor: 'rgba(13,110,253,0.6)',
                        borderColor: 'rgba(13,110,253,1)',
                        data: <?= json_encode(array_values($grafikMasuk)) ?>
                    },
                    {
                        label: 'Surat Keluar',
                        backgroundColor: 'rgba(25,135,84,0.6)',
                        borderColor: 'rgba(25,135,84,1)',
                        data: <?= json_encode(array_values($grafikKeluar)) ?>
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
