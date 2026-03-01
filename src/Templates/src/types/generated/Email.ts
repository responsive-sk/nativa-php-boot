/**
 * Email
 * Auto-generated from PHP class: Domain\ValueObjects\Email
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface Email {
  value: string;
}

export function isEmail(data: unknown): data is Email {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as Email).value === 'string'
  );
}
