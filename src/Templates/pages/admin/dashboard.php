<?php
/**
 * Admin Dashboard Template
 * Based on vzor design - dark theme with glassmorphism
 *
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 */

use Infrastructure\View\AssetHelper;

$adminCss = AssetHelper::css('admin');
$adminJs = AssetHelper::js('admin');
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title) ?> - Admin</title>
    <link rel="stylesheet" href="<?= $adminCss ?>">
</head>
<body>
    <!-- Topbar -->
    <header class="topbar">
        <div>
            <div class="title">Nativa Admin</div>
            <div class="subtitle">CMS Dashboard • <?= date('Y-m-d H:i') ?></div>
        </div>
        
        <div class="topbar-actions">
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" type="button">
                <svg class="theme-icon theme-icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="theme-icon theme-icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
            
            <!-- Logout -->
            <a href="/logout" class="btn-logout" title="Logout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
            
            <!-- Clock -->
            <div class="clock">
                <div id="time" class="time"><?= date('H:i') ?></div>
                <div id="date" class="date"><?= date('Y-m-d') ?></div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <main class="layout">
        <!-- Quick Stats -->
        <section class="card">
            <h2>Quick Stats</h2>
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Articles</div>
                </div>
                <div class="stat">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Forms</div>
                </div>
                <div class="stat">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Contacts</div>
                </div>
                <div class="stat">
                    <div class="stat-value">1</div>
                    <div class="stat-label">Users</div>
                </div>
            </div>
        </section>

        <!-- Quick Links -->
        <section class="card">
            <h2>Quick Links</h2>
            <div class="grid">
                <a href="/admin/articles" class="tile">
                    <span class="name">Articles</span>
                    <span class="desc">Manage blog posts</span>
                </a>
                <a href="/admin/pages" class="tile">
                    <span class="name">Pages</span>
                    <span class="desc">Static pages</span>
                </a>
                <a href="/admin/forms" class="tile">
                    <span class="name">Forms</span>
                    <span class="desc">Form builder</span>
                </a>
                <a href="/admin/media" class="tile">
                    <span class="name">Media</span>
                    <span class="desc">Media library</span>
                </a>
            </div>
        </section>

        <!-- Create New -->
        <section class="card">
            <h2>Create New</h2>
            <div class="row">
                <a href="/admin/articles/create" class="btn">+ Article</a>
                <a href="/admin/pages/create" class="btn secondary">+ Page</a>
                <a href="/admin/forms/create" class="btn secondary">+ Form</a>
            </div>
        </section>

        <!-- System Info -->
        <section class="card">
            <h2>System</h2>
            <div class="cmd">
                <div class="cmdRow">
                    <span class="label">PHP Version</span>
                    <code><?= phpversion() ?></code>
                    <button class="copy" data-copy="<?= phpversion() ?>">Copy</button>
                </div>
                <div class="cmdRow">
                    <span class="label">Database</span>
                    <code>SQLite</code>
                    <button class="copy" data-copy="SQLite">Copy</button>
                </div>
                <div class="cmdRow">
                    <span class="label">Environment</span>
                    <code><?= htmlspecialchars(is_string($_ENV['APP_ENV'] ?? '') ? ($_ENV['APP_ENV'] ?? 'development') : 'development') ?></code>
                    <button class="copy" data-copy="<?= htmlspecialchars(is_string($_ENV['APP_ENV'] ?? '') ? ($_ENV['APP_ENV'] ?? 'development') : 'development') ?>">Copy</button>
                </div>
            </div>
        </section>

        <!-- Notes -->
        <section class="card">
            <h2>Notes</h2>
            <textarea id="notes" class="notes" placeholder="Admin notes (autosaves locally)"></textarea>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <span>Nativa CMS v1.0 - Built with PHP 8.4+</span>
    </footer>

    <script src="<?= $adminJs ?>" defer></script>
</body>
</html>
