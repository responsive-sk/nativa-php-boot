<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Services\RateLimiter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate Limiting Middleware
 *
 * Protects against brute-force and DoS attacks by limiting request frequency.
 *
 * Usage in Kernel:
 *   $this->rateLimitMiddleware->handle($request, 'login', 5, 60); // 5 attempts per minute
 *
 * Pre-configured limits:
 *   - Login: 5 attempts per minute
 *   - Form submissions: 10 per hour
 *   - API endpoints: 100 per minute
 *   - General: 60 per minute
 */
class RateLimitMiddleware
{
    private RateLimiter $rateLimiter;

    public function __construct(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Apply rate limiting to login attempts
     * Returns error response if rate limited, null otherwise
     */
    public function limitLogin(Request $request): ?Response
    {
        $identifier = $this->getIdentifier($request);
        $key = 'login:' . $identifier;

        if (!$this->rateLimiter->isAllowed($key, 5, 60)) {
            return $this->createRateLimitResponse(
                'Too many login attempts. Please try again later.',
                $this->rateLimiter->getRetryAfter($key, 60)
            );
        }

        return null;
    }

    /**
     * Apply rate limiting to form submissions
     */
    public function limitFormSubmission(Request $request): ?Response
    {
        $identifier = $this->getIdentifier($request);
        $key = 'form:' . $identifier;

        if (!$this->rateLimiter->isAllowed($key, 10, 3600)) {
            return $this->createRateLimitResponse(
                'Too many form submissions. Please try again later.',
                $this->rateLimiter->getRetryAfter($key, 3600)
            );
        }

        return null;
    }

    /**
     * Apply rate limiting to API endpoints
     */
    public function limitApi(Request $request): ?Response
    {
        $identifier = $this->getIdentifier($request);
        $key = 'api:' . $identifier;

        if (!$this->rateLimiter->isAllowed($key, 100, 60)) {
            return $this->createRateLimitResponse(
                'API rate limit exceeded.',
                $this->rateLimiter->getRetryAfter($key, 60)
            );
        }

        return null;
    }

    /**
     * Apply general rate limiting
     */
    public function limitGeneral(Request $request): ?Response
    {
        $identifier = $this->getIdentifier($request);
        $key = 'general:' . $identifier;

        if (!$this->rateLimiter->isAllowed($key, 60, 60)) {
            return $this->createRateLimitResponse(
                'Rate limit exceeded. Please slow down.',
                $this->rateLimiter->getRetryAfter($key, 60)
            );
        }

        return null;
    }

    /**
     * Apply custom rate limit
     *
     * @param string $key Custom key identifier
     * @param int $maxRequests Maximum requests allowed
     * @param int $windowSeconds Time window in seconds
     */
    public function limitCustom(
        Request $request,
        string $key,
        int $maxRequests,
        int $windowSeconds
    ): ?Response {
        $identifier = $this->getIdentifier($request);
        $fullKey = $key . ':' . $identifier;

        if (!$this->rateLimiter->isAllowed($fullKey, $maxRequests, $windowSeconds)) {
            return $this->createRateLimitResponse(
                'Rate limit exceeded.',
                $this->rateLimiter->getRetryAfter($fullKey, $windowSeconds)
            );
        }

        return null;
    }

    /**
     * Get unique identifier for rate limiting
     * Uses IP address, with optional user ID if authenticated
     */
    private function getIdentifier(Request $request): string
    {
        // Use IP address as base identifier
        $ip = $request->getClientIp() ?? 'unknown';

        // Hash IP for privacy
        return hash('sha256', $ip);
    }

    /**
     * Create 429 Too Many Requests response
     */
    private function createRateLimitResponse(string $message, int $retryAfter): Response
    {
        $response = new Response($message, 429);
        $response->headers->set('Retry-After', (string) $retryAfter);
        $response->headers->set('X-RateLimit-Limit', 'See rate limit policy');
        $response->headers->set('X-RateLimit-Remaining', '0');

        return $response;
    }
}

/**
 * Rate Limit Exception
 */
class RateLimitException extends \Exception
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        int $retryAfter = 60
    ) {
        parent::__construct($message, 429);
        $this->retryAfter = $retryAfter;
    }

    public int $retryAfter;
}
