<?php $pageTitle = $user ? 'Edit User' : 'Tambah User'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<?php $action = $user ? '/users/' . $user['id'] . '/update' : '/users'; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-4"><?= $user ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h4>
        <form method="POST" action="<?= $action ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control<?= !empty($errors['name']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($user['name'] ?? old('name')) ?>">
                    <?php if (!empty($errors['name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['name'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control<?= !empty($errors['email']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($user['email'] ?? old('email')) ?>">
                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select<?= !empty($errors['role']) ? ' is-invalid' : '' ?>">
                        <option value="admin" <?= ($user['role'] ?? old('role')) === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="staff" <?= ($user['role'] ?? old('role')) === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                    <?php if (!empty($errors['role'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['role'][0]) ?></div><?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= ($user['status'] ?? old('status', 'active')) === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= ($user['status'] ?? old('status')) === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control" placeholder="Kosongkan untuk default">
                    <small class="text-muted">Default: password123</small>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/users" class="btn btn-outline-secondary">Kembali</a>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
