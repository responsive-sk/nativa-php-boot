<?php

declare(strict_types=1);

namespace Application\Middleware;

use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Security Headers Middleware
 *
 * Adds security headers to all responses to protect against common web vulnerabilities:
 * - Content-Security-Policy (XSS, injection attacks)
 * - X-Frame-Options (clickjacking)
 * - X-Content-Type-Options (MIME sniffing)
 * - Strict-Transport-Security (protocol downgrade attacks)
 * - Referrer-Policy (information leakage)
 * - Permissions-Policy (browser features)
 */
class SecurityHeadersMiddleware
{
    /**
     * CSP directives for TailwindCSS + Alpine.js
     * - 'self' for same-origin resources
     * - 'unsafe-inline' for styles (Tailwind requirement)
     * - 'unsafe-eval' for TailwindCSS CDN (required for runtime compilation)
     * - data: for inline images
     * - https: for external fonts/CDNs
     */
    private const CSP_DIRECTIVES = [
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https:",  // TailwindCSS CDN + Alpine.js
        'style-src' => "'self' 'unsafe-inline'",   // TailwindCSS needs inline styles
        'img-src' => "'self' data: https:",
        'font-src' => "'self' https:",
        'connect-src' => "'self' https:",
        'frame-ancestors' => "'none'",
        'base-uri' => "'self'",
        'form-action' => "'self'",
    ];

    /**
     * Apply security headers to response
     */
    public function handle(Request $request, Response $response): Response
    {
        // Content-Security-Policy
        $response->setHeader(
            'Content-Security-Policy',
            $this->buildCspPolicy()
        );

        // X-Frame-Options - prevent clickjacking
        $response->setHeader('X-Frame-Options', 'DENY');

        // X-Content-Type-Options - prevent MIME sniffing
        $response->setHeader('X-Content-Type-Options', 'nosniff');

        // Strict-Transport-Security - force HTTPS (only in production)
        if ($this->isProduction()) {
            $response->setHeader(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Referrer-Policy - control referrer information
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - disable unnecessary browser features
        $response->setHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        // X-XSS-Protection - legacy but still useful for older browsers
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        // Cross-Origin policies
        $response->setHeader('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->setHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->setHeader('Cross-Origin-Resource-Policy', 'same-origin');

        return $response;
    }

    /**
     * Build CSP policy string from directives
     */
    private function buildCspPolicy(): string
    {
        $parts = [];
        foreach (self::CSP_DIRECTIVES as $directive => $value) {
            $parts[] = "{$directive} {$value}";
        }
        return implode('; ', $parts);
    }

    /**
     * Check if running in production environment
     */
    private function isProduction(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'development') === 'production'
            || ($_ENV['APP_DEBUG'] ?? 'true') === 'false';
    }
}
