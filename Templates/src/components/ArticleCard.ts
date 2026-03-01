/**
 * ArticleCard Component
 * 
 * Displays a single article card with title, excerpt, metadata and status badge.
 * 
 * @example
 * ```typescript
 * import { ArticleCard } from '@components/ArticleCard';
 * 
 * const html = ArticleCard({ article, showExcerpt: true, showStatus: true });
 * ```
 */

import { Article, ArticleStatus } from '@types/generated';

export interface ArticleCardProps {
  /** Article data from API */
  article: Article;
  /** Show article excerpt if available */
  showExcerpt?: boolean;
  /** Show status badge */
  showStatus?: boolean;
  /** Show view count */
  showViews?: boolean;
  /** Custom CSS class */
  className?: string;
}

/**
 * Get status badge color based on article status
 */
function getStatusColor(status: ArticleStatus): string {
  switch (status) {
    case 'draft':
      return 'gray';
    case 'published':
      return 'green';
    case 'archived':
      return 'orange';
    default:
      return 'gray';
  }
}

/**
 * Format date string to human-readable format
 */
function formatDate(dateString: string | null): string {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
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
 * Render ArticleCard component to HTML string
 */
export function ArticleCard({
  article,
  showExcerpt = true,
  showStatus = false,
  showViews = false,
  className = '',
}: ArticleCardProps): string {
  const statusColor = getStatusColor(article.status);
  const publishedDate = formatDate(article.publishedAt);
  const excerpt = article.excerpt || truncate(article.content, 150);

  return `
    <article class="blog-card ${className}" data-article-id="${article.id}">
      <div class="blog-card__content">
        ${showStatus ? `
          <span class="status-badge status-badge--${statusColor}">
            ${article.status}
          </span>
        ` : ''}
        
        <h2 class="blog-card__title">
          <a href="/blog/${escapeHtml(article.slug)}">
            ${escapeHtml(article.title)}
          </a>
        </h2>
        
        <div class="blog-card__meta">
          <span class="blog-card__author">
            <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Author: ${escapeHtml(article.authorId.substring(0, 8))}...
          </span>
          
          ${publishedDate ? `
            <span class="blog-card__date">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              ${publishedDate}
            </span>
          ` : ''}
          
          ${showViews ? `
            <span class="blog-card__views">
              <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              ${article.views} ${article.views === 1 ? 'view' : 'views'}
            </span>
          ` : ''}
        </div>
        
        ${showExcerpt && excerpt ? `
          <p class="blog-card__excerpt">
            ${escapeHtml(excerpt)}
          </p>
        ` : ''}
        
        <a href="/blog/${escapeHtml(article.slug)}" class="blog-card__link">
          Read more →
        </a>
      </div>
    </article>
  `;
}

/**
 * Truncate text to specified length
 */
function truncate(text: string, maxLength: number): string {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength).trim() + '...';
}

/**
 * Render multiple article cards
 */
export function ArticleCardList({
  articles,
  showExcerpt = true,
  showStatus = false,
  showViews = false,
}: {
  articles: Article[];
  showExcerpt?: boolean;
  showStatus?: boolean;
  showViews?: boolean;
}): string {
  if (articles.length === 0) {
    return `
      <div class="blog-empty">
        <h2>No Articles Yet</h2>
        <p>Check back soon for new content!</p>
      </div>
    `;
  }

  return `
    <div class="blog-grid">
      ${articles.map(article => 
        ArticleCard({ 
          article, 
          showExcerpt, 
          showStatus, 
          showViews 
        })
      ).join('')}
    </div>
  `;
}
