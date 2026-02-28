<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var Role[] $roles
 */
use Domain\Model\Role;
?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/roles/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + New Role
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            <?= $role->isAdmin() ? 'bg-red-100 text-red-800' : 
                               ($role->isEditor() ? 'bg-blue-100 text-blue-800' : 
                               'bg-gray-100 text-gray-800') ?>">
                            <?= $this->e($role->nameString()) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?= $this->e($role->description() ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= $role->getLevel() ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= $this->date($role->createdAt()) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="/admin/roles/<?= $this->e($role->id()) ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <?php if (!in_array($role->nameString(), ['admin', 'editor', 'viewer', 'user'], true)): ?>
                        <form method="POST" action="/admin/roles/<?= $this->e($role->id()) ?>" class="inline" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
