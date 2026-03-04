<?php

declare(strict_types=1);

namespace Application\Services;

/**
 * Session Security Configuration
 *
 * Configures PHP sessions with secure settings to prevent:
 * - Session hijacking
 * - Session fixation
 * - XSS-based session theft
 * - CSRF via cookies
 *
 * Usage: Call SessionSecurityConfig::configure() early in bootstrap
 */
final class SessionSecurityConfig
{
    /**
     * Configure secure session settings
     * Should be called before session_start()
     */
    public static function configure(): void
    {
        // Cookie security settings
        ini_set('session.cookie_httponly', '1');      // Prevent JavaScript access
        ini_set('session.cookie_secure', '0');        // Set to 1 in production (HTTPS only)
        ini_set('session.cookie_samesite', 'Strict'); // Prevent CSRF
        
        // Session fixation prevention
        ini_set('session.use_strict_mode', '1');      // Reject uninitialized session IDs
        
        // Session ID configuration
        ini_set('session.use_only_cookies', '1');     // Don't accept session IDs from URLs
        ini_set('session.use_trans_sid', '0');        // Disable transparent SID support
        
        // Session timeout settings
        ini_set('session.gc_maxlifetime', '1800');    // 30 minutes (server-side)
        ini_set('session.cookie_lifetime', '0');      // Session cookie (expires on browser close)
        
        // Entropy for session ID generation
        ini_set('session.entropy_length', '32');
        ini_set('session.entropy_file', '/dev/urandom');
        
        // Cache limiter - prevent caching of session pages
        ini_set('session.cache_limiter', 'nocache');
        
        // Hash function for session ID
        ini_set('session.hash_function', 'sha256');
    }
    
    /**
     * Configure for production environment (HTTPS required)
     */
    public static function configureForProduction(): void
    {
        self::configure();
        
        // Force HTTPS-only cookies
        ini_set('session.cookie_secure', '1');
        
        // Additional production hardening
        ini_set('session.cookie_samesite', 'Strict');
    }
    
    /**
     * Start session with security configuration
     */
    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            self::configure();
            session_start();
        }
    }
    
    /**
     * Regenerate session ID (call after login, privilege changes)
     *
     * @param bool $deleteOldSession Whether to delete old session data
     */
    public static function regenerateSessionId(bool $deleteOldSession = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
        }
    }
    
    /**
     * Set session timeout timestamp
     */
    public static function setLastActivity(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['_last_activity'] = time();
        }
    }
    
    /**
     * Check if session has timed out
     *
     * @param int $timeoutSeconds Timeout in seconds (default: 30 minutes)
     * @return bool True if session is still active
     */
    public static function isSessionActive(int $timeoutSeconds = 1800): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        
        $lastActivity = $_SESSION['_last_activity'] ?? 0;
        
        if ($lastActivity === 0) {
            // No last activity recorded, set it now
            self::setLastActivity();
            return true;
        }
        
        return (time() - $lastActivity) < $timeoutSeconds;
    }
    
    /**
     * Destroy session completely
     */
    public static function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear all session data
            $_SESSION = [];
            
            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            // Destroy session
            session_destroy();
        }
    }
}
