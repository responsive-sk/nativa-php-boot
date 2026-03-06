<?php
/**
 * Homepage - Hybrid PHP + Svelte
 * 
 * PHP renders initial HTML for SEO
 * Svelte enhances with interactivity
 */

use Infrastructure\View\AssetHelper;

$articles = $articles ?? [];
$pageTitle = $pageTitle ?? 'Nativa CMS';

// Get Svelte component URLs from manifest
$articleListJs = AssetHelper::js('article-list');
?>

<section class="articles-section">
    <header class="section-header">
        <h2>Latest Articles</h2>
        <p>Fresh insights and tutorials from our blog</p>
        
        <!-- Svelte will enhance this search input -->
        <div class="search-wrapper">
            <input 
                type="search" 
                id="article-search" 
                placeholder="Search articles..."
                class="article-search-input"
            >
        </div>
    </header>
    
    <!-- 
     HYBRID APPROACH:
     1. PHP renders initial articles for SEO and no-JS users
     2. Svelte mounts on this container and adds:
        - Live search
        - Filtering
        - Animations
        - Load more functionality
    -->
    <div 
        id="article-list-container"
        class="article-list-wrapper"
        data-articles='<?= htmlspecialchars(json_encode(array_map(fn($a) => [
            'id' => $a->id(),
            'title' => $a->title(),
            'excerpt' => $a->excerpt(),
            'slug' => $a->slug(),
            'publishedAt' => $a->publishedAt()?->format('Y-m-d') ?? '',
            'tags' => $a->tags() ?? [],
        ]), $articles), ENT_QUOTES, 'UTF-8') ?>'
    >
        <!-- PHP rendered content (SEO friendly) -->
        <div class="php-fallback">
            <?php if (empty($articles)): ?>
                <p class="no-articles">No articles yet. Check back soon!</p>
            <?php else: ?>
                <div class="article-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card">
                            <header>
                                <h3>
                                    <a href="/blog/<?= $article->slug() ?>">
                                        <?= $this->e($article->title()) ?>
                                    </a>
                                </h3>
                                <?php if ($article->publishedAt()): ?>
                                    <time datetime="<?= $article->publishedAt()->format('Y-m-d') ?>">
                                        <?= $article->publishedAt()->format('M d, Y') ?>
                                    </time>
                                <?php endif; ?>
                            </header>
                            
                            <p class="excerpt"><?= $this->e($article->excerpt()) ?></p>
                            
                            <footer>
                                <a href="/blog/<?= $article->slug() ?>" class="read-more">
                                    Read more →
                                </a>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Load Svelte component -->
    <?php if (!empty($articles)): ?>
    <script type="module">
        import ArticleList from '<?= $articleListJs ?>';
        
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('article-list-container');
            const articles = JSON.parse(container.dataset.articles);
            
            // Initialize Svelte component
            const articleList = new ArticleList({
                target: container,  // Mount on existing container
                props: {
                    articles: articles,
                    searchQuery: ''
                }
            });
            
            // Connect search input to Svelte store
            const searchInput = document.getElementById('article-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    articleList.$set({ searchQuery: e.target.value });
                });
            }
        });
    </script>
    <?php endif; ?>
</section>

<style>
    .articles-section {
        padding: 4rem 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .search-wrapper {
        max-width: 400px;
        margin: 2rem auto 0;
    }
    
    .article-search-input {
        width: 100%;
        padding: 0.75rem 1.5rem;
        border: 2px solid #e0e0e0;
        border-radius: 9999px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    
    .article-search-input:focus {
        outline: none;
        border-color: #007bff;
    }
    
    .article-list-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .php-fallback {
        display: grid;
        gap: 2rem;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
    
    .article-card {
        padding: 2rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .article-card h3 {
        margin: 0 0 0.5rem 0;
    }
    
    .article-card h3 a {
        color: #1a1a1a;
        text-decoration: none;
    }
    
    .article-card h3 a:hover {
        color: #007bff;
    }
    
    .excerpt {
        color: #4a4a4a;
        line-height: 1.6;
        margin: 1rem 0;
    }
    
    .read-more {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }
    
    .no-articles {
        text-align: center;
        padding: 3rem;
        color: #666;
    }
</style>
