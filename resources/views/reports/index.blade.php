<?php ob_start(); ?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h1 class="text-2xl font-semibold text-slate-700">Reports</h1>
    <div class="flex gap-2">
        <a href="/reports/export/pdf?type=<?= urlencode($type) ?>&period=<?= urlencode($period) ?>&date=<?= urlencode($date) ?>" class="px-4 py-2 bg-rose-500 text-white rounded-lg">Export PDF</a>
        <a href="/reports/export/excel?type=<?= urlencode($type) ?>&period=<?= urlencode($period) ?>&date=<?= urlencode($date) ?>" class="px-4 py-2 bg-emerald-500 text-white rounded-lg">Export Excel</a>
    </div>
</div>
<form method="GET" class="card mt-6 grid md:grid-cols-4 gap-4">
    <div>
        <label class="text-sm text-slate-500">Letter Type</label>
        <select name="type" class="w-full px-3 py-2 border rounded-lg">
            <option value="incoming" <?= $type === 'incoming' ? 'selected' : '' ?>>Incoming</option>
            <option value="outgoing" <?= $type === 'outgoing' ? 'selected' : '' ?>>Outgoing</option>
        </select>
    </div>
    <div>
        <label class="text-sm text-slate-500">Period</label>
        <select name="period" class="w-full px-3 py-2 border rounded-lg">
            <option value="daily" <?= $period === 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="monthly" <?= $period === 'monthly' ? 'selected' : '' ?>>Monthly</option>
            <option value="yearly" <?= $period === 'yearly' ? 'selected' : '' ?>>Yearly</option>
        </select>
    </div>
    <div>
        <label class="text-sm text-slate-500">Reference Date</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div class="flex items-end">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg w-full">Apply Filters</button>
    </div>
</form>

<div class="table-wrapper mt-6">
    <table>
        <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                <th>Letter Number</th>
                <th><?= $type === 'incoming' ? 'Sender' : 'Receiver' ?></th>
                <th><?= $type === 'incoming' ? 'Date Received' : 'Date Sent' ?></th>
                <th>Subject</th>
                <th>Category</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr class="text-sm text-slate-600">
                    <td><?= htmlspecialchars($row['no_surat']) ?></td>
                    <td><?= htmlspecialchars($type === 'incoming' ? $row['sender'] : $row['receiver']) ?></td>
                    <td><?= htmlspecialchars($type === 'incoming' ? $row['tanggal_masuk'] : $row['tanggal_keluar']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                    <td><?= htmlspecialchars($row['creator']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
