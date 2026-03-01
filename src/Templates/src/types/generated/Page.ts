/**
 * Page
 * Auto-generated from PHP class: Domain\Model\Page
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Page {
  id: string;
  title: string;
  slug: string;
  content: string;
  template: string;
  metaTitle?: string | null;
  metaDescription?: string | null;
  isPublished: boolean;
  createdAt: string;
  updatedAt: string;
}

export function isPage(data: unknown): data is Page {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Page).id === 'string' &&
    typeof (data as Page).title === 'string' &&
    typeof (data as Page).slug === 'object' &&
    typeof (data as Page).content === 'string' &&
    typeof (data as Page).template === 'string' &&
    typeof (data as Page).metaTitle === 'string' &&
    typeof (data as Page).metaDescription === 'string' &&
    typeof (data as Page).isPublished === 'boolean' &&
    typeof (data as Page).createdAt === 'string' &&
    typeof (data as Page).updatedAt === 'string'
  );
}
