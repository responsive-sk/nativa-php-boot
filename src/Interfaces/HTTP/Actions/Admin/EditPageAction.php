<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Admin Edit Page Action.
 */
final class EditPageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        // Check if this is an update (POST to /admin/pages/{id}/edit)
        if ('POST' === $request->getMethod()) {
            // Check for special actions
            $action = (string) $request->getRequestParam('_action', '');

            if ($action) {
                $this->handleAction($request, $action);

                // handleAction() sends its own response, so we return here
                return new Response('', 204);
            }

            return $this->update($request);
        }

        return $this->edit($request);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(PageManager::class),
            $container->get(TemplateRenderer::class),
        );
    }

    private function handleAction(Request $request, string $action): void
    {
        try {
            match ($action) {
                'add_block'    => $this->addBlock($request),
                'update_block' => $this->updateBlock($request),
                'delete_block' => $this->deleteBlock($request),
                'attach_media' => $this->attachMedia($request),
                'embed_form'   => $this->embedForm($request),
                default        => $this->json(['error' => 'Unknown action'], 400),
            };
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function addBlock(Request $request): Response
    {
        $id = (string) $this->param($request, 'id');
        $type = (string) $request->getRequestParam('type', '');
        $title = (string) $request->getRequestParam('title', '');
        $content = (string) $request->getRequestParam('content', '');
        $sortOrder = (int) $request->getRequestParam('sortOrder', 0);

        if (empty($type)) {
            return $this->json(['error' => 'Block type is required'], 400);
        }

        $this->pageManager->addBlock($id, $type, $title ?: null, $content ?: null, [], $sortOrder);

        return $this->json(['success' => true]);
    }

    private function updateBlock(Request $request): Response
    {
        $blockId = (string) $request->getRequestParam('blockId', '');
        $title = (string) $request->getRequestParam('title', '');

        if (empty($blockId)) {
            return $this->json(['error' => 'Block ID is required'], 400);
        }

        // TODO: Implement block update in PageManager
        return $this->json(['success' => true, 'message' => 'Block update TODO']);
    }

    private function deleteBlock(Request $request): Response
    {
        $blockId = (string) $request->getRequestParam('blockId', '');

        if (empty($blockId)) {
            return $this->json(['error' => 'Block ID is required'], 400);
        }

        // TODO: Implement block delete
        return $this->json(['success' => true, 'message' => 'Block delete TODO']);
    }

    private function attachMedia(Request $request): Response
    {
        $id = (string) $this->param($request, 'id');
        $mediaId = (string) $request->getRequestParam('mediaId', '');
        $caption = (string) $request->getRequestParam('caption', '');

        if (empty($mediaId)) {
            return $this->json(['error' => 'Media ID is required'], 400);
        }

        $this->pageManager->attachMedia($id, $mediaId, $caption ?: null);

        return $this->json(['success' => true]);
    }

    private function embedForm(Request $request): Response
    {
        $id = (string) $this->param($request, 'id');
        $formId = (string) $request->getRequestParam('formId', '');
        $title = (string) $request->getRequestParam('title', '');
        $position = (string) $request->getRequestParam('position', 'sidebar');

        if (empty($formId)) {
            return $this->json(['error' => 'Form ID is required'], 400);
        }

        $this->pageManager->embedForm($id, $formId, $title ?: null, $position);

        return $this->json(['success' => true]);
    }

    private function edit(Request $request): Response
    {
        $id = (string) $this->param($request, 'id');
        $page = $this->pageManager->findById($id);

        if (null === $page) {
            return $this->notFound('Page not found');
        }

        // Get page with all relationships
        $pageData = $this->pageManager->getPageWithRelations($id);

        $content = $this->renderer->render(
            'admin/pages/pages/edit',
            [
                'title'  => 'Edit Page',
                'page'   => $page,
                'blocks' => $pageData['blocks'] ?? [],
                'media'  => $pageData['media'] ?? [],
                'forms'  => $pageData['forms'] ?? [],
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function update(Request $request): Response
    {
        try {
            $id = (string) $this->param($request, 'id');

            if (empty($id)) {
                return $this->json(['error' => 'Page ID is required'], 400);
            }

            $title = (string) $request->getRequestParam('title', '');
            $content = (string) $request->getRequestParam('content', '');
            $template = (string) $request->getRequestParam('template', 'default');
            $metaTitle = (string) $request->getRequestParam('metaTitle', '');
            $metaDescription = (string) $request->getRequestParam('metaDescription', '');

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
                'page'    => [
                    'id'    => $page->id(),
                    'title' => $page->title(),
                    'slug'  => $page->slug(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
