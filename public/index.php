<?php

declare(strict_types=1);

/**
 * Application entry point
 */

// Codeception C3 Code Coverage
if (file_exists(__DIR__ . '/../c3.php')) {
    include __DIR__ . '/../c3.php';
}

use Interfaces\HTTP\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

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
