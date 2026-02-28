<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var string|null $error
 */
?>
<div class="p-6">
    <div class="max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= $this->e($title) ?></h1>

        <?php if (isset($error) && $error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $this->e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/admin/permissions" class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       value="<?= $this->e($old['name'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., admin.dashboard">
                <p class="mt-1 text-sm text-gray-500">Format: <code>resource.action</code> (e.g., <code>admin.dashboard</code>, <code>articles.create</code>)</p>
            </div>

            <div class="mb-4">
                <label for="group" class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                <input type="text" 
                       id="group" 
                       name="group" 
                       value="<?= $this->e($old['group'] ?? 'default') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., admin, articles, users">
                <p class="mt-1 text-sm text-gray-500">Groups help organize permissions (e.g., all "admin.*" permissions)</p>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Describe this permission..."><?= $this->e($old['description'] ?? '') ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Create Permission
                </button>
                <a href="/admin/permissions" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
