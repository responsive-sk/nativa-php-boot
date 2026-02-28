<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Roles;

use Application\Services\RoleService;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Create Role Action
 */
final class CreateRoleAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/roles/create',
            ['title' => 'Create Role', 'error' => null],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function store(Request $request): Response
    {
        try {
            $name = $request->request->get('name', '');
            $description = $request->request->get('description', '');

            if (empty($name)) {
                throw new \InvalidArgumentException('Role name is required');
            }

            $this->roleService->createRole($name, $description);

            return $this->redirect('/admin/roles');
        } catch (\InvalidArgumentException $e) {
            $content = $this->renderer->render(
                'admin/roles/create',
                [
                    'title' => 'Create Role',
                    'error' => $e->getMessage(),
                    'old' => [
                        'name' => $request->request->get('name', ''),
                        'description' => $request->request->get('description', ''),
                    ],
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        } catch (\RuntimeException $e) {
            $content = $this->renderer->render(
                'admin/roles/create',
                [
                    'title' => 'Create Role',
                    'error' => $e->getMessage(),
                    'old' => [
                        'name' => $request->request->get('name', ''),
                        'description' => $request->request->get('description', ''),
                    ],
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        }
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
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
