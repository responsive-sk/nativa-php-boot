/**
 * PermissionName
 * Auto-generated from PHP class: Domain\ValueObjects\PermissionName
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface PermissionName {
  name: string;
  resource: string;
  action: string;
}

export function isPermissionName(data: unknown): data is PermissionName {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as PermissionName).name === 'string' &&
    typeof (data as PermissionName).resource === 'string' &&
    typeof (data as PermissionName).action === 'string'
  );
}
