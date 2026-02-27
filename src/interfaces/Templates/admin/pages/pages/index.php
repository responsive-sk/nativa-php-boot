<?php
/**
 * Admin Pages List Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array $pages
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/pages/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + New Page
        </a>
    </div>

    <?php if (empty($pages)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No pages yet.</p>
            <a href="/admin/pages/create" class="text-blue-600 hover:underline mt-2 inline-block">Create your first page</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <a href="/admin/pages/<?= $page->id() ?>/edit" class="text-blue-600 hover:underline">
                                <?= $this->e($page->title()) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $this->e($page->slug()) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full <?= $page->isPublished() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= $page->isPublished() ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $this->e($page->template()) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $this->date($page->createdAt()) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <a href="/admin/pages/<?= $page->id() ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <button onclick="deletePage('<?= $page->id() ?>')" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
async function deletePage(id) {
    if (!confirm('Are you sure you want to delete this page?')) return;
    
    try {
        const response = await fetch('/admin/pages/' + id, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            const result = await response.json();
            alert('Delete failed: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        alert('Delete error: ' + error.message);
    }
}
</script>
