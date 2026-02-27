<?php
/**
 * Admin Dashboard Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 */
?>
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $this->e($title) ?></h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Article Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Articles</h3>
            <p class="text-3xl font-bold text-gray-900">0</p>
            <a href="/admin/articles" class="text-blue-600 hover:underline text-sm mt-2 inline-block">View all →</a>
        </div>

        <!-- Form Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Forms</h3>
            <p class="text-3xl font-bold text-gray-900">0</p>
            <a href="/admin/forms" class="text-blue-600 hover:underline text-sm mt-2 inline-block">View all →</a>
        </div>

        <!-- Contact Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Contacts</h3>
            <p class="text-3xl font-bold text-gray-900">0</p>
            <a href="/admin/contacts" class="text-blue-600 hover:underline text-sm mt-2 inline-block">View all →</a>
        </div>

        <!-- User Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Users</h3>
            <p class="text-3xl font-bold text-gray-900">1</p>
            <a href="/admin/users" class="text-blue-600 hover:underline text-sm mt-2 inline-block">View all →</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="flex gap-4">
            <a href="/admin/articles/create" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + New Article
            </a>
            <a href="/admin/forms/create" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                + New Form
            </a>
            <a href="/admin/pages/create" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                + New Page
            </a>
        </div>
    </div>
</div>
