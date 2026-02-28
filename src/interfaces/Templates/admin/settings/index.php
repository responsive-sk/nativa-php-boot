<?php
/**
 * @var TemplateRenderer $this
 * @var string $title
 * @var array $settings
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

        <form method="POST" action="/admin/settings" class="bg-white rounded-lg shadow p-6 space-y-4">
            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                <input type="text"
                       id="site_name"
                       name="site_name"
                       value="<?= $this->e($settings['site_name'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="site_url" class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                <input type="url"
                       id="site_url"
                       name="site_url"
                       value="<?= $this->e($settings['site_url'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                <input type="email"
                       id="admin_email"
                       name="admin_email"
                       value="<?= $this->e($settings['admin_email'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
