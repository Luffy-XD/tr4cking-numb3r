<?php $pageTitle = 'Laporan Surat'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="mb-3">Filter Laporan</h4>
        <form method="GET" action="/laporan" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Jenis Surat</label>
                <select name="jenis" class="form-select" id="jenisSelect">
                    <option value="masuk" <?= $jenis === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
                    <option value="keluar" <?= $jenis === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Periode</label>
                <select name="periode" class="form-select" id="periodeSelect">
                    <option value="harian" <?= $filters['periode'] === 'harian' ? 'selected' : '' ?>>Harian</option>
                    <option value="bulanan" <?= $filters['periode'] === 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                    <option value="tahunan" <?= $filters['periode'] === 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                    <option value="rentang" <?= $filters['periode'] === 'rentang' ? 'selected' : '' ?>>Rentang Tanggal</option>
                </select>
            </div>
            <div class="col-md-3 periode-harian">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-md-3 periode-bulanan">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    <?php for ($i=1;$i<=12;$i++): ?>
                        <option value="<?= $i ?>" <?= ($filters['bulan'] ?? '') == $i ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 periode-bulanan periode-tahunan">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?= htmlspecialchars($filters['tahun']) ?>">
            </div>
            <div class="col-md-3 periode-rentang">
                <label class="form-label">Mulai</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-md-3 periode-rentang">
                <label class="form-label">Selesai</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach ($kategori as $kat): ?>
                        <option value="<?= $kat['id'] ?>" <?= ($filters['kategori_id'] ?? '') == $kat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tampilkan</button>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Hasil Laporan</h4>
            <div class="btn-group">
                <a href="<?= '/laporan?'.http_build_query(array_merge($filters, ['jenis'=>$jenis, 'export'=>'pdf'])) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-filetype-pdf"></i> Export PDF</a>
                <a href="<?= '/laporan?'.http_build_query(array_merge($filters, ['jenis'=>$jenis, 'export'=>'excel'])) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> Export Excel</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="table-light">
                <tr>
                    <th>No Surat</th>
                    <th><?= $jenis === 'masuk' ? 'Pengirim' : 'Penerima' ?></th>
                    <th>Tanggal</th>
                    <th>Perihal</th>
                    <th>Kategori</th>
                    <th>Petugas</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_surat']) ?></td>
                        <td><?= htmlspecialchars($row[$jenis === 'masuk' ? 'pengirim' : 'penerima']) ?></td>
                        <td><?= format_date($row[$jenis === 'masuk' ? 'tanggal_masuk' : 'tanggal_keluar']) ?></td>
                        <td><?= htmlspecialchars($row['perihal']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td><?= htmlspecialchars($row['pembuat'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data untuk filter ini</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function togglePeriodeFields() {
        const periode = document.getElementById('periodeSelect').value;
        document.querySelectorAll('.periode-harian').forEach(el => el.style.display = periode === 'harian' ? 'block' : 'none');
        document.querySelectorAll('.periode-bulanan').forEach(el => el.style.display = periode === 'bulanan' ? 'block' : 'none');
        document.querySelectorAll('.periode-tahunan').forEach(el => el.style.display = ['tahunan','bulanan'].includes(periode) ? 'block' : 'none');
        document.querySelectorAll('.periode-rentang').forEach(el => el.style.display = periode === 'rentang' ? 'block' : 'none');
    }
    document.getElementById('periodeSelect').addEventListener('change', togglePeriodeFields);
    togglePeriodeFields();
</script>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
