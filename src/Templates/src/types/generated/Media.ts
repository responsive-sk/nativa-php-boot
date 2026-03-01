/**
 * Media
 * Auto-generated from PHP class: Domain\Model\Media
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Media {
  id: string;
  userId?: string | null;
  filename: string;
  originalName: string;
  mimeType: string;
  size: number;
  path: string;
  url: string;
  provider: string;
  hash?: string | null;
  createdAt: string;
}

export function isMedia(data: unknown): data is Media {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Media).id === 'string' &&
    typeof (data as Media).userId === 'string' &&
    typeof (data as Media).filename === 'string' &&
    typeof (data as Media).originalName === 'string' &&
    typeof (data as Media).mimeType === 'string' &&
    typeof (data as Media).size === 'number' &&
    typeof (data as Media).path === 'string' &&
    typeof (data as Media).url === 'string' &&
    typeof (data as Media).provider === 'string' &&
    typeof (data as Media).hash === 'string' &&
    typeof (data as Media).createdAt === 'string'
  );
}
