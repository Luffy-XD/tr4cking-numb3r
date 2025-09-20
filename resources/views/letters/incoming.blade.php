<?php ob_start(); ?>
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-700">Incoming Letters</h1>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" data-toggle="#createIncomingModal">Add Letter</button>
</div>
<div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <form method="GET" class="flex items-center gap-2">
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search letters..." class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button class="px-3 py-2 bg-slate-700 text-white rounded-lg">Search</button>
    </form>
</div>
<div class="table-wrapper mt-6">
    <table>
        <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                <th>Letter Number</th>
                <th>Sender</th>
                <th>Date Received</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($letters as $letter): ?>
                <tr class="text-sm text-slate-600">
                    <td><?= htmlspecialchars($letter['no_surat']) ?></td>
                    <td><?= htmlspecialchars($letter['sender']) ?></td>
                    <td><?= htmlspecialchars($letter['tanggal_masuk']) ?></td>
                    <td><?= htmlspecialchars($letter['subject']) ?></td>
                    <td><?= htmlspecialchars($letter['nama_kategori']) ?></td>
                    <td><?= htmlspecialchars($letter['creator']) ?></td>
                    <td class="space-x-2">
                        <a class="text-blue-600" href="/incoming-letters/<?= $letter['id'] ?>">View</a>
                        <a class="text-emerald-600" href="/incoming-letters/<?= $letter['id'] ?>/download">Download</a>
                        <button type="button" class="text-amber-600" data-toggle="#editIncomingModal" data-letter='<?= json_encode($letter) ?>'>Edit</button>
                        <form action="/incoming-letters/<?= $letter['id'] ?>/delete" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="text-rose-600" onclick="return confirm('Delete this letter?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="createIncomingModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Add Incoming Letter</h2>
            <button data-close="#createIncomingModal">&times;</button>
        </div>
        <form action="/incoming-letters" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-500">Letter Number</label>
                <input type="text" name="no_surat" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Sender</label>
                <input type="text" name="sender" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Date Received</label>
                <input type="date" name="tanggal_masuk" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Subject</label>
                <input type="text" name="subject" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Category</label>
                <select name="kategori_id" class="w-full px-3 py-2 border rounded-lg" required>
                    <option value="">Select</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-sm text-slate-500">Upload PDF</label>
                <input type="file" name="file" accept="application/pdf" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#createIncomingModal">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Letter</button>
            </div>
        </form>
    </div>
</div>

<div id="editIncomingModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Edit Incoming Letter</h2>
            <button data-close="#editIncomingModal">&times;</button>
        </div>
        <form id="editIncomingForm" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-500">Letter Number</label>
                <input type="text" name="no_surat" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Sender</label>
                <input type="text" name="sender" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Date Received</label>
                <input type="date" name="tanggal_masuk" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Subject</label>
                <input type="text" name="subject" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Category</label>
                <select name="kategori_id" class="w-full px-3 py-2 border rounded-lg" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-sm text-slate-500">Replace File (PDF)</label>
                <input type="file" name="file" accept="application/pdf" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="md:col-span-2 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#editIncomingModal">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Letter</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-letter]').forEach(button => {
            button.addEventListener('click', () => {
                const data = JSON.parse(button.dataset.letter);
                const form = document.getElementById('editIncomingForm');
                form.action = `/incoming-letters/${data.id}/update`;
                form.querySelector('[name=no_surat]').value = data.no_surat;
                form.querySelector('[name=sender]').value = data.sender;
                form.querySelector('[name=tanggal_masuk]').value = data.tanggal_masuk;
                form.querySelector('[name=subject]').value = data.subject;
                form.querySelector('[name=kategori_id]').value = data.kategori_id;
            });
        });
    });
</script>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
