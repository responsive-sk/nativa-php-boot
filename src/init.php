<?php

declare(strict_types=1);

/**
 * Application Initialization
 * 
 * Zero-dependency bootstrap for production
 * No vendor/ required!
 */

// ============================================================================
// 1. SIMPLE PSR-4 AUTOLOADER
// ============================================================================

spl_autoload_register(function (string $class): void {
    // Our namespace prefixes (support both old and new casing)
    $map = [
        'Domain\\' => [
            __DIR__ . '/Domain/',
            __DIR__ . '/domain/',
        ],
        'Application\\' => [
            __DIR__ . '/Application/',
            __DIR__ . '/application/',
        ],
        'Infrastructure\\' => [
            __DIR__ . '/Infrastructure/',
            __DIR__ . '/infrastructure/',
        ],
        'Interfaces\\' => [
            __DIR__ . '/Interfaces/',
            __DIR__ . '/interfaces/',
        ],
    ];
    
    foreach ($map as $prefix => $baseDirs) {
        // Check if class starts with prefix
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        // Get relative class name
        $relativeClass = substr($class, $len);
        
        // Replace namespace separators with directory separators
        $relativeFile = str_replace('\\', '/', $relativeClass);
        
        // Try each base directory
        foreach ($baseDirs as $baseDir) {
            $file = $baseDir . $relativeFile . '.php';
            
            // Load file if exists
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// ============================================================================
// 2. LOAD ENVIRONMENT
// ============================================================================

// Infrastructure\Env should be auto-loaded by now
if (class_exists('Infrastructure\Env')) {
    Infrastructure\Env::load(__DIR__ . '/..');
}

// ============================================================================
// 3. ERROR HANDLING
// ============================================================================

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ============================================================================
// 4. SESSION SETUP
// ============================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// 5. TIMEZONE
// ============================================================================

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Bratislava');
