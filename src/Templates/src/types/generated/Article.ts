/**
 * Article
 * Auto-generated from PHP class: Domain\Model\Article
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Article {
  id: string;
  authorId: string;
  categoryId?: string | null;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  image?: string | null;
  status: 'draft' | 'published' | 'archived';
  views: number;
  publishedAt?: string | null;
  createdAt: string;
  updatedAt: string;
  tags: string[];
}

export function isArticle(data: unknown): data is Article {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Article).id === 'string' &&
    typeof (data as Article).authorId === 'string' &&
    typeof (data as Article).categoryId === 'string' &&
    typeof (data as Article).title === 'string' &&
    typeof (data as Article).slug === 'object' &&
    typeof (data as Article).excerpt === 'string' &&
    typeof (data as Article).content === 'string' &&
    typeof (data as Article).image === 'string' &&
    typeof (data as Article).status === 'object' &&
    typeof (data as Article).views === 'number' &&
    typeof (data as Article).publishedAt === 'string' &&
    typeof (data as Article).createdAt === 'string' &&
    typeof (data as Article).updatedAt === 'string' &&
    typeof (data as Article).tags === 'object'
  );
}
