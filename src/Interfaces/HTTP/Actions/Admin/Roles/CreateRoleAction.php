<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Roles;

use Application\Services\RoleService;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Create Role Action.
 */
final class CreateRoleAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function show(Request $request): Response
    {
        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/roles/create',
            ['title' => 'Create Role', 'error' => null],
            'admin'
        );
    }

    public function store(Request $request): Response
    {
        try {
            $name = $request->getRequestParam('name', '');
            $description = $request->getRequestParam('description', '');

            if (empty($name)) {
                throw new \InvalidArgumentException('Role name is required');
            }

            $this->roleService->createRole($name, $description);

            return $this->redirect('/admin/roles');
        } catch (\InvalidArgumentException $e) {
            return $this->renderPage(
                $request,
                $this->renderer,
                'admin/roles/create',
                [
                    'title' => 'Create Role',
                    'error' => $e->getMessage(),
                    'old'   => [
                        'name'        => $request->getRequestParam('name', ''),
                        'description' => $request->getRequestParam('description', ''),
                    ],
                ],
                'admin',
                400
            );
        } catch (\RuntimeException $e) {
            return $this->renderPage(
                $request,
                $this->renderer,
                'admin/roles/create',
                [
                    'title' => 'Create Role',
                    'error' => $e->getMessage(),
                    'old'   => [
                        'name'        => $request->getRequestParam('name', ''),
                        'description' => $request->getRequestParam('description', ''),
                    ],
                ],
                'admin',
                400
            );
        }
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('GET' === $request->getMethod()) {
            return $this->show($request);
        }

        return $this->store($request);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(RoleService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
