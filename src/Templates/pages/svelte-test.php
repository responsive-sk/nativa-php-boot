<?php
/**
 * Svelte Hybrid Test Page
 * 
 * @var array $articles
 * @var string $pageTitle
 */

use Infrastructure\View\AssetHelper;

$articleListJs = AssetHelper::js('article-list');
$themeToggleJs = AssetHelper::js('theme-toggle');
$navigationJs = AssetHelper::js('navigation');
$toastJs = AssetHelper::js('toast');
$contactFormCss = AssetHelper::css('contact-form');
$navigationCss = AssetHelper::css('navigation');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $contactFormCss ?>">
    <link rel="stylesheet" href="<?= $navigationCss ?>">
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f5f5f5;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --accent: #007bff;
            --card-bg: #ffffff;
            --border: #e0e0e0;
        }
        
        .dark {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --card-bg: #2d2d2d;
            --border: #404040;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        header {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--bg-secondary);
        }
        
        h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        
        .test-section {
            margin-bottom: 4rem;
        }
        
        .test-info {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .test-info code {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        
        .dark .test-info code {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .article-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .demo-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .demo-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .demo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .demo-btn--success {
            background: #28a745;
            color: white;
        }
        
        .demo-btn--error {
            background: #dc3545;
            color: white;
        }
        
        .demo-btn--info {
            background: #17a2b8;
            color: white;
        }
        
        /* Theme Toggle Styles (inline - no CSS extracted) */
        .theme-toggle-svelte {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 9999px;
            transition: background 0.3s ease;
        }
        
        .theme-toggle-svelte:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .theme-toggle-svelte .icon {
            width: 1.25rem;
            height: 1.25rem;
            transition: transform 0.5s ease, opacity 0.3s ease;
        }
        
        .theme-toggle-svelte .icon--sun {
            color: #f59e0b;
        }
        
        .theme-toggle-svelte .icon--moon {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Svelte Navigation -->
    <div id="nav-container" data-current-page="home"></div>
    
    <div class="container">
        <header>
            <h1>🚀 Svelte Hybrid Test</h1>
            <p>Testing Svelte 4 components with PHP backend</p>
        </header>
        
        <!-- Test 1: Theme Toggle -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 1: Theme Toggle</h2>
                <p><strong>Feature:</strong> Dark/Light mode with localStorage persistence</p>
                <p><strong>How to test:</strong> Click the toggle in navigation</p>
            </div>
            
            <div id="theme-toggle-container"></div>
        </section>
        
        <!-- Test 2: Toast Notifications -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 2: Toast Notifications</h2>
                <p><strong>Feature:</strong> Success/Error/Info notifications with auto-dismiss</p>
                <p><strong>How to test:</strong> Click the buttons below</p>
            </div>
            
            <div class="demo-buttons">
                <button class="demo-btn demo-btn--success" onclick="showSuccess()">
                    ✓ Success Toast
                </button>
                <button class="demo-btn demo-btn--error" onclick="showError()">
                    ✕ Error Toast
                </button>
                <button class="demo-btn demo-btn--info" onclick="showInfo()">
                    ℹ Info Toast
                </button>
            </div>
        </section>
        
        <!-- Test 3: Article List -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 3: ArticleList Component</h2>
                <p><strong>Features:</strong> Live search, filtering, animations</p>
                <p><strong>How to test:</strong> Type in the search box below</p>
            </div>
            
            <div 
                id="article-list-container"
                data-articles='<?= htmlspecialchars(json_encode($articles), ENT_QUOTES, 'UTF-8') ?>'
            >
                <!-- PHP rendered fallback -->
                <div class="php-fallback article-grid">
                    <?php foreach ($articles as $article): ?>
                        <article style="padding: 1.5rem; background: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h3><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
        <!-- Test 4: Contact Form -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 4: ContactForm Component</h2>
                <p><strong>Features:</strong> Validation, AJAX submit, success/error states</p>
                <p><strong>How to test:</strong> Fill the form and submit (won't actually send)</p>
            </div>
            
            <div id="contact-form-container"></div>
        </section>
    </div>
    
    <!-- Load Svelte components (auto-mount) -->
    <script type="module" src="<?= $navigationJs ?>"></script>
    <script type="module" src="<?= $themeToggleJs ?>"></script>
    <script type="module" src="<?= $toastJs ?>"></script>
    <script type="module" src="<?= $articleListJs ?>"></script>
    
    <!-- Toast demo functions -->
    <script>
        // Wait for stores to be available
        setTimeout(() => {
            if (typeof window.notifications !== 'undefined') {
                window.showSuccess = function() {
                    window.notifications.success('Operation completed successfully! 🎉');
                };
                
                window.showError = function() {
                    window.notifications.error('Something went wrong! Please try again.');
                };
                
                window.showInfo = function() {
                    window.notifications.info('Here is some useful information.');
                };
            }
        }, 500);
    </script>
</body>
</html>
