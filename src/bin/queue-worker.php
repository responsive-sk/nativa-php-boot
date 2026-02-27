#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Queue Worker CLI Command
 * 
 * Usage:
 *   php bin/queue-worker.php default
 *   php bin/queue-worker.php default --tries=5 --timeout=120
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Infrastructure\Persistence\DatabaseConnectionManager;
use Infrastructure\Queue\QueueRepository;
use Infrastructure\Queue\Worker\Worker;
use Infrastructure\Queue\Worker\JobHandler;
use Infrastructure\Queue\Handlers\JobHandlerRegistry;
use Infrastructure\Queue\Handlers\OutboxProcessor;

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Get queue name from arguments
$queue = $argv[1] ?? 'default';
$maxTries = (int) ($argv[2] ?? 3);
$timeout = (int) ($argv[3] ?? 60);

// Parse --options
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--tries=')) {
        $maxTries = (int) substr($arg, 8);
    }
    if (str_starts_with($arg, '--timeout=')) {
        $timeout = (int) substr($arg, 10);
    }
}

echo "╔════════════════════════════════════════╗\n";
echo "║       PHP CMS Queue Worker             ║\n";
echo "╚════════════════════════════════════════╝\n\n";

// Initialize dependencies
$dbManager = new DatabaseConnectionManager();
$queue = new QueueRepository($dbManager);

// Create job handler registry and register handlers
$registry = new JobHandlerRegistry();

// Register Outbox Processor
$outboxProcessor = new OutboxProcessor($dbManager);
$registry->register('process-outbox', fn($job) => $outboxProcessor->process());

// Register Contact Form handler
$registry->register('send-contact-notification', function($job) {
    $handler = new \Infrastructure\Queue\Handlers\SendContactNotificationHandler();
    $handler($job);
});

// Register Form Submission handler
$registry->register('send-form-submission-notification', function($job) {
    $handler = new \Infrastructure\Queue\Handlers\SendFormSubmissionNotificationHandler();
    $handler($job);
});

// Register custom handlers here
// $registry->register('send-email', fn($job) => ...);
// $registry->register('reindex-article', fn($job) => ...);

// Create worker
$jobHandler = new JobHandler($registry);
$worker = new Worker($queue, $jobHandler);

// Handle SIGINT (Ctrl+C)
pcntl_signal(SIGINT, function () use ($worker) {
    echo "\nShutting down worker...\n";
    $worker->stop();
});

pcntl_dispatch_signals();

// Start worker
$worker->work($queue, $maxTries, $timeout);
