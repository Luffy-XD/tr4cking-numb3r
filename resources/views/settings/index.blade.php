<?php ob_start(); ?>
<div class="grid gap-6 lg:grid-cols-2">
    <div class="card space-y-4">
        <h1 class="text-xl font-semibold text-slate-700">Institution Identity</h1>
        <form action="/settings" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-500">Institution Name</label>
                <input type="text" name="institution_name" value="<?= htmlspecialchars($settings['institution_name'] ?? '') ?>" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Address</label>
                <textarea name="institution_address" class="w-full px-3 py-2 border rounded-lg" rows="3" required><?= htmlspecialchars($settings['institution_address'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="text-sm text-slate-500">Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                <?php if (!empty($settings['logo_path'])):
                    $logoFile = storage_path('app/' . $settings['logo_path']);
                    if (file_exists($logoFile)) {
                        $mime = mime_content_type($logoFile);
                        $base64 = base64_encode(file_get_contents($logoFile));
                        echo '<img src="data:' . htmlspecialchars($mime) . ';base64,' . $base64 . '" alt="Logo" class="h-16 mt-2 rounded">';
                    }
                endif; ?>
            </div>
            <div>
                <label class="text-sm text-slate-500">Max Upload Size (KB)</label>
                <input type="number" name="max_upload_size" value="<?= htmlspecialchars($settings['max_upload_size'] ?? 10240) ?>" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="flex justify-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Settings</button>
            </div>
        </form>
    </div>
    <div class="space-y-6">
        <div class="card space-y-3">
            <h2 class="text-lg font-semibold text-slate-700">Backup & Restore</h2>
            <a href="/settings/backup" class="px-4 py-2 bg-emerald-500 text-white rounded-lg inline-block">Generate Backup</a>
            <form action="/settings/restore" method="POST" enctype="multipart/form-data" class="space-y-3">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input type="file" name="backup" accept=".sql" class="w-full px-3 py-2 border rounded-lg" required>
                <button class="px-4 py-2 bg-rose-500 text-white rounded-lg">Restore Database</button>
            </form>
        </div>
        <div class="card space-y-3">
            <h2 class="text-lg font-semibold text-slate-700">Audit Trail (latest)</h2>
            <div class="max-h-64 overflow-y-auto space-y-3">
                <?php
                $logs = \App\Models\AuditTrail::query('SELECT a.*, u.name FROM audit_trails a JOIN users u ON u.id = a.user_id ORDER BY a.id DESC LIMIT 10');
                foreach ($logs as $log): ?>
                    <div class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                        <div class="font-semibold text-slate-700"><?= htmlspecialchars($log['name']) ?> - <?= htmlspecialchars($log['action']) ?></div>
                        <div class="text-slate-500 text-xs"><?= htmlspecialchars($log['description']) ?></div>
                        <div class="text-slate-400 text-xs"><?= htmlspecialchars($log['created_at']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
