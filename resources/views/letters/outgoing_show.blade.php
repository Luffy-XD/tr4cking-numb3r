<?php ob_start(); ?>
<div class="card space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-700">Outgoing Letter Detail</h1>
        <a href="/outgoing-letters" class="px-3 py-2 bg-slate-200 rounded-lg">Back</a>
    </div>
    <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-600">
        <div><span class="font-semibold text-slate-700">Letter Number:</span> <?= htmlspecialchars($letter['no_surat']) ?></div>
        <div><span class="font-semibold text-slate-700">Receiver:</span> <?= htmlspecialchars($letter['receiver']) ?></div>
        <div><span class="font-semibold text-slate-700">Date Sent:</span> <?= htmlspecialchars($letter['tanggal_keluar']) ?></div>
        <div><span class="font-semibold text-slate-700">Subject:</span> <?= htmlspecialchars($letter['subject']) ?></div>
        <div><span class="font-semibold text-slate-700">Category:</span> <?= htmlspecialchars($category['nama_kategori'] ?? '-') ?></div>
    </div>
    <div class="mt-4">
        <iframe src="/outgoing-letters/<?= $letter['id'] ?>/download?preview=1" class="w-full h-[500px] rounded-lg border"></iframe>
    </div>
</div>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
