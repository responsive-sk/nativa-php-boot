<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var Article $article
 * @var string|null $error
 */
use Domain\Model\Article;
?>
<div class="p-6">
    <div class="max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= $this->e($title) ?></h1>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $this->e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/admin/articles/<?= $this->e($article->id()) ?>" class="bg-white rounded-lg shadow p-6 space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text"
                       id="title"
                       name="title"
                       required
                       value="<?= $this->e($article->title()) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                <textarea id="excerpt"
                          name="excerpt"
                          rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $this->e($article->excerpt()) ?></textarea>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea id="content"
                          name="content"
                          rows="15"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $this->e($article->content()) ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Article
                </button>
                <a href="/admin/articles" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
            </div>
        </form>

        <div class="mt-6 border-t pt-4">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Article Info</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span class="ml-2 <?= $article->isPublished() ? 'text-green-600' : 'text-yellow-600' ?>">
                        <?= $article->isPublished() ? 'Published' : 'Draft' ?>
                    </span>
                </div>
                <div>
                    <span class="text-gray-500">Created:</span>
                    <span class="ml-2"><?= $this->date($article->createdAt(), 'M d, Y H:i') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
