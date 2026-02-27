<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Page Controller
 */
class PageController
{
    public function index(): Response
    {
        return new Response($this->layout(<<<CONTENT
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Pages</h2>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Pages functionality coming soon...</p>
            </div>
        CONTENT));
    }

    public function create(): Response
    {
        return new Response($this->layout(<<<CONTENT
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Page</h2>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Create page form coming soon...</p>
            </div>
        CONTENT));
    }

    private function layout(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4"><h1 class="text-2xl font-bold">Admin</h1></div>
            <nav class="mt-4">
                <a href="/admin" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
                <a href="/admin/articles" class="block px-4 py-2 hover:bg-gray-700">Articles</a>
                <a href="/admin/pages" class="block px-4 py-2 bg-gray-700">Pages</a>
                <a href="/admin/forms" class="block px-4 py-2 hover:bg-gray-700">Forms</a>
            </nav>
        </div>
        <div class="flex-1 overflow-auto p-8">{$content}</div>
    </div>
</body>
</html>
HTML;
    }
}
