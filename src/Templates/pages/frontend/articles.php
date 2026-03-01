<?php declare(strict_types=1);

/**
 * Articles Listing Template
 *
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

error_log("DEBUG: articles.php template rendering");
?>

<!-- Hero Section -->
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <picture class="blog-hero__picture">
        <source media="(min-width: 769px)" srcset="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_1280/v1658528025/cld-sample-2.jpg" crossorigin="anonymous">
        <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_768/v1658528025/cld-sample-2.jpg" alt="Articles background" fetchpriority="high" loading="eager" decoding="async" class="blog-hero__image" width="1280" height="720" crossorigin="anonymous">
    </picture>
    <div class="blog-hero__content">
        <h1>All Articles</h1>
        <p>Latest insights and tutorials from our development journey</p>
    </div>
</section>

<!-- Articles Section -->
<section class="blog">
    <div class="blog-search">
        <form class="blog-search__form" onsubmit="return false;">
            <div class="blog-search__input-group">
                <input
                    type="search"
                    id="articles-search"
                    placeholder="Search articles..."
                    class="blog-search__input"
                >
                <button type="submit" class="blog-search__submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>
    
    <div id="articles-results">
        <!-- Loading state -->
        <div class="loading">Loading articles...</div>
    </div>
    
    <!-- Pagination -->
    <nav class="pagination" id="articles-pagination" style="display: none;">
        <a href="#" class="pagination__link pagination__link--prev" id="articles-prev">← Previous</a>
        <span class="pagination__info" id="articles-page-info">Page 1 of 1</span>
        <a href="#" class="pagination__link pagination__link--next" id="articles-next">Next →</a>
    </nav>
</section>

<!-- Articles JavaScript -->
<script type="module">
// Articles state
const state = {
  page: 1,
  totalPages: 1,
  searchQuery: '',
};

// Initialize
(async function initArticles() {
  await loadArticles();
  setupSearch();
  setupPagination();
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
    
    const container = document.getElementById('articles-results');
    if (!container) return;
    
    if (!data.articles || data.articles.length === 0) {
      container.innerHTML = '<div class="blog-empty"><h2>No Articles Found</h2><p>Check back soon for new content!</p></div>';
      document.getElementById('articles-pagination').style.display = 'none';
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
      document.getElementById('articles-pagination').style.display = 'flex';
    }
  } catch (error) {
    console.error('Failed to load articles:', error);
    const container = document.getElementById('articles-results');
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
  const searchInput = document.getElementById('articles-search');
  
  if (!searchForm || !searchInput) return;
  
  // Handle search submit
  searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    state.searchQuery = searchInput.value.trim();
    state.page = 1;
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
    }, 500);
  });
}

/**
 * Setup pagination
 */
function setupPagination() {
  const prevLink = document.getElementById('articles-prev');
  const nextLink = document.getElementById('articles-next');
  
  if (prevLink) {
    prevLink.addEventListener('click', (e) => {
      e.preventDefault();
      if (state.page > 1) {
        state.page--;
        loadArticles();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }
  
  if (nextLink) {
    nextLink.addEventListener('click', (e) => {
      e.preventDefault();
      if (state.page < state.totalPages) {
        state.page++;
        loadArticles();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }
}

/**
 * Update pagination UI
 */
function updatePagination() {
  const prevLink = document.getElementById('articles-prev');
  const nextLink = document.getElementById('articles-next');
  const pageInfo = document.getElementById('articles-page-info');
  
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
