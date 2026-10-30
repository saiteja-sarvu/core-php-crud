<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Dashboard';
$activeMenu = $activeMenu ?? 'dashboard';
$user = current_user() ?? [];
$userName = $user['name'] ?? 'User';
$userEmail = $user['email'] ?? '';
$initial = strtoupper(substr((string)$userName, 0, 1));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($pageTitle) ?> | <?= escape(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= escape(url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<div class="app-sidebar">
    <div class="px-4 py-4">
        <a class="brand text-decoration-none" href="<?= escape(url('modules/dashboard/index.php')) ?>">
            <span class="brand-mark"><i class="bi bi-people-fill"></i></span><?= escape(APP_NAME) ?>
        </a>
    </div>
    <div class="side-label">Workspace</div>
    <nav class="sidebar-nav">
        <a class="side-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" href="<?= escape(url('modules/dashboard/index.php')) ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a class="side-link <?= $activeMenu === 'products' ? 'active' : '' ?>" href="<?= escape(url('modules/products/index.php')) ?>"><i class="bi bi-box-seam"></i> Products</a>
    </nav>
    <div class="side-label">Account</div>
    <nav class="sidebar-nav">
        <a class="side-link <?= $activeMenu === 'users' ? 'active' : '' ?>" href="<?= escape(url('modules/users/index.php')) ?>"><i class="bi bi-shield-lock"></i> Users</a>
        <a class="side-link" href="<?= escape(url('modules/auth/logout.php')) ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
    <div class="sidebar-caption position-absolute bottom-0 p-4 small d-none d-lg-block">Project-Panel · Core PHP</div>
</div>
<main class="app-main">
    <header class="topbar d-flex align-items-center justify-content-between px-4 px-lg-5">
        <div class="muted small"><i class="bi bi-house-door me-1"></i> Project-Panel <span class="mx-2">/</span> <?= escape($pageTitle) ?></div>
        <div class="d-flex align-items-center gap-2">
            <div class="text-end d-none d-sm-block">
                <div class="fw-semibold small"><?= escape($userName) ?></div>
                <div class="muted text-xs"><?= escape($userEmail) ?></div>
            </div>
            <span class="avatar"><?= escape($initial) ?></span>
        </div>
    </header>
    <div class="content-wrap">
        <?php display_flash(); ?>
