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

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Error reporting
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Create kernel and handle request
$kernel = new Kernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
