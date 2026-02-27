<?php
/**
 * Articles List
 * 
 * @var TemplateRenderer $this
 * @var array $articles
 * @var string $title
 * @var string|null $searchQuery
 */
?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <?= $this->e($title ?? 'Articles') ?>
    </h1>

    <?php if (isset($searchQuery)): ?>
        <p class="text-gray-600 mb-4">Search results for: <strong><?= $this->e($searchQuery) ?></strong></p>
    <?php endif; ?>

    <?php if ($this->isEmpty($articles)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No articles found.</p>
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
