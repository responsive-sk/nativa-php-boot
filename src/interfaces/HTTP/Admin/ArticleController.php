<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Application\Services\ArticleManager;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin Article Controller
 */
class ArticleController
{
    private ArticleManager $articleManager;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $uow = new UnitOfWork($db);
        $articleRepo = new ArticleRepository($uow);
        $eventDispatcher = new \Application\Services\EventDispatcher();
        $this->articleManager = new ArticleManager($articleRepo, $eventDispatcher);
    }

    public function index(): Response
    {
        $articles = $this->articleManager->listPublished(100);
        
        $rows = '';
        foreach ($articles as $article) {
            $statusClass = $article->isPublished() ? 'text-green-600' : 'text-yellow-600';
            $status = $article->isPublished() ? 'Published' : 'Draft';
            
            $rows .= <<<TR
                <tr class="border-b">
                    <td class="p-4">
                        <a href="/admin/articles/{$article->id()}/edit" class="text-blue-600 hover:underline">
                            {$article->title()}
                        </a>
                    </td>
                    <td class="p-4">{$article->authorId()}</td>
                    <td class="p-4 {$statusClass}">{$status}</td>
                    <td class="p-4">{$article->createdAt()}</td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a href="/admin/articles/{$article->id()}/edit" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="/admin/articles/{$article->id()}" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            TR;
        }

        $html = $this->getLayout(<<<CONTENT
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Articles</h2>
                <a href="/admin/articles/create" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + New Article
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Title</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Author</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Status</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Created</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </div>
        CONTENT);

        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->getLayout(<<<CONTENT
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Article</h2>
            
            <form method="POST" action="/admin/articles" class="bg-white rounded-lg shadow p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Excerpt</label>
                    <textarea name="excerpt" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content *</label>
                    <textarea name="content" rows="15" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border"></textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" name="status" value="draft"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="submit" name="status" value="published"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Publish
                    </button>
                    <a href="/admin/articles" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                </div>
            </form>
        CONTENT);

        return new Response($html);
    }

    public function store(Request $request): Response
    {
        try {
            $data = $request->request->all();
            
            $article = $this->articleManager->create(
                title: $data['title'],
                content: $data['content'],
                authorId: 'admin', // TODO: Get from session
                excerpt: $data['excerpt'] ?? null,
            );

            if ($data['status'] === 'published') {
                $this->articleManager->publish($article->id());
            }

            header('Location: /admin/articles');
            exit;
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function edit(string $id): Response
    {
        $article = $this->articleManager->findById($id);
        
        if (!$article) {
            return new Response('Article not found', 404);
        }

        $html = $this->getLayout(<<<CONTENT
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Article</h2>
            
            <form method="POST" action="/admin/articles/{$id}?_method=PUT" class="bg-white rounded-lg shadow p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" value="{$article->title()}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Excerpt</label>
                    <textarea name="excerpt" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{$article->excerpt()}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content *</label>
                    <textarea name="content" rows="15" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{$article->content()}</textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update
                    </button>
                    <a href="/admin/articles" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                </div>
            </form>
        CONTENT);

        return new Response($html);
    }

    public function update(string $id, Request $request): Response
    {
        try {
            $data = $request->request->all();
            
            $this->articleManager->update(
                articleId: $id,
                title: $data['title'] ?? null,
                content: $data['content'] ?? null,
                excerpt: $data['excerpt'] ?? null,
            );

            header('Location: /admin/articles');
            exit;
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(string $id, Request $request): Response
    {
        try {
            $this->articleManager->delete($id);
            header('Location: /admin/articles');
            exit;
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function publish(string $id): Response
    {
        try {
            $this->articleManager->publish($id);
            header('Location: /admin/articles');
            exit;
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    private function getLayout(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Articles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4"><h1 class="text-2xl font-bold">Admin Panel</h1></div>
            <nav class="mt-4">
                <a href="/admin" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
                <a href="/admin/articles" class="block px-4 py-2 bg-gray-700">Articles</a>
                <a href="/admin/pages" class="block px-4 py-2 hover:bg-gray-700">Pages</a>
                <a href="/admin/forms" class="block px-4 py-2 hover:bg-gray-700">Forms</a>
                <a href="/admin/media" class="block px-4 py-2 hover:bg-gray-700">Media</a>
                <a href="/admin/settings" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
            </nav>
        </div>
        <div class="flex-1 overflow-auto p-8">{$content}</div>
    </div>
</body>
</html>
HTML;
    }
}
