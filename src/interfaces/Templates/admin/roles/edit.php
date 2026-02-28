<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var Role $role
 * @var string|null $error
 */
use Domain\Model\Role;
?>
<div class="p-6">
    <div class="max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= $this->e($title) ?></h1>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $this->e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/admin/roles/<?= $this->e($role->id()) ?>/edit" class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                <input type="text" 
                       value="<?= $this->e($role->nameString()) ?>"
                       disabled
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500">
                <p class="mt-1 text-sm text-gray-500">Role name cannot be changed</p>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $this->e($role->description() ?? '') ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Update Role
                </button>
                <a href="/admin/roles" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>

        <?php if (!in_array($role->nameString(), ['admin', 'editor', 'viewer', 'user'], true)): ?>
        <div class="mt-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Danger Zone</h3>
            <form method="POST" action="/admin/roles/<?= $this->e($role->id()) ?>" onsubmit="return confirm('Are you sure you want to delete this role? This cannot be undone.')">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Delete Role
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
