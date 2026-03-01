/**
 * Blog Page Initialization
 * 
 * Handles blog listing page functionality including:
 * - Article card rendering
 * - Search with HTMX
 * - Pagination
 * - Loading states
 */

import { api } from '@core/http';
import { handleError } from '@core/error-handler';
import { ArticleCardList } from '@components/ArticleCard';
import { Article, isArticle } from '@types/generated';

interface BlogPageState {
  articles: Article[];
  loading: boolean;
  page: number;
  totalPages: number;
  searchQuery: string;
}

/**
 * Initialize blog page
 */
export async function initBlogPage(): Promise<BlogPageState> {
  const state: BlogPageState = {
    articles: [],
    loading: false,
    page: 1,
    totalPages: 1,
    searchQuery: '',
  };

  // Load initial articles
  await loadArticles(state);

  // Setup HTMX events for search
  setupHtmxEvents();

  return state;
}

/**
 * Load articles from API
 */
async function loadArticles(state: BlogPageState): Promise<void> {
  state.loading = true;
  updateLoadingState(true);

  try {
    const params = new URLSearchParams({
      page: state.page.toString(),
      limit: '10',
      ...(state.searchQuery ? { q: state.searchQuery } : {}),
    });

    const response = await api.get<{
      articles: unknown[];
      page: number;
      totalPages: number;
    }>(`/api/articles?${params}`);

    state.articles = response.articles.filter(isArticle);
    state.page = response.page;
    state.totalPages = response.totalPages;

    renderArticles(state.articles);
  } catch (error) {
    handleError(error);
    state.articles = [];
    renderArticles([]);
  } finally {
    state.loading = false;
    updateLoadingState(false);
  }
}

/**
 * Render articles to DOM
 */
function renderArticles(articles: Article[]): void {
  const container = document.getElementById('blog-results');
  if (!container) return;

  container.innerHTML = ArticleCardList({
    articles,
    showExcerpt: true,
    showStatus: false,
    showViews: true,
  });
}

/**
 * Update loading state UI
 */
function updateLoadingState(loading: boolean): void {
  const container = document.querySelector('.blog-grid');
  if (!container) return;

  if (loading) {
    container.classList.add('loading');
  } else {
    container.classList.remove('loading');
  }
}

/**
 * Setup HTMX event listeners
 */
function setupHtmxEvents(): void {
  // Search completed
  document.body.addEventListener('htmx:afterSwap', (event: any) => {
    if (event.detail.target.id === 'blog-results') {
      // Search results loaded
      console.log('Search results updated');
    }
  });

  // Error handling
  document.body.addEventListener('htmx:responseError', (event: any) => {
    handleError(new Error(`HTTP ${event.detail.xhr.status}: ${event.detail.xhr.statusText}`));
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBlogPage);
} else {
  initBlogPage();
}
