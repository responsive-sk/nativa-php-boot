<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Media;

use Application\Services\MediaManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Delete Media Action
 */
final class DeleteMediaAction extends Action
{
    public function __construct(
        private readonly MediaManager $mediaManager,
    ) {
    }

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($id);
        }

        // Support form POST with _method override
        if ($request->getMethod() === 'POST' && $request->request->get('_method') === 'DELETE') {
            return $this->delete($id);
        }

        return new Response('Method not allowed', 405);
    }

    private function delete(string $id): Response
    {
        try {
            $this->mediaManager->delete($id);
            return $this->redirect('/admin/media');
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(MediaManager::class),
        );
    }
}
