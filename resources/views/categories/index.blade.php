<?php ob_start(); ?>
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-700">Letter Categories</h1>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" data-toggle="#categoryModal">Add Category</button>
</div>
<div class="table-wrapper mt-6">
    <table>
        <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                <th>Name</th>
                <th>Description</th>
                <th>Total Letters</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr class="text-sm text-slate-600">
                    <td><?= htmlspecialchars($category['nama_kategori']) ?></td>
                    <td><?= htmlspecialchars($category['description'] ?? '-') ?></td>
                    <td><?= (int) $category['incoming_count'] + (int) $category['outgoing_count'] ?></td>
                    <td class="space-x-2">
                        <button type="button" class="text-amber-600" data-toggle="#categoryModal" data-category='<?= json_encode($category) ?>'>Edit</button>
                        <form action="/letter-categories/<?= $category['id'] ?>/delete" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="text-rose-600" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="categoryModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Manage Category</h2>
            <button data-close="#categoryModal">&times;</button>
        </div>
        <form id="categoryForm" method="POST" class="space-y-4">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id">
            <div>
                <label class="text-sm text-slate-500">Category Name</label>
                <input type="text" name="nama_kategori" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Description</label>
                <textarea name="description" class="w-full px-3 py-2 border rounded-lg" rows="3" required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#categoryModal">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-category]').forEach(button => {
            button.addEventListener('click', () => {
                const data = JSON.parse(button.dataset.category);
                const form = document.getElementById('categoryForm');
                form.action = '/letter-categories';
                form.querySelector('[name=id]').value = data.id;
                form.querySelector('[name=nama_kategori]').value = data.nama_kategori;
                form.querySelector('[name=description]').value = data.description || '';
            });
        });
        document.querySelector('[data-toggle="#categoryModal"]').addEventListener('click', () => {
            const form = document.getElementById('categoryForm');
            form.querySelector('[name=id]').value = '';
            form.querySelector('[name=nama_kategori]').value = '';
            form.querySelector('[name=description]').value = '';
        });
    });
</script>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
