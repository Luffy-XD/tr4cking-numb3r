<?php ob_start(); ?>
<div class="grid gap-6 lg:grid-cols-4">
    <div class="card lg:col-span-2">
        <div class="text-sm text-slate-500">Total Incoming Letters</div>
        <div class="text-3xl font-semibold mt-2"><?= (int) $incomingCount ?></div>
        <div class="text-xs text-slate-400 mt-2">All time records</div>
    </div>
    <div class="card lg:col-span-2">
        <div class="text-sm text-slate-500">Total Outgoing Letters</div>
        <div class="text-3xl font-semibold mt-2"><?= (int) $outgoingCount ?></div>
        <div class="text-xs text-slate-400 mt-2">All time records</div>
    </div>
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-700">Monthly Activity</h2>
            <div class="text-sm text-slate-400">Current Year</div>
        </div>
        <canvas id="dashboardChart"></canvas>
    </div>
    <div class="card space-y-4">
        <h2 class="text-lg font-semibold text-slate-700">Categories</h2>
        <div class="space-y-3 max-h-72 overflow-y-auto pr-2">
            <?php foreach ($categories as $category): ?>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div>
                        <div class="font-semibold text-slate-700"><?= htmlspecialchars($category['nama_kategori']) ?></div>
                        <div class="text-xs text-slate-500"><?= htmlspecialchars($category['description'] ?? '-') ?></div>
                    </div>
                    <div class="text-right text-sm text-slate-500">
                        <div><?= (int) $category['incoming_count'] ?> incoming</div>
                        <div><?= (int) $category['outgoing_count'] ?> outgoing</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('dashboardChart');
        if (!ctx) return;
        fetch('/dashboard/chart-data')
            .then(resp => resp.json())
            .then(data => {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Incoming',
                                data: data.incoming,
                                borderColor: '#3b82f6',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Outgoing',
                                data: data.outgoing,
                                borderColor: '#f97316',
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            });
    });
</script>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
