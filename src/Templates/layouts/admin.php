<?php

declare(strict_types=1);

/**
 * @var string $content
 * @var App\Domain\User\User|null $user
 * @var bool $isGuest
 */

use App\Infrastructure\View\AssetHelper;

$assetBase = '/assets';
$isGuest = $isGuest ?? true;
$csrfToken = $csrfToken ?? '';

// Use AssetHelper for production builds with hashed filenames
$themeInitJs = AssetHelper::js('init.js');
$cssBundle = AssetHelper::css('css.css');
$appJs = AssetHelper::js('app.js');

// Admin-specific CSS
$adminCss = AssetHelper::css('use-cases/admin/admin.css');

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Yii Boot</title>
    <meta name="description" content="Admin Dashboard">
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <?= $themeInitJs ?>
    <?= $cssBundle ?>
    <?= $adminCss ?>

    <style>
        /* Admin Layout Styles */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 250px;
            background: #1a1a2e;
            color: #fff;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .admin-sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar nav li {
            margin-bottom: 0.5rem;
        }

        .admin-sidebar nav a {
            color: #a0a0a0;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: #16213e;
            color: #fff;
        }

        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            background: #f5f5f5;
            min-height: 100vh;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .admin-header h1 {
            font-size: 2rem;
            color: #333;
            margin: 0;
        }

        .admin-user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-user-menu a {
            color: #505050;
            text-decoration: none;
        }

        .admin-user-menu a:hover {
            color: #000;
        }

        .admin-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            margin: 0;
        }

        .stat-card p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #667eea;
            color: #fff;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-success {
            background: #28a745;
            color: #fff;
        }

        .badge-secondary {
            background: #6c757d;
            color: #fff;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .col-md-8 {
            flex: 0 0 66.666%;
        }

        .col-md-4 {
            flex: 0 0 33.333%;
        }

        .col-md-6 {
            flex: 0 0 50%;
        }

        .col-md-3 {
            flex: 0 0 25%;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .admin-content {
                margin-left: 0;
            }

            .col-md-8,
            .col-md-4,
            .col-md-6,
            .col-md-3 {
                flex: 0 0 100%;
            }
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>🎛️ Admin</h2>
            <nav>
                <ul>
                    <li><a href="/admin" class="<?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">📊 Dashboard</a></li>
                    <li><a href="/admin/pages" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/pages') ? 'active' : '' ?>">📄 Pages</a></li>
                    <li><a href="/admin/blog/manage">📝 Blog</a></li>
                    <li><a href="/admin/forms">📋 Forms</a></li>
                    <li><a href="/admin/users">👥 Users</a></li>
                    <li><a href="/">🏠 Back to Site</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <header class="admin-header">
                <h1><?= $pageTitle ?? 'Admin' ?></h1>
                <div class="admin-user-menu">
                    <?php if (!$isGuest && isset($user)): ?>
                        <span>👤 <?= htmlspecialchars($user->name) ?></span>
                        <a href="/profile">Profile</a>
                        <form method="post" action="/logout" style="display:inline;">
                            <input type="hidden" name="_csrf" value="<?= $csrfToken ?>">
                            <button type="submit" class="btn btn-secondary">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Login</a>
                    <?php endif; ?>
                </div>
            </header>

            <?= $content ?>
        </main>
    </div>

    <!-- Scripts -->
    <?= $appJs ?>
</body>

</html>
