<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= htmlspecialchars($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard"><?= htmlspecialchars($appName) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($authUser): ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link<?= $_SERVER['REQUEST_URI'] === '/dashboard' ? ' active' : '' ?>" href="/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/surat-masuk') ? ' active' : '' ?>" href="/surat-masuk">Surat Masuk</a></li>
                <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/surat-keluar') ? ' active' : '' ?>" href="/surat-keluar">Surat Keluar</a></li>
                <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/laporan') ? ' active' : '' ?>" href="/laporan">Laporan</a></li>
                <?php if ($authUser['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/kategori') ? ' active' : '' ?>" href="/kategori">Kategori Surat</a></li>
                    <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/users') ? ' active' : '' ?>" href="/users">Manajemen User</a></li>
                    <li class="nav-item"><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/settings') ? ' active' : '' ?>" href="/settings">Pengaturan</a></li>
                <?php endif; ?>
            </ul>
            <form method="POST" action="/logout" class="d-flex">
                <?= csrf_field() ?>
                <span class="navbar-text me-3"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($authUser['name']) ?> (<?= htmlspecialchars(strtoupper($authUser['role'])) ?>)</span>
                <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container py-4">
    <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>
<div class="container pb-5">
