/**
 * ArticleStatus
 * Auto-generated from PHP class: Domain\ValueObjects\ArticleStatus
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface ArticleStatus {
  value: string;
}

export function isArticleStatus(data: unknown): data is ArticleStatus {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as ArticleStatus).value === 'string'
  );
}
