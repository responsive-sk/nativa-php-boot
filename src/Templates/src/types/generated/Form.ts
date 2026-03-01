/**
 * Form
 * Auto-generated from PHP class: Domain\Model\Form
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Form {
  id: string;
  name: string;
  slug: string;
  schema: unknown[];
  emailNotification?: string | null;
  successMessage: string;
  createdAt: string;
  updatedAt: string;
}

export function isForm(data: unknown): data is Form {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Form).id === 'string' &&
    typeof (data as Form).name === 'string' &&
    typeof (data as Form).slug === 'string' &&
    typeof (data as Form).schema === 'object' &&
    typeof (data as Form).emailNotification === 'string' &&
    typeof (data as Form).successMessage === 'string' &&
    typeof (data as Form).createdAt === 'string' &&
    typeof (data as Form).updatedAt === 'string'
  );
}
