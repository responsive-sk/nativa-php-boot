/**
 * User
 * Auto-generated from PHP class: Domain\Model\User
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */
export interface User {
  id: string;
  name: string;
  email: string;
  password: Password;
  role: Role;
  avatar?: string | null;
  isActive: boolean;
  lastLoginAt?: string | null;
  lastLoginIp?: string | null;
  createdAt: string;
  updatedAt: string;
  assignedRoles: unknown[];
  permissions: unknown[];
  permissionCache: unknown[];
}

export function isUser(data: unknown): data is User {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof (data as User).id === 'string' &&
    typeof (data as User).name === 'string' &&
    typeof (data as User).email === 'object' &&
    typeof (data as User).password === 'object' &&
    typeof (data as User).role === 'object' &&
    typeof (data as User).avatar === 'string' &&
    typeof (data as User).isActive === 'boolean' &&
    typeof (data as User).lastLoginAt === 'string' &&
    typeof (data as User).lastLoginIp === 'string' &&
    typeof (data as User).createdAt === 'string' &&
    typeof (data as User).updatedAt === 'string' &&
    typeof (data as User).assignedRoles === 'object' &&
    typeof (data as User).permissions === 'object' &&
    typeof (data as User).permissionCache === 'object'
  );
}
