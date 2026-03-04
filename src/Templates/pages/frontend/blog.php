<?php declare(strict_types = 1);

/**
 * Blog Listing Template - CMS Integration.
 *
 * @var array  $articles Array of Article entities
 * @var int    $currentPage Current page number
 * @var int    $totalPages Total number of pages
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

// Cloudinary hero images
$blogHeroImageMobile = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_768/v1658528025/cld-sample-2.jpg';
$blogHeroImageDesktop = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_1280/v1658528025/cld-sample-2.jpg';

$articleList = $articles ?? [];
$currentPage ??= 1;
$totalPages ??= 1;

?>

<!-- Hero Section (Portfolio style) -->
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <picture class="blog-hero__picture">
        <source media="(min-width: 769px)" srcset="<?php echo $blogHeroImageDesktop; ?>">
        <img src="<?php echo $blogHeroImageMobile; ?>" alt="Blog background" fetchpriority="high" loading="eager" decoding="async" class="blog-hero__image" width="1280" height="720" crossorigin="anonymous">
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
                    value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
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
    <div id="blog-results">
        <!-- Articles loaded via JavaScript -->
    </div>

    <!-- Pagination -->
    <nav class="pagination" id="blog-pagination" style="display: none;">
        <a href="#" class="pagination__link pagination__link--prev" id="blog-prev">← Previous</a>
        <span class="pagination__info" id="blog-page-info">Page 1 of 1</span>
        <a href="#" class="pagination__link pagination__link--next" id="blog-next">Next →</a>
    </nav>
</section>

<!-- Blog JavaScript -->
<script type="module">
// Blog state
const state = {
  page: <?php echo (int) $currentPage; ?>,
  totalPages: <?php echo (int) $totalPages; ?>,
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
    container.innerHTML = `
      <div class="blog-grid">
        ${data.articles.map(article => `
          <article class="blog-card" data-article-id="${article.id}">
            <div class="blog-card__image-wrapper">
              <img
                src="${article.image || 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_800/v1658528025/cld-sample-2.jpg'}"
                alt="${escapeHtml(article.title)}"
                class="blog-card__image"
                loading="lazy"
                crossorigin="anonymous"
              >
            </div>
            <div class="blog-card__content">
              <h2 class="blog-card__title">
                <a href="/blog/${article.slug}">${escapeHtml(article.title)}</a>
              </h2>
              <div class="blog-card__meta">
                <span class="blog-card__author">
                  <svg class="blog-card__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  ${article.author_id ? article.author_id.substring(0, 8) + '...' : 'Unknown'}
                </span>
                ${article.published_at ? `
                  <span class="blog-card__date">
                    <svg class="blog-card__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                      <line x1="16" y1="2" x2="16" y2="6"/>
                      <line x1="8" y1="2" x2="8" y2="6"/>
                      <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    ${new Date(article.published_at).toLocaleDateString('sk-SK')}
                  </span>
                ` : ''}
                <span class="blog-card__views">
                  <svg class="blog-card__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  ${article.views || 0}
                </span>
              </div>
              ${article.excerpt ? `<p class="blog-card__excerpt">${escapeHtml(article.excerpt)}</p>` : ''}
              <a href="/blog/${article.slug}" class="blog-card__link">Read more →</a>
            </div>
          </article>
        `).join('')}
      </div>
    `;

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
  const prevLink = document.getElementById('blog-prev');
  const nextLink = document.getElementById('blog-next');
  const pageInfo = document.getElementById('blog-page-info');

  if (prevLink) {
    if (state.page > 1) {
      prevLink.style.pointerEvents = 'auto';
      prevLink.style.opacity = '1';
    } else {
      prevLink.style.pointerEvents = 'none';
      prevLink.style.opacity = '0.5';
    }
  }

  if (nextLink) {
    if (state.page < state.totalPages) {
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
