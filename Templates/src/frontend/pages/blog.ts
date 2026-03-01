/**
 * Blog Page Entry Point
 * 
 * Auto-executes on page load to initialize blog functionality.
 * Import this file in your HTML to enable blog features.
 */

import { api } from '../core/http';
import { ArticleCardList } from '../components/ArticleCard';
import type { Article } from '../types/generated';

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
async function initBlogPage(): Promise<void> {
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

    state.articles = response.articles as Article[];
    state.page = response.page;
    state.totalPages = response.totalPages;

    renderArticles(state.articles);
  } catch (error) {
    console.error('Failed to load articles:', error);
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

  if (articles.length === 0) {
    container.innerHTML = `
      <div class="blog-empty">
        <h2>No Articles Yet</h2>
        <p>Check back soon for new content!</p>
      </div>
    `;
    return;
  }

  container.innerHTML = `
    <div class="blog-grid">
      ${articles.map(article => `
        <article class="blog-card" data-article-id="${article.id}">
          <div class="blog-card__content">
            <h2 class="blog-card__title">
              <a href="/blog/${article.slug}">${escapeHtml(article.title)}</a>
            </h2>
            <div class="blog-card__meta">
              <span class="blog-card__author">Author: ${article.authorId.substring(0, 8)}...</span>
              ${article.publishedAt ? `<span class="blog-card__date">${new Date(article.publishedAt).toLocaleDateString('sk-SK')}</span>` : ''}
              <span class="blog-card__views">${article.views} ${article.views === 1 ? 'view' : 'views'}</span>
            </div>
            ${article.excerpt ? `<p class="blog-card__excerpt">${escapeHtml(article.excerpt)}</p>` : ''}
            <a href="/blog/${article.slug}" class="blog-card__link">Read more →</a>
          </div>
        </article>
      `).join('')}
    </div>
  `;
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
 * Escape HTML to prevent XSS
 */
function escapeHtml(text: string): string {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

/**
 * Setup HTMX event listeners
 */
function setupHtmxEvents(): void {
  document.body.addEventListener('htmx:responseError', (event: any) => {
    console.error('HTMX error:', event.detail.xhr.status, event.detail.xhr.statusText);
  });
}

// Auto-execute on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => initBlogPage().catch(console.error));
} else {
  initBlogPage().catch(console.error);
}
