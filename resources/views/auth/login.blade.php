<?php $pageTitle = 'Login'; include __DIR__ . '/../layouts/header.blade.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Masuk ke Sistem</h3>
                <form method="POST" action="/login">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Email atau Username</label>
                        <input type="text" name="login" class="form-control<?= !empty($errors['login']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars(old('login')) ?>" required>
                        <?php if (!empty($errors['login'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['login'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control<?= !empty($errors['password']) ? ' is-invalid' : '' ?>" required>
                        <?php if (!empty($errors['password'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['password'][0]) ?></div><?php endif; ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.blade.php'; ?>
