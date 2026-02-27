<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin Dashboard Controller
 */
class DashboardController
{
    public function index(): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-4">
                <a href="/admin" class="block px-4 py-2 bg-gray-700">Dashboard</a>
                <a href="/admin/articles" class="block px-4 py-2 hover:bg-gray-700">Articles</a>
                <a href="/admin/pages" class="block px-4 py-2 hover:bg-gray-700">Pages</a>
                <a href="/admin/forms" class="block px-4 py-2 hover:bg-gray-700">Forms</a>
                <a href="/admin/media" class="block px-4 py-2 hover:bg-gray-700">Media</a>
                <a href="/admin/settings" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h2>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Articles</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Pages</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Forms</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Submissions</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="flex gap-4">
                        <a href="/admin/articles/create" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            New Article
                        </a>
                        <a href="/admin/pages/create" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            New Page
                        </a>
                        <a href="/admin/forms/create" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                            New Form
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return new Response($html);
    }
}
