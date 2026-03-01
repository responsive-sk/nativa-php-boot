<?php declare(strict_types=1);

/**
 * Blog Listing Template - CMS Integration
 *
 * @var array $articles Array of Article entities
 * @var int $currentPage Current page number
 * @var int $totalPages Total number of pages
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

// Cloudinary hero images
$blogHeroImageMobile = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_768/v1658528025/cld-sample-2.jpg';
$blogHeroImageDesktop = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_1280/v1658528025/cld-sample-2.jpg';

$articleList = $articles ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;

error_log("DEBUG: blog.php template rendering - page: {$currentPage}, total: {$totalPages}, articles: " . count($articleList));
?>

<!-- Hero Section (Portfolio style) -->
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <picture class="blog-hero__picture">
        <source media="(min-width: 769px)" srcset="<?= $blogHeroImageDesktop ?>" crossorigin="anonymous">
        <img src="<?= $blogHeroImageMobile ?>" alt="Blog background" fetchpriority="high" loading="eager" decoding="async" class="blog-hero__image" width="1280" height="720" crossorigin="anonymous">
    </picture>
    <div class="blog-hero__content">
        <h1>Our Blog</h1>
        <p>Latest insights and tutorials from Nativa CMS</p>
    </div>
</section>

<!-- Blog Content -->
<section class="blog">
    <div class="blog-search">
        <form class="blog-search__form"
              hx-get="/blog/search"
              hx-target="#blog-results"
              hx-trigger="keyup changed delay:300ms, submit"
              hx-indicator=".htmx-indicator">
            <div class="blog-search__input-group">
                <input
                    type="search"
                    name="q"
                    placeholder="Search articles..."
                    class="blog-search__input"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                >
                <button type="submit" class="blog-search__submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    Search
                </button>
            </div>
            <div class="htmx-indicator">
                <svg class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2" opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </form>
    </div>
    
    <!-- Search results will be loaded here via HTMX -->
    <div id="blog-results"></div>

    <?php if (empty($articleList)): ?>
    <div class="blog-empty">
        <h2>No Articles Yet</h2>
        <p>Check back soon for new content!</p>
        <a href="/admin/articles/create" class="btn btn--primary">Create First Article</a>
    </div>
    <?php else: ?>
    <div class="blog-grid">
        <?php foreach ($articleList as $article): ?>
        <article class="blog-card">
            <div class="blog-card__content">
                <h2 class="blog-card__title">
                    <a href="/blog/<?= $this->e($article->slug()) ?>">
                        <?= $this->e($article->title()) ?>
                    </a>
                </h2>
                <div class="blog-card__meta">
                    <span class="blog-card__author">
                        By <?= $this->e($article->authorId()) ?>
                    </span>
                    <span class="blog-card__date">
                        <?= $this->date($article->publishedAt()) ?>
                    </span>
                </div>
                <p class="blog-card__excerpt">
                    <?= $this->e($article->excerpt() ?: substr(strip_tags($article->content()), 0, 200) . '...') ?>
                </p>
                <a href="/blog/<?= $this->e($article->slug()) ?>" class="blog-card__link">
                    Read more →
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="pagination">
        <?php if ($currentPage > 1): ?>
        <a href="/blog?page=<?= $currentPage - 1 ?>" class="pagination__link pagination__link--prev">
            ← Previous
        </a>
        <?php endif; ?>

        <span class="pagination__info">
            Page <?= $currentPage ?> of <?= $totalPages ?>
        </span>

        <?php if ($currentPage < $totalPages): ?>
        <a href="/blog?page=<?= $currentPage + 1 ?>" class="pagination__link pagination__link--next">
            Next →
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</section>

<!-- Blog JavaScript -->
<script type="module">
// Blog state
const state = {
  page: <?= (int)$currentPage ?>,
  totalPages: <?= (int)$totalPages ?>,
  searchQuery: '',
};

// Simple blog initialization
(async function initBlog() {
  await loadArticles();
  setupSearch();
})();

/**
 * Load articles from API
 */
async function loadArticles() {
  try {
    const params = new URLSearchParams({
      page: state.page.toString(),
      limit: '10',
      ...(state.searchQuery ? { q: state.searchQuery } : {}),
    });
    
    const response = await fetch('/api/articles?' + params.toString());
    const data = await response.json();
    
    const container = document.getElementById('blog-results');
    if (!container) return;
    
    if (!data.articles || data.articles.length === 0) {
      container.innerHTML = '<div class="blog-empty"><h2>No Articles Found</h2><p>Try adjusting your search or check back later.</p></div>';
      return;
    }
    
    // Render articles
    container.innerHTML = data.articles.map(article => `
      <article class="blog-card" data-article-id="${article.id}">
        <div class="blog-card__content">
          <h2 class="blog-card__title">
            <a href="/blog/${article.slug}">${escapeHtml(article.title)}</a>
          </h2>
          <div class="blog-card__meta">
            <span class="blog-card__author">Author: ${article.author_id ? article.author_id.substring(0, 8) + '...' : 'Unknown'}</span>
            ${article.published_at ? `<span class="blog-card__date">${new Date(article.published_at).toLocaleDateString('sk-SK')}</span>` : ''}
            <span class="blog-card__views">${article.views || 0} views</span>
          </div>
          ${article.excerpt ? `<p class="blog-card__excerpt">${escapeHtml(article.excerpt)}</p>` : ''}
          <a href="/blog/${article.slug}" class="blog-card__link">Read more →</a>
        </div>
      </article>
    `).join('');
    
    // Update pagination
    if (data.totalPages) {
      state.totalPages = data.totalPages;
      updatePagination();
    }
  } catch (error) {
    console.error('Failed to load blog articles:', error);
    const container = document.getElementById('blog-results');
    if (container) {
      container.innerHTML = '<div class="blog-empty"><h2>Error Loading Articles</h2><p>Please try again later.</p></div>';
    }
  }
}

/**
 * Setup search functionality
 */
function setupSearch() {
  const searchForm = document.querySelector('.blog-search__form');
  const searchInput = document.querySelector('.blog-search__input');
  
  if (!searchForm || !searchInput) return;
  
  // Handle search submit
  searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    state.searchQuery = searchInput.value.trim();
    state.page = 1; // Reset to first page
    loadArticles();
  });
  
  // Handle input with debounce
  let debounceTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      state.searchQuery = searchInput.value.trim();
      state.page = 1;
      loadArticles();
    }, 500); // 500ms debounce
  });
}

/**
 * Update pagination links
 */
function updatePagination() {
  const prevLink = document.querySelector('.pagination__link--prev');
  const nextLink = document.querySelector('.pagination__link--next');
  const pageInfo = document.querySelector('.pagination__info');
  
  if (prevLink) {
    if (state.page > 1) {
      prevLink.href = '/blog?page=' + (state.page - 1) + (state.searchQuery ? '&q=' + encodeURIComponent(state.searchQuery) : '');
      prevLink.style.pointerEvents = 'auto';
      prevLink.style.opacity = '1';
    } else {
      prevLink.style.pointerEvents = 'none';
      prevLink.style.opacity = '0.5';
    }
  }
  
  if (nextLink) {
    if (state.page < state.totalPages) {
      nextLink.href = '/blog?page=' + (state.page + 1) + (state.searchQuery ? '&q=' + encodeURIComponent(state.searchQuery) : '');
      nextLink.style.pointerEvents = 'auto';
      nextLink.style.opacity = '1';
    } else {
      nextLink.style.pointerEvents = 'none';
      nextLink.style.opacity = '0.5';
    }
  }
  
  if (pageInfo) {
    pageInfo.textContent = 'Page ' + state.page + ' of ' + state.totalPages;
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}
</script>
