<?php ob_start(); ?>
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-700">Outgoing Letters</h1>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" data-toggle="#createOutgoingModal">Add Letter</button>
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
                <th>Receiver</th>
                <th>Date Sent</th>
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
                    <td><?= htmlspecialchars($letter['receiver']) ?></td>
                    <td><?= htmlspecialchars($letter['tanggal_keluar']) ?></td>
                    <td><?= htmlspecialchars($letter['subject']) ?></td>
                    <td><?= htmlspecialchars($letter['nama_kategori']) ?></td>
                    <td><?= htmlspecialchars($letter['creator']) ?></td>
                    <td class="space-x-2">
                        <a class="text-blue-600" href="/outgoing-letters/<?= $letter['id'] ?>">View</a>
                        <a class="text-emerald-600" href="/outgoing-letters/<?= $letter['id'] ?>/download">Download</a>
                        <button type="button" class="text-amber-600" data-toggle="#editOutgoingModal" data-letter='<?= json_encode($letter) ?>'>Edit</button>
                        <form action="/outgoing-letters/<?= $letter['id'] ?>/delete" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="text-rose-600" onclick="return confirm('Delete this letter?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="createOutgoingModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Add Outgoing Letter</h2>
            <button data-close="#createOutgoingModal">&times;</button>
        </div>
        <form action="/outgoing-letters" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-500">Letter Number</label>
                <input type="text" name="no_surat" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Receiver</label>
                <input type="text" name="receiver" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Date Sent</label>
                <input type="date" name="tanggal_keluar" class="w-full px-3 py-2 border rounded-lg" required>
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
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#createOutgoingModal">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Letter</button>
            </div>
        </form>
    </div>
</div>

<div id="editOutgoingModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Edit Outgoing Letter</h2>
            <button data-close="#editOutgoingModal">&times;</button>
        </div>
        <form id="editOutgoingForm" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-500">Letter Number</label>
                <input type="text" name="no_surat" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Receiver</label>
                <input type="text" name="receiver" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Date Sent</label>
                <input type="date" name="tanggal_keluar" class="w-full px-3 py-2 border rounded-lg" required>
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
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#editOutgoingModal">Cancel</button>
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
                const form = document.getElementById('editOutgoingForm');
                form.action = `/outgoing-letters/${data.id}/update`;
                form.querySelector('[name=no_surat]').value = data.no_surat;
                form.querySelector('[name=receiver]').value = data.receiver;
                form.querySelector('[name=tanggal_keluar]').value = data.tanggal_keluar;
                form.querySelector('[name=subject]').value = data.subject;
                form.querySelector('[name=kategori_id]').value = data.kategori_id;
            });
        });
    });
</script>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
