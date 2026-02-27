<?php
/**
 * Admin Forms List Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array $forms
 */
?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
    <a href="/admin/forms/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Create Form
    </a>
</div>

<?php if (empty($forms)): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-600">No forms yet.</p>
        <a href="/admin/forms/create" class="text-blue-600 hover:underline mt-2 inline-block">Create your first form</a>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fields</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($forms as $form): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= $this->e($form->name()) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500"><?= $this->e($form->slug()) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500"><?= count($form->schema()) ?> fields</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= $this->date($form->createdAt()) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="/admin/forms/<?= $this->e($form->id()) ?>/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <a href="/admin/forms/<?= $this->e($form->id()) ?>/submissions" class="text-green-600 hover:text-green-900 mr-3">Submissions</a>
                        <form action="/admin/forms/<?= $this->e($form->id()) ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
