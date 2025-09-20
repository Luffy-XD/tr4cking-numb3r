<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars(config('app.name')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full space-y-6">
        <h1 class="text-3xl font-semibold text-slate-800 text-center">Welcome Back</h1>
        <?php if (!empty($success)): ?>
            <div class="bg-emerald-100 text-emerald-700 px-3 py-2 rounded-lg text-sm"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-rose-100 text-rose-600 px-3 py-2 rounded-lg text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/login" class="space-y-4">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div>
                <label class="text-sm text-slate-600">Email or Username</label>
                <input type="text" name="login" value="<?= htmlspecialchars(old('login')) ?>" class="mt-1 w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Password</label>
                <input type="password" name="password" class="mt-1 w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">Sign In</button>
        </form>
        <p class="text-xs text-center text-slate-400">Default admin: admin@example.com / password123</p>
    </div>
</body>
</html>
