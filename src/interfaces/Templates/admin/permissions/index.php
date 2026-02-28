<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var Permission[] $permissions
 * @var string $currentGroup
 */
use Domain\Model\Permission;
?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/permissions/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + New Permission
        </a>
    </div>

    <?php if (!empty($currentGroup)): ?>
    <div class="mb-4">
        <a href="/admin/permissions" class="text-blue-600 hover:text-blue-800">← View All Permissions</a>
        <span class="ml-4 text-gray-600">Filtering by group: <strong><?= $this->e($currentGroup) ?></strong></span>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($permissions as $permission): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="px-2 py-1 bg-gray-100 rounded text-sm"><?= $this->e($permission->nameString()) ?></code>
                    </td>
                    <td class="px-6 py-4">
                        <?= $this->e($permission->description() ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="/admin/permissions?group=<?= $this->e($permission->group()) ?>" 
                           class="text-blue-600 hover:text-blue-800">
                            <?= $this->e($permission->group()) ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= $this->date($permission->createdAt()) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="/admin/permissions/<?= $this->e($permission->id()) ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <form method="POST" action="/admin/permissions/<?= $this->e($permission->id()) ?>" class="inline" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
