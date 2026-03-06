<?php
/**
 * Svelte Hybrid Test Page
 * 
 * Access at: http://localhost:8000/svelte-test
 */

use Infrastructure\View\AssetHelper;

$articles = [
    [
        'id' => '1',
        'title' => 'Getting Started with Svelte',
        'excerpt' => 'Learn how to integrate Svelte with PHP in a hybrid approach.',
        'slug' => 'getting-started-svelte',
        'publishedAt' => '2026-03-06',
        'tags' => ['svelte', 'php', 'hybrid'],
    ],
    [
        'id' => '2',
        'title' => 'Modern Web Development',
        'excerpt' => 'Combining the best of both worlds: PHP backend + Svelte frontend.',
        'slug' => 'modern-web-dev',
        'publishedAt' => '2026-03-05',
        'tags' => ['web', 'development'],
    ],
    [
        'id' => '3',
        'title' => 'Progressive Enhancement',
        'excerpt' => 'Why SEO matters and how to keep it while using modern frameworks.',
        'slug' => 'progressive-enhancement',
        'publishedAt' => '2026-03-04',
        'tags' => ['seo', 'performance'],
    ],
];

$articleListJs = AssetHelper::js('article-list');
$themeToggleJs = AssetHelper::js('theme-toggle');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svelte Hybrid Test</title>
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f5f5f5;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --accent: #007bff;
        }
        
        .dark {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 2rem;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🚀 Svelte Hybrid Test</h1>
            
            <!-- Svelte Theme Toggle -->
            <div id="theme-toggle-container"></div>
        </header>
        
        <!-- Test 1: Article List -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 1: ArticleList Component</h2>
                <p><strong>Features:</strong> Live search, filtering, animations</p>
                <p><strong>How to test:</strong> Type in the search box below</p>
            </div>
            
            <div 
                id="article-list-container"
                data-articles='<?= htmlspecialchars(json_encode($articles), ENT_QUOTES, 'UTF-8') ?>'
            >
                <!-- PHP rendered fallback -->
                <div class="php-fallback">
                    <?php foreach ($articles as $article): ?>
                        <article style="padding: 1.5rem; background: var(--bg-secondary); margin-bottom: 1rem; border-radius: 8px;">
                            <h3><?= htmlspecialchars($article['title']) ?></h3>
                            <p><?= htmlspecialchars($article['excerpt']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
        <!-- Test 2: Contact Form -->
        <section class="test-section">
            <div class="test-info">
                <h2>Test 2: ContactForm Component</h2>
                <p><strong>Features:</strong> Validation, AJAX submit, success/error states</p>
                <p><strong>How to test:</strong> Fill the form and submit (won't actually send)</p>
            </div>
            
            <div id="contact-form-container"></div>
        </section>
    </div>
    
    <!-- Load Svelte components -->
    <script type="module">
        import ArticleList from '<?= $articleListJs ?>';
        import ThemeToggle from '<?= $themeToggleJs ?>';
        
        // Initialize Theme Toggle
        new ThemeToggle({
            target: document.getElementById('theme-toggle-container')
        });
        
        // Initialize Article List
        const articleContainer = document.getElementById('article-list-container');
        const articles = JSON.parse(articleContainer.dataset.articles);
        
        const articleList = new ArticleList({
            target: articleContainer,
            props: {
                articles: articles,
                searchQuery: ''
            }
        });
        
        console.log('✅ Svelte components loaded successfully!');
        console.log('📄 Articles:', articles.length);
    </script>
</body>
</html>
