/**
 * FormSubmission
 * Auto-generated from PHP class: Domain\Model\FormSubmission
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface FormSubmission {
  id: string;
  formId: string;
  data: unknown[];
  ipAddress: string;
  userAgent: string;
  submittedAt: string;
}

export function isFormSubmission(data: unknown): data is FormSubmission {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as FormSubmission).id === 'string' &&
    typeof (data as FormSubmission).formId === 'string' &&
    typeof (data as FormSubmission).data === 'object' &&
    typeof (data as FormSubmission).ipAddress === 'string' &&
    typeof (data as FormSubmission).userAgent === 'string' &&
    typeof (data as FormSubmission).submittedAt === 'string'
  );
}
