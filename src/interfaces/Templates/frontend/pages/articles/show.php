<?php
/**
 * Article Detail
 * 
 * @var TemplateRenderer $this
 * @var object $article
 */
?>
<article class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-8">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= $this->e($article->title()) ?></h1>
            <div class="text-sm text-gray-500">
                <span>Published on <?= $this->date($article->publishedAt(), 'F d, Y') ?></span>
            </div>
        </header>

        <?php if ($article->image()): ?>
            <img src="<?= $this->e($article->image()) ?>" alt="<?= $this->e($article->title()) ?>" class="w-full h-64 object-cover rounded-lg mb-6">
        <?php endif; ?>

        <div class="prose max-w-none">
            <?= $this->nl2br($article->content()) ?>
        </div>

        <?php if (!empty($article->tags())): ?>
            <div class="mt-6 pt-6 border-t">
                <span class="text-gray-500 text-sm">Tags:</span>
                <div class="flex gap-2 mt-2">
                    <?php foreach ($article->tags() as $tag): ?>
                        <a href="/tag/<?= $this->e(strtolower(str_replace(' ', '-', $tag))) ?>" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">
                            <?= $this->e($tag) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>

<div class="max-w-3xl mx-auto mt-8">
    <a href="/articles" class="text-blue-600 hover:underline">← Back to Articles</a>
</div>
