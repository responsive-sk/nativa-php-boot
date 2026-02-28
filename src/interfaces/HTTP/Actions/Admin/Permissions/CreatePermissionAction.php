<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Permissions;

use Application\Services\PermissionService;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Create Permission Action
 */
final class CreatePermissionAction extends Action
{
    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/permissions/create',
            ['title' => 'Create Permission', 'error' => null],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function store(Request $request): Response
    {
        try {
            $name = $request->request->get('name', '');
            $description = $request->request->get('description', '');
            $group = $request->request->get('group', 'default');

            if (empty($name)) {
                throw new \InvalidArgumentException('Permission name is required');
            }

            // Validate permission name format (resource.action or resource.action.subaction)
            if (!preg_match('/^[a-z]+(\.[a-z_]+)+$/', $name)) {
                throw new \InvalidArgumentException(
                    'Invalid permission format. Use "resource.action" (e.g., "admin.dashboard", "admin.users.manage")'
                );
            }

            $this->permissionService->createPermission($name, $description, $group);

            return $this->redirect('/admin/permissions');
        } catch (\InvalidArgumentException $e) {
            $content = $this->renderer->render(
                'admin/permissions/create',
                [
                    'title' => 'Create Permission',
                    'error' => $e->getMessage(),
                    'old' => [
                        'name' => $request->request->get('name', ''),
                        'description' => $request->request->get('description', ''),
                        'group' => $request->request->get('group', ''),
                    ],
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        } catch (\RuntimeException $e) {
            $content = $this->renderer->render(
                'admin/permissions/create',
                [
                    'title' => 'Create Permission',
                    'error' => $e->getMessage(),
                    'old' => [
                        'name' => $request->request->get('name', ''),
                        'description' => $request->request->get('description', ''),
                        'group' => $request->request->get('group', ''),
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
            $container->get(PermissionService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
