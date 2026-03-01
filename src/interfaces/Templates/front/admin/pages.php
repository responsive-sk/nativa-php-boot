<?php

declare(strict_types=1);

use App\Domain\Page\Page;
use App\Domain\Page\PageStatus;

/**
 * @var Page[] $pages
 */
?>

<div class="admin-pages">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Pages</h1>
        <a href="/admin/pages/create" class="btn btn-primary">+ New Page</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($page->title) ?></strong>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($page->slug) ?></code>
                        </td>
                        <td>
                            <?php if ($page->isPublished()): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $page->updated_at->format('Y-m-d H:i') ?>
                        </td>
                        <td class="text-end">
                            <a href="/admin/pages/edit/<?= $page->getId() ?>"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="deletePage(<?= $page->getId() ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($pages)): ?>
            <div class="text-center py-5">
                <p class="text-muted mb-0">No pages found.</p>
                <a href="/admin/pages/create" class="btn btn-primary mt-2">Create First Page</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deletePage(id) {
    if (confirm('Are you sure you want to delete this page?')) {
        fetch('/admin/pages/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}
</script>
