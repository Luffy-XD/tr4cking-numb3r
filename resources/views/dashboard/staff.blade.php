<?php ob_start(); ?>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="card">
        <div class="text-sm text-slate-500">My Incoming Letters</div>
        <div class="text-3xl font-semibold mt-2"><?= (int) $incomingCount ?></div>
    </div>
    <div class="card">
        <div class="text-sm text-slate-500">My Outgoing Letters</div>
        <div class="text-3xl font-semibold mt-2"><?= (int) $outgoingCount ?></div>
    </div>
    <div class="card">
        <div class="text-sm text-slate-500">Latest Update</div>
        <div class="text-3xl font-semibold mt-2">Today</div>
        <div class="text-xs text-slate-400 mt-2">Keep your letters organized!</div>
    </div>
</div>
<div class="grid gap-6 lg:grid-cols-2">
    <div class="table-wrapper">
        <div class="flex items-center justify-between p-4">
            <h2 class="text-lg font-semibold text-slate-700">Recent Incoming Letters</h2>
        </div>
        <table>
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th>Letter No.</th>
                    <th>Sender</th>
                    <th>Date</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentIncoming as $letter): ?>
                    <tr class="text-sm text-slate-600">
                        <td><?= htmlspecialchars($letter['no_surat']) ?></td>
                        <td><?= htmlspecialchars($letter['sender']) ?></td>
                        <td><?= htmlspecialchars($letter['tanggal_masuk']) ?></td>
                        <td><?= htmlspecialchars($letter['nama_kategori']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="table-wrapper">
        <div class="flex items-center justify-between p-4">
            <h2 class="text-lg font-semibold text-slate-700">Recent Outgoing Letters</h2>
        </div>
        <table>
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th>Letter No.</th>
                    <th>Receiver</th>
                    <th>Date</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOutgoing as $letter): ?>
                    <tr class="text-sm text-slate-600">
                        <td><?= htmlspecialchars($letter['no_surat']) ?></td>
                        <td><?= htmlspecialchars($letter['receiver']) ?></td>
                        <td><?= htmlspecialchars($letter['tanggal_keluar']) ?></td>
                        <td><?= htmlspecialchars($letter['nama_kategori']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
