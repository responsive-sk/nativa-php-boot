/**
 * HTTP Helper
 * 
 * Simple, type-safe HTTP client for API requests.
 * 
 * @example
 * ```typescript
 * import { api } from '@core/http';
 * import { Article } from '@types/generated';
 * 
 * const articles = await api.get<Article[]>('/api/articles');
 * ```
 */

export interface HttpError extends Error {
  status: number;
  statusText: string;
  response?: Response;
}

export interface RequestOptions extends RequestInit {
  timeout?: number;
}

/**
 * Create HTTP error with status information
 */
function createHttpError(response: Response): HttpError {
  const error = new Error(
    `HTTP ${response.status}: ${response.statusText}`
  ) as HttpError;
  error.status = response.status;
  error.statusText = response.statusText;
  error.response = response;
  return error;
}

/**
 * Make HTTP request with automatic JSON handling
 */
export async function http<T>(
  url: string,
  options: RequestOptions = {}
): Promise<T> {
  const { timeout = 30000, ...fetchOptions } = options;

  const defaultOptions: RequestInit = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  };

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  try {
    const response = await fetch(url, {
      ...defaultOptions,
      ...fetchOptions,
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw createHttpError(response);
    }

    // Handle empty responses
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      return {} as T;
    }

    return response.json();
  } catch (error) {
    clearTimeout(timeoutId);
    
    if (error instanceof Error && error.name === 'AbortError') {
      const timeoutError = new Error('Request timeout') as HttpError;
      timeoutError.status = 408;
      timeoutError.statusText = 'Request Timeout';
      throw timeoutError;
    }
    
    throw error;
  }
}

/**
 * API convenience methods
 */
export const api = {
  /**
   * GET request
   */
  get: <T>(url: string, options?: RequestOptions) => 
    http<T>(url, { ...options, method: 'GET' }),

  /**
   * POST request
   */
  post: <T>(url: string, data?: unknown, options?: RequestOptions) => 
    http<T>(url, { 
      ...options, 
      method: 'POST', 
      body: data ? JSON.stringify(data) : undefined,
    }),

  /**
   * PUT request
   */
  put: <T>(url: string, data?: unknown, options?: RequestOptions) => 
    http<T>(url, { 
      ...options, 
      method: 'PUT', 
      body: data ? JSON.stringify(data) : undefined,
    }),

  /**
   * PATCH request
   */
  patch: <T>(url: string, data?: unknown, options?: RequestOptions) => 
    http<T>(url, { 
      ...options, 
      method: 'PATCH', 
      body: data ? JSON.stringify(data) : undefined,
    }),

  /**
   * DELETE request
   */
  delete: <T>(url: string, options?: RequestOptions) => 
    http<T>(url, { ...options, method: 'DELETE' }),
};

/**
 * Check if error is HTTP error
 */
export function isHttpError(error: unknown): error is HttpError {
  return (
    error instanceof Error &&
    'status' in error &&
    typeof (error as HttpError).status === 'number'
  );
}
