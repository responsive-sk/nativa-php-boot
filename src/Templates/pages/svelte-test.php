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
$designSystemCss = AssetHelper::css('design-system');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $designSystemCss ?>">
    <style>
        /* Demo page specific styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-8);
        }
        
        .test-section {
            margin-bottom: var(--spacing-16);
        }
        
        .test-info {
            background: var(--color-bg-secondary);
            padding: var(--spacing-6);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-6);
        }
        
        .demo-buttons {
            display: flex;
            gap: var(--spacing-4);
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <!-- Svelte Navigation -->
    <div id="nav-container" data-current-page="home"></div>
    
    <div class="container">
        <header>
            <h1>🚀 Svelte Hybrid Test</h1>
            <p>Testing Svelte 5 components with PHP backend</p>
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
                <button class="button button--primary" onclick="showSuccess()">
                    ✓ Success Toast
                </button>
                <button class="button button--secondary" onclick="showError()">
                    ✕ Error Toast
                </button>
                <button class="button button--outline" onclick="showInfo()">
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
                <div class="php-fallback" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                    <?php foreach ($articles as $article): ?>
                        <article class="card card--hover card--accent">
                            <div class="card__content">
                                <header class="card__header">
                                    <h3 class="card__title">
                                        <a href="#"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                    </h3>
                                </header>
                                <p class="card__excerpt"><?= htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8') ?></p>
                                <footer class="card__footer">
                                    <a href="#" class="card__link">Read more</a>
                                </footer>
                            </div>
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
