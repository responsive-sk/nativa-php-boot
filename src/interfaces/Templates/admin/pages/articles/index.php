<?php
/**
 * Admin Articles List Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array $articles
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/articles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + New Article
        </a>
    </div>

    <?php if (empty($articles)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No articles yet.</p>
            <a href="/admin/articles/create" class="text-blue-600 hover:underline mt-2 inline-block">Create your first article</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <a href="/admin/articles/<?= $article->id() ?>/edit" class="text-blue-600 hover:underline">
                                <?= $this->e($article->title()) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full <?= $article->isPublished() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= $article->isPublished() ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $this->date($article->createdAt()) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <a href="/admin/articles/<?= $article->id() ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <a href="#" onclick="if(confirm('Delete?')) {}" class="text-red-600 hover:text-red-900">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
