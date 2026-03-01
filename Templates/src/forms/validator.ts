/**
 * Form Validation Helpers
 * 
 * Simple, composable validation functions for forms.
 * 
 * @example
 * ```typescript
 * import { validate, required, email, minLength } from '@forms/validator';
 * 
 * const errors = validate(formData, {
 *   name: required,
 *   email: email,
 *   message: minLength(10),
 * });
 * ```
 */

/**
 * Validation function type
 */
export type Validator = (value: unknown) => string | null;

/**
 * Validate object against rules
 */
export function validate<T extends Record<string, unknown>>(
  data: T,
  rules: Partial<Record<keyof T, Validator>>
): Partial<Record<keyof T, string>> {
  const errors: Partial<Record<keyof T, string>> = {};

  for (const [key, rule] of Object.entries(rules)) {
    if (rule) {
      const error = rule(data[key as keyof T]);
      if (error) {
        errors[key as keyof T] = error;
      }
    }
  }

  return errors;
}

/**
 * Check if value is not empty
 */
export function required(value: unknown): string | null {
  if (typeof value === 'string') {
    return value.trim() ? null : 'This field is required';
  }
  if (value === null || value === undefined) {
    return 'This field is required';
  }
  return null;
}

/**
 * Validate email format
 */
export function email(value: unknown): string | null {
  if (typeof value !== 'string') {
    return 'Invalid email format';
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(value)) {
    return 'Please enter a valid email address';
  }
  
  return null;
}

/**
 * Validate minimum length
 */
export function minLength(length: number): Validator {
  return (value: unknown): string | null => {
    if (typeof value !== 'string') {
      return 'Invalid value';
    }
    if (value.length < length) {
      return `Must be at least ${length} characters`;
    }
    return null;
  };
}

/**
 * Validate maximum length
 */
export function maxLength(length: number): Validator {
  return (value: unknown): string | null => {
    if (typeof value !== 'string') {
      return 'Invalid value';
    }
    if (value.length > length) {
      return `Must be at most ${length} characters`;
    }
    return null;
  };
}

/**
 * Validate minimum value (for numbers)
 */
export function minValue(min: number): Validator {
  return (value: unknown): string | null => {
    if (typeof value !== 'number') {
      return 'Must be a number';
    }
    if (value < min) {
      return `Must be at least ${min}`;
    }
    return null;
  };
}

/**
 * Validate maximum value (for numbers)
 */
export function maxValue(max: number): Validator {
  return (value: unknown): string | null => {
    if (typeof value !== 'number') {
      return 'Must be a number';
    }
    if (value > max) {
      return `Must be at most ${max}`;
    }
    return null;
  };
}

/**
 * Validate with regex pattern
 */
export function pattern(regex: RegExp, message: string): Validator {
  return (value: unknown): string | null => {
    if (typeof value !== 'string') {
      return 'Invalid format';
    }
    if (!regex.test(value)) {
      return message;
    }
    return null;
  };
}

/**
 * Validate against multiple rules (all must pass)
 */
export function all(...validators: Validator[]): Validator {
  return (value: unknown): string | null => {
    for (const validator of validators) {
      const error = validator(value);
      if (error) {
        return error;
      }
    }
    return null;
  };
}

/**
 * Validate against multiple rules (first error wins)
 */
export function any(...validators: Validator[]): Validator {
  return (value: unknown): string | null => {
    const errors: string[] = [];
    
    for (const validator of validators) {
      const error = validator(value);
      if (!error) {
        return null;
      }
      errors.push(error);
    }
    
    return errors[0] ?? null;
  };
}

/**
 * Conditional validation
 */
export function when(
  condition: () => boolean,
  validator: Validator
): Validator {
  return (value: unknown): string | null => {
    if (!condition()) {
      return null;
    }
    return validator(value);
  };
}

/**
 * Transform value before validation
 */
export function transform<T>(
  transformFn: (value: unknown) => T,
  validator: Validator
): Validator {
  return (value: unknown): string | null => {
    const transformed = transformFn(value);
    return validator(transformed);
  };
}

/**
 * Compose validators with custom error messages
 */
export function withMessage(
  validator: Validator,
  message: string
): Validator {
  return (value: unknown): string | null => {
    const error = validator(value);
    return error ? message : null;
  };
}
