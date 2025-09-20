<?php ob_start(); ?>
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-700">User Management</h1>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" data-toggle="#userModal">Add Staff</button>
</div>
<div class="table-wrapper mt-6">
    <table>
        <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="text-sm text-slate-600">
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td class="capitalize"><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <span class="badge <?= $user['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>"><?= htmlspecialchars($user['status']) ?></span>
                    </td>
                    <td class="space-x-2">
                        <button type="button" class="text-amber-600" data-toggle="#userModal" data-user='<?= json_encode($user) ?>'>Edit</button>
                        <form action="/users/<?= $user['id'] ?>/reset" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="text-blue-600">Reset Password</button>
                        </form>
                        <form action="/users/<?= $user['id'] ?>/delete" method="POST" class="inline">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="text-rose-600" onclick="return confirm('Delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="userModal" class="modal fixed inset-0 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">User Form</h2>
            <button data-close="#userModal">&times;</button>
        </div>
        <form id="userForm" method="POST" class="grid gap-4 md:grid-cols-2">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="create">
            <div class="md:col-span-2">
                <label class="text-sm text-slate-500">Name</label>
                <input type="text" name="name" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="md:col-span-2">
                <label class="text-sm text-slate-500">Email</label>
                <input type="email" name="email" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="text-sm text-slate-500">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded-lg">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-slate-500">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="md:col-span-2" id="passwordField">
                <label class="text-sm text-slate-500">Default Password</label>
                <input type="text" name="password" class="w-full px-3 py-2 border rounded-lg" value="password123" required>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 border rounded-lg" data-close="#userModal">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('userForm');
        document.querySelectorAll('[data-user]').forEach(button => {
            button.addEventListener('click', () => {
                const data = JSON.parse(button.dataset.user);
                form.action = `/users/${data.id}/update`;
                form.querySelector('[name=_method]').value = 'update';
                form.querySelector('[name=name]').value = data.name;
                form.querySelector('[name=email]').value = data.email;
                form.querySelector('[name=role]').value = data.role;
                form.querySelector('[name=status]').value = data.status;
                const passwordField = document.getElementById('passwordField');
                passwordField.classList.add('hidden');
                passwordField.querySelector('input').removeAttribute('required');
            });
        });
        document.querySelector('[data-toggle="#userModal"]').addEventListener('click', () => {
            form.action = '/users';
            form.querySelector('[name=_method]').value = 'create';
            form.querySelector('[name=name]').value = '';
            form.querySelector('[name=email]').value = '';
            form.querySelector('[name=role]').value = 'staff';
            form.querySelector('[name=status]').value = 'active';
            const passwordField = document.getElementById('passwordField');
            passwordField.classList.remove('hidden');
            passwordField.querySelector('input').value = 'password123';
            passwordField.querySelector('input').setAttribute('required', 'required');
        });
    });
</script>
<?php $content = ob_get_clean(); include resource_path('views/layouts/app.blade.php'); ?>
