#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Seed Demo Articles
 * 
 * Creates sample articles about recent development work
 */

require __DIR__ . '/../vendor/autoload.php';

use Domain\Model\Article;
use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Persistence\DatabaseConnection;

echo "📝 Seeding demo articles...\n\n";

// Get database connection
$db = DatabaseConnection::getInstance()->getConnection();

// Get or create admin user
$userId = $db->query('SELECT id FROM users WHERE email = "admin@phpcms.local"')->fetchColumn();
if (!$userId) {
    echo "❌ Admin user not found. Please run 'php bin/cms seed' first.\n";
    exit(1);
}

// Create ArticleManager
$container = ContainerFactory::create();
$articleManager = $container->get(ArticleManager::class);

// Articles to seed
$articles = [
    [
        'title' => 'End-to-End Type Safety: PHP to TypeScript',
        'slug' => 'end-to-end-type-safety-php-typescript',
        'excerpt' => 'Auto-generate TypeScript types from PHP entities for complete type safety across your entire stack.',
        'content' => 'In modern web development, maintaining type consistency between backend and frontend is crucial. Our new TypeScript type generation system solves this problem elegantly by automatically generating TypeScript interfaces from PHP classes.',
        'tags' => ['TypeScript', 'PHP', 'Type Safety'],
    ],
    [
        'title' => 'Modern PHP: Using Enums for Better Type Safety',
        'slug' => 'modern-php-enums-type-safety',
        'excerpt' => 'Migrate from class-based value objects to native PHP 8.4 enums for cleaner, more maintainable code.',
        'content' => 'PHP 8.1 introduced native enumerations, and PHP 8.4 made them even better. We migrated our ArticleStatus and Role value objects to enums for better type safety and IDE support.',
        'tags' => ['PHP 8.4', 'Enums', 'Refactoring'],
    ],
    [
        'title' => 'Restructuring Templates for Better Organization',
        'slug' => 'restructuring-templates-better-organization',
        'excerpt' => 'Centralize all templates under a single root for improved maintainability and clearer separation of concerns.',
        'content' => 'We recently restructured our template organization from a deeply nested structure to a centralized Templates/ root. This improved our developer experience and made the codebase easier to navigate.',
        'tags' => ['Architecture', 'Templates', 'Refactoring'],
    ],
    [
        'title' => 'Building Reusable TypeScript Components',
        'slug' => 'building-reusable-typescript-components',
        'excerpt' => 'Create type-safe, reusable UI components using generated TypeScript types and best practices.',
        'content' => 'Components are the building blocks of modern frontend development. Our ArticleCard component demonstrates how to create type-safe, reusable components using generated TypeScript types.',
        'tags' => ['TypeScript', 'Components', 'Frontend'],
    ],
    [
        'title' => 'Asset Management with Vite and PHP',
        'slug' => 'asset-management-vite-php',
        'excerpt' => 'Handle hashed assets, manifest files, and cache busting with AssetHelper and Vite build system.',
        'content' => 'Managing frontend assets in a PHP application requires careful coordination between build tools and runtime. Our AssetHelper bridges the gap between Vite builds and PHP runtime.',
        'tags' => ['Vite', 'Assets', 'Build Tools'],
    ],
];

// Seed articles
$count = 0;
foreach ($articles as $articleData) {
    try {
        $article = Article::create(
            title: $articleData['title'],
            content: $articleData['content'],
            authorId: $userId,
            excerpt: $articleData['excerpt'],
        );
        
        $article->setTags($articleData['tags']);
        $article->publish();
        
        // Save directly
        $repo = $container->get(\Domain\Repository\ArticleRepositoryInterface::class);
        $repo->save($article);
        
        echo "✅ Created: {$articleData['title']}\n";
        $count++;
    } catch (\Throwable $e) {
        echo "❌ Failed to create '{$articleData['title']}': " . $e->getMessage() . "\n";
    }
}

echo "\n✨ Done! Created $count demo articles.\n";
echo "📝 View them at: http://localhost:8000/articles\n";
