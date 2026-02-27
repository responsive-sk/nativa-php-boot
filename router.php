<?php
/**
 * PHP Built-in Server Router
 * 
 * Usage: php -S localhost:8000 router.php
 */

require __DIR__ . '/vendor/autoload.php';

use Infrastructure\Paths\AppPaths;

$paths = AppPaths::instance();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle storage files
if (str_starts_with($uri, '/storage/')) {
    // URI: /storage/uploads/2026/02/27/file.jpg
    // Map to: storage/uploads/2026/02/27/file.jpg
    $relativePath = substr($uri, 1); // Remove leading /
    $filePath = $paths->getBasePath() . '/' . $relativePath;
    
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        header("Content-Type: $mimeType");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        return true;
    }
    
    // File not found
    http_response_code(404);
    echo 'File not found';
    return true;
}

// Let PHP serve static files from public/
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Route everything else through index.php
// Extract route parameters from URI and add to request
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Parse URI for potential route parameters
// e.g., /admin/pages/123/edit → store '123' as 'id' parameter
$patterns = [
    '#/admin/pages/([^/]+)/edit#' => '_route_id',
    '#/admin/pages/([^/]+)#' => '_route_id',
    '#/admin/articles/([^/]+)/edit#' => '_route_id',
    '#/admin/articles/([^/]+)#' => '_route_id',
    '#/admin/forms/([^/]+)/submissions#' => '_route_id',
    '#/admin/forms/([^/]+)/edit#' => '_route_id',
    '#/admin/forms/([^/]+)#' => '_route_id',
    '#/admin/media/([^/]+)#' => '_route_id',
];

foreach ($patterns as $pattern => $paramName) {
    if (preg_match($pattern, $uri, $matches)) {
        $_GET[$paramName] = $matches[1];
        break;
    }
}

require __DIR__ . '/public/index.php';

return true;
