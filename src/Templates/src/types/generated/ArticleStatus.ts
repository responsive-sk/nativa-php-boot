/**
 * ArticleStatus
 * Auto-generated from PHP enum: Domain\ValueObjects\ArticleStatus
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export type ArticleStatus = 'draft' | 'published' | 'archived';

export function isArticleStatus(value: unknown): value is ArticleStatus {
  return typeof value === 'string' && ['draft', 'published', 'archived'].includes(value);
}
