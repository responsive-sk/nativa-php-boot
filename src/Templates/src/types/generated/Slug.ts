/**
 * Slug
 * Auto-generated from PHP class: Domain\ValueObjects\Slug
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Slug {
  value: string;
}

export function isSlug(data: unknown): data is Slug {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Slug).value === 'string'
  );
}
