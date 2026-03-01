/**
 * Role
 * Auto-generated from PHP enum: Domain\ValueObjects\Role
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export type Role = 'admin' | 'editor' | 'viewer' | 'user';

export function isRole(value: unknown): value is Role {
  return typeof value === 'string' && ['admin', 'editor', 'viewer', 'user'].includes(value);
}
