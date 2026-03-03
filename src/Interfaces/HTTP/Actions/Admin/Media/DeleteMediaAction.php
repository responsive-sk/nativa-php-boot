<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Media;

use Application\Services\MediaManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;

/**
 * Delete Media Action.
 */
final class DeleteMediaAction extends Action
{
    public function __construct(
        private readonly MediaManager $mediaManager,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        /** @var string|null $id */
        $id = $request->getAttribute('id');

        if (null === $id) {
            return new Response('Media ID required', 400);
        }

        if ('DELETE' === $request->getMethod()) {
            return $this->delete($id);
        }

        // Support form POST with _method override
        if ('POST' === $request->getMethod() && 'DELETE' === $request->getRequestParam('_method')) {
            return $this->delete($id);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(MediaManager::class),
        );
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
}
