<?php
/**
 * Admin Form Submissions Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array $submissions
 * @var int $currentPage
 * @var int $totalPages
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
            <p class="text-gray-600 mt-1">Page <?= $currentPage ?> of <?= $totalPages ?></p>
        </div>
        <a href="/admin/forms" class="text-gray-600 hover:text-gray-900">← Back to Forms</a>
    </div>

    <?php if (empty($submissions)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No submissions yet.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $this->e(substr($submission->id(), 0, 8)) ?>...</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php foreach ($submission->data() as $key => $value): ?>
                                    <div class="mb-1">
                                        <strong class="text-gray-700"><?= $this->e($key) ?>:</strong>
                                        <span class="text-gray-600"><?= $this->e(is_array($value) ? implode(', ', $value) : $value) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= $this->e($submission->ipAddress() ?? 'N/A') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $this->date($submission->submittedAt(), 'M d, Y H:i') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="if(confirm('Delete this submission?')) { document.getElementById('delete-<?= $submission->id() ?>').submit(); }" 
                                    class="text-red-600 hover:text-red-900">Delete</button>
                            <form id="delete-<?= $submission->id() ?>" 
                                  action="/admin/submissions/<?= $submission->id() ?>" 
                                  method="POST" class="hidden">
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-4 flex justify-center gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">← Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 rounded <?= $i === $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
