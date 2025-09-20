<?php use App\Core\Auth; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f6fa; }
        .sidebar { min-width: 250px; background: linear-gradient(180deg, #1f2937 0%, #111827 100%); color: #f9fafb; }
        .sidebar a { display: block; padding: 0.75rem 1.25rem; border-radius: 0.75rem; color: inherit; text-decoration: none; margin-bottom: 0.5rem; transition: background-color 0.2s ease, transform 0.2s ease; }
        .sidebar a.active, .sidebar a:hover { background-color: rgba(59, 130, 246, 0.2); transform: translateX(4px); }
        .card { background: #ffffff; border-radius: 1rem; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); padding: 1.5rem; }
        .table-wrapper { background: #ffffff; border-radius: 1rem; box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05); overflow-x: auto; }
        .table-wrapper table { width: 100%; border-collapse: collapse; }
        .table-wrapper th, .table-wrapper td { padding: 0.85rem 1rem; border-bottom: 1px solid #eef2f7; }
        .badge { border-radius: 9999px; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background-color: rgba(16, 185, 129, 0.15); color: #047857; }
        .badge-warning { background-color: rgba(251, 191, 36, 0.15); color: #92400e; }
        .modal { display: none; align-items: center; justify-content: center; background-color: rgba(15, 23, 42, 0.45); padding: 1.5rem; }
        .modal:not(.hidden) { display: flex; }
    </style>
</head>
<body class="min-h-screen flex bg-slate-100">
    <?php $user = Auth::user(); ?>
    <aside class="sidebar hidden md:flex flex-col p-6 space-y-6">
        <div class="text-2xl font-semibold">Arsip <span class="text-blue-400">Surat</span></div>
        <nav class="flex-1">
            <a href="/dashboard" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>">Dashboard</a>
            <a href="/incoming-letters" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/incoming-letters') ? 'active' : '' ?>">Incoming Letters</a>
            <a href="/outgoing-letters" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/outgoing-letters') ? 'active' : '' ?>">Outgoing Letters</a>
            <?php if ($user && $user['role'] === 'admin'): ?>
                <a href="/letter-categories" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/letter-categories') ? 'active' : '' ?>">Letter Categories</a>
            <?php endif; ?>
            <a href="/reports" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/reports') ? 'active' : '' ?>">Reports</a>
            <?php if ($user && $user['role'] === 'admin'): ?>
                <a href="/users" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'active' : '' ?>">User Management</a>
                <a href="/settings" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/settings') ? 'active' : '' ?>">System Settings</a>
            <?php endif; ?>
        </nav>
        <form method="POST" action="/logout">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg w-full">Logout</button>
        </form>
    </aside>
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button class="md:hidden inline-flex items-center" data-toggle="#mobileMenu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div class="text-xl font-semibold text-slate-700">Dashboard</div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="font-semibold text-slate-700"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                    <div class="text-sm text-slate-500 capitalize"><?= htmlspecialchars($user['role'] ?? '') ?></div>
                </div>
                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
            </div>
        </header>
        <div id="mobileMenu" class="md:hidden hidden bg-slate-900 text-white p-4 space-y-4">
            <a href="/dashboard" class="block">Dashboard</a>
            <a href="/incoming-letters" class="block">Incoming Letters</a>
            <a href="/outgoing-letters" class="block">Outgoing Letters</a>
            <a href="/reports" class="block">Reports</a>
            <?php if ($user && $user['role'] === 'admin'): ?>
                <a href="/letter-categories" class="block">Letter Categories</a>
                <a href="/users" class="block">User Management</a>
                <a href="/settings" class="block">System Settings</a>
            <?php endif; ?>
        </div>
        <main class="p-6 space-y-6">
            <?php if (!empty($flashMessage = session_flash('status'))): ?>
                <div class="transition-opacity duration-500" data-toast>
                    <div class="bg-emerald-500 text-white px-4 py-3 rounded-lg shadow-lg inline-flex items-center space-x-2">
                        <span><?= htmlspecialchars($flashMessage) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-toggle]').forEach(button => {
                button.addEventListener('click', () => {
                    const target = document.querySelector(button.dataset.toggle);
                    if (target) {
                        target.classList.toggle('hidden');
                    }
                });
            });
            document.querySelectorAll('[data-close]').forEach(button => {
                button.addEventListener('click', () => {
                    const target = document.querySelector(button.dataset.close);
                    if (target) {
                        target.classList.add('hidden');
                    }
                });
            });
            const toast = document.querySelector('[data-toast]');
            if (toast) {
                setTimeout(() => toast.classList.add('opacity-0'), 4000);
            }
        });
    </script>
</body>
</html>
