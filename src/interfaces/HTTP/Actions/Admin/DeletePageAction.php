<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Delete Page Action
 */
class DeletePageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $this->param($request, 'id');
            $this->pageManager->delete($id);

            return $this->json(['success' => true]);

        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(PageManager::class),
        );
    }
}
