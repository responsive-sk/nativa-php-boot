<?php
declare(strict_types=1);

/**
 * Application entry point
 * 
 * Features:
 * - Zero-dependency bootstrap
 * - Gzip/Brotli compression (via .htaccess)
 * - HTML minification (production only)
 * - Security headers (via .htaccess)
 */

// ============================================================================
// HTML MINIFICATION (Production Only)
// Compresses HTML output to reduce bandwidth
// ============================================================================
if (($_ENV['APP_DEBUG'] ?? 'false') !== 'true') {
    ob_start(function ($buffer) {
        $originalBuffer = $buffer;
        
        // Remove leading/trailing whitespace
        $buffer = trim($buffer);

        // Remove HTML comments (except conditional comments for IE)
        $buffer = preg_replace('/<!--[^<!].*?-->/s', '', $buffer);
        if ($buffer === null && preg_last_error() !== PREG_NO_ERROR) {
            error_log('HTML minification failed (comment removal): ' . preg_last_error_msg());
            return $originalBuffer;
        }

        // Preserve pre/code/textarea content
        $preserve = [];
        $buffer = preg_replace_callback(
            '/<(pre|code|textarea)\b[^>]*>.*?<\/\1>/is',
            function($matches) use (&$preserve) {
                $key = '{{PRESERVE_' . count($preserve) . '}}';
                $preserve[] = $matches[0];
                return $key;
            },
            $buffer
        );
        if ($buffer === null && preg_last_error() !== PREG_NO_ERROR) {
            error_log('HTML minification failed (preserve): ' . preg_last_error_msg());
            return $originalBuffer;
        }

        // Remove extra whitespace
        $buffer = preg_replace('/\s+/', ' ', $buffer);
        if ($buffer === null && preg_last_error() !== PREG_NO_ERROR) {
            error_log('HTML minification failed (whitespace): ' . preg_last_error_msg());
            return $originalBuffer;
        }

        // Remove whitespace around tags
        $buffer = preg_replace('/>\s+</', '><', $buffer);
        if ($buffer === null && preg_last_error() !== PREG_NO_ERROR) {
            error_log('HTML minification failed (tags): ' . preg_last_error_msg());
            return $originalBuffer;
        }

        // Restore preserved content
        foreach ($preserve as $i => $content) {
            $buffer = str_replace('{{PRESERVE_' . $i . '}}', $content, $buffer);
        }

        return $buffer;
    });
}

// Invalidate OPcache for development - ensures changes are picked up immediately
// Skip in production to avoid performance impact
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    if (function_exists('opcache_invalidate')) {
        // Check if OPcache functions are not disabled (shared hosting)
        $disabled = explode(',', ini_get('disable_functions'));
        if (!in_array('opcache_invalidate', $disabled, true)) {
            opcache_invalidate(__FILE__, true);
        }
    }
}
// Never call opcache_reset() in production - destroys all cached files

// Codeception C3 Code Coverage
if (file_exists(__DIR__ . '/../c3.php')) {
    include __DIR__ . '/../c3.php';
}

// Initialize application (zero-dependency bootstrap)
require_once __DIR__ . '/../src/init.php';

use Interfaces\HTTP\Kernel;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

// Error reporting
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Handle storage files (uploaded media)
$request = Request::createFromGlobals();
$pathInfo = $request->getPathInfo();

if (str_starts_with($pathInfo, '/storage/')) {
    $filePath = __DIR__ . '/../storage/uploads/' . substr($pathInfo, 9);
    
    if (file_exists($filePath) && is_file($filePath)) {
        $response = new BinaryFileResponse($filePath);
        $response->headers->set('Content-Type', mime_content_type($filePath) ?: 'application/octet-stream');
        $response->send();
        exit;
    }
    
    // File not found
    http_response_code(404);
    echo 'File not found';
    exit;
}

// Create kernel and handle request
$kernel = new Kernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
