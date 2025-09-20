<?php $pageTitle = 'Manajemen User'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Pengguna</h4>
    <a href="/users/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah User</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($user['role']) ?></span></td>
                        <td><span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>"><?= strtoupper($user['status']) ?></span></td>
                        <td class="table-actions">
                            <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/users/<?= $user['id'] ?>/reset" class="d-inline" onsubmit="return confirm('Reset password user ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-clockwise"></i></button>
                            </form>
                            <form method="POST" action="/users/<?= $user['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Belum ada pengguna</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
