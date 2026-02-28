<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Edit Page Action
 */
final class EditPageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        // Check if this is an update (POST to /admin/pages/{id}/edit)
        if ($request->getMethod() === 'POST') {
            // Check for special actions
            $action = $request->request->get('_action');
            
            if ($action) {
                return $this->handleAction($request, $action);
            }
            
            return $this->update($request);
        }
        return $this->edit($request);
    }

    private function handleAction(Request $request, string $action): Response
    {
        try {
            match ($action) {
                'add_block' => $this->addBlock($request),
                'update_block' => $this->updateBlock($request),
                'delete_block' => $this->deleteBlock($request),
                'attach_media' => $this->attachMedia($request),
                'embed_form' => $this->embedForm($request),
                default => $this->json(['error' => 'Unknown action'], 400),
            };
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function addBlock(Request $request): Response
    {
        $id = $this->param($request, 'id');
        $type = $request->request->get('type', '');
        $title = $request->request->get('title', '');
        $content = $request->request->get('content', '');
        $sortOrder = (int) $request->request->get('sortOrder', 0);

        if (empty($type)) {
            return $this->json(['error' => 'Block type is required'], 400);
        }

        $this->pageManager->addBlock($id, $type, $title ?: null, $content ?: null, [], $sortOrder);
        return $this->json(['success' => true]);
    }

    private function updateBlock(Request $request): Response
    {
        $blockId = $request->request->get('blockId', '');
        $title = $request->request->get('title', '');

        if (empty($blockId)) {
            return $this->json(['error' => 'Block ID is required'], 400);
        }

        // TODO: Implement block update in PageManager
        return $this->json(['success' => true, 'message' => 'Block update TODO']);
    }

    private function deleteBlock(Request $request): Response
    {
        $blockId = $request->request->get('blockId', '');

        if (empty($blockId)) {
            return $this->json(['error' => 'Block ID is required'], 400);
        }

        // TODO: Implement block delete
        return $this->json(['success' => true, 'message' => 'Block delete TODO']);
    }

    private function attachMedia(Request $request): Response
    {
        $id = $this->param($request, 'id');
        $mediaId = $request->request->get('mediaId', '');
        $caption = $request->request->get('caption', '');

        if (empty($mediaId)) {
            return $this->json(['error' => 'Media ID is required'], 400);
        }

        $this->pageManager->attachMedia($id, $mediaId, $caption ?: null);
        return $this->json(['success' => true]);
    }

    private function embedForm(Request $request): Response
    {
        $id = $this->param($request, 'id');
        $formId = $request->request->get('formId', '');
        $title = $request->request->get('title', '');
        $position = $request->request->get('position', 'sidebar');

        if (empty($formId)) {
            return $this->json(['error' => 'Form ID is required'], 400);
        }

        $this->pageManager->embedForm($id, $formId, $title ?: null, $position);
        return $this->json(['success' => true]);
    }

    private function edit(Request $request): Response
    {
        $id = $this->param($request, 'id');
        $page = $this->pageManager->findById($id);

        if ($page === null) {
            return $this->notFound('Page not found');
        }

        // Get page with all relationships
        $pageData = $this->pageManager->getPageWithRelations($id);

        $content = $this->renderer->render(
            'admin/pages/pages/edit',
            [
                'title' => 'Edit Page',
                'page' => $page,
                'blocks' => $pageData['blocks'] ?? [],
                'media' => $pageData['media'] ?? [],
                'forms' => $pageData['forms'] ?? [],
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function update(Request $request): Response
    {
        try {
            $id = $this->param($request, 'id');
            
            if (empty($id)) {
                return $this->json(['error' => 'Page ID is required'], 400);
            }
            
            $title = (string) $request->request->get('title', '');
            $content = (string) $request->request->get('content', '');
            $template = (string) $request->request->get('template', 'default');
            $metaTitle = (string) $request->request->get('metaTitle', '');
            $metaDescription = (string) $request->request->get('metaDescription', '');

            if (empty($title) || empty($content)) {
                return $this->json(['error' => 'Title and content are required'], 400);
            }

            $page = $this->pageManager->update(
                pageId: $id,
                title: $title,
                content: $content,
                template: $template,
                metaTitle: $metaTitle ?: null,
                metaDescription: $metaDescription ?: null,
            );

            return $this->json([
                'success' => true,
                'page' => [
                    'id' => $page->id(),
                    'title' => $page->title(),
                    'slug' => $page->slug(),
                ],
            ]);

        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(PageManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
