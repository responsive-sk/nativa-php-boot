/**
 * Contact
 * Auto-generated from PHP class: Domain\Model\Contact
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Contact {
  id: string;
  name: string;
  email: string;
  subject?: string | null;
  message: string;
  status: string;
  createdAt: string;
}

export function isContact(data: unknown): data is Contact {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Contact).id === 'string' &&
    typeof (data as Contact).name === 'string' &&
    typeof (data as Contact).email === 'string' &&
    typeof (data as Contact).subject === 'string' &&
    typeof (data as Contact).message === 'string' &&
    typeof (data as Contact).status === 'string' &&
    typeof (data as Contact).createdAt === 'string'
  );
}
