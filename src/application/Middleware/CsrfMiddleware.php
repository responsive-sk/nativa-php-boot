<?php

declare(strict_types=1);

namespace Application\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CSRF Protection Middleware
 *
 * Protects against Cross-Site Request Forgery attacks by validating
 * tokens on all state-changing requests (POST, PUT, DELETE, PATCH).
 *
 * Usage:
 * 1. Add middleware to Kernel
 * 2. Include token in forms: <input type="hidden" name="_token" value="<?= CsrfMiddleware::token() ?>">
 * 3. Token is automatically validated on POST/PUT/DELETE/PATCH requests
 */
class CsrfMiddleware
{
    private const TOKEN_KEY = 'csrf_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Handle request - validate CSRF token for state-changing methods
     */
    public function handle(Request $request, callable $next): Response
    {
        // Only validate state-changing requests
        if ($this->isStateChangingMethod($request->getMethod())) {
            $this->validateToken($request);
        }

        return $next($request);
    }

    /**
     * Generate and/or get CSRF token for current session
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Regenerate CSRF token (use after login or sensitive operations)
     */
    public static function regenerateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Get CSRF token input field HTML
     */
    public static function tokenField(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate CSRF token from request
     *
     * @throws CsrfException
     */
    public function validateToken(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $request->request->get('_token')
            ?? $request->headers->get('X-CSRF-Token')
            ?? $request->query->get('_token');

        if (empty($token)) {
            throw new CsrfException('Missing CSRF token', 403);
        }

        if (empty($_SESSION[self::TOKEN_KEY])) {
            throw new CsrfException('No CSRF token in session', 403);
        }

        if (!hash_equals($_SESSION[self::TOKEN_KEY], $token)) {
            throw new CsrfException('Invalid CSRF token', 403);
        }
    }

    /**
     * Check if HTTP method is state-changing
     */
    private function isStateChangingMethod(string $method): bool
    {
        return in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'], true);
    }
}

/**
 * CSRF Validation Exception
 */
class CsrfException extends \Exception
{
    public function __construct(string $message = 'CSRF token validation failed', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
