<?php

declare(strict_types=1);

use App\Domain\Admin\DashboardStats;

/**
 * @var DashboardStats $stats
 */
?>

<div class="admin-dashboard">
    <h1>Dashboard</h1>

    <div class="row mt-4">
        <!-- Articles Stats -->
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h5 class="card-title">Articles</h5>
                    <h2 class="mb-0"><?= $stats->totalArticles ?></h2>
                    <small class="text-muted">
                        <?= $stats->articlesByStatus['published'] ?> published,
                        <?= $stats->articlesByStatus['draft'] ?> drafts
                    </small>
                </div>
            </div>
        </div>

        <!-- Pages Stats -->
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h5 class="card-title">Pages</h5>
                    <h2 class="mb-0"><?= $stats->totalPages ?></h2>
                    <small class="text-muted">Static pages</small>
                </div>
            </div>
        </div>

        <!-- Users Stats -->
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <h2 class="mb-0"><?= $stats->totalUsers ?></h2>
                    <small class="text-muted">
                        <?= $stats->usersByRole['admin'] ?> admins,
                        <?= $stats->usersByRole['editor'] ?> editors
                    </small>
                </div>
            </div>
        </div>

        <!-- Forms Stats -->
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h5 class="card-title">Forms</h5>
                    <h2 class="mb-0"><?= $stats->totalForms ?></h2>
                    <small class="text-muted">
                        <?= $stats->totalFormSubmissions ?> submissions
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Searches -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Searches</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($stats->topSearches as $query => $count): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($query) ?>
                            <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/admin/pages/create" class="btn btn-primary">Create New Page</a>
                        <a href="/admin/blog/articles/create" class="btn btn-primary">Create New Article</a>
                        <a href="/admin/forms" class="btn btn-primary">Manage Forms</a>
                        <a href="/admin/users" class="btn btn-secondary">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard .stats-card {
    border-left: 4px solid #0d6efd;
}
.admin-dashboard .card-title {
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
}
.admin-dashboard h2 {
    font-size: 2.5rem;
    font-weight: 700;
}
</style>
