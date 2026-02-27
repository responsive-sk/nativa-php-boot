<?php
/**
 * Homepage
 * 
 * @var TemplateRenderer $this
 * @var array $articles
 * @var string $title
 */
?>
<div class="max-w-4xl mx-auto">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">Welcome to PHP CMS</h1>
        <p class="text-xl">Modern Content Management System built with PHP 8.4+</p>
    </div>

    <!-- Latest Articles -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Latest Articles</h2>

        <?php if ($this->isEmpty($articles)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600">No articles yet.</p>
                <a href="/admin/articles/create" class="text-blue-600 hover:underline mt-2 inline-block">Create your first article</a>
            </div>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($articles as $article): ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">
                            <a href="/articles/<?= $this->e($article->slug()) ?>" class="hover:text-blue-600">
                                <?= $this->e($article->title()) ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4"><?= $this->e($article->excerpt()) ?></p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><?= $this->date($article->publishedAt() ?? $article->createdAt()) ?></span>
                            <a href="/articles/<?= $this->e($article->slug()) ?>" class="text-blue-600 hover:underline">Read more →</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <a href="/articles" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            View All Articles
        </a>
    </div>
</div>
