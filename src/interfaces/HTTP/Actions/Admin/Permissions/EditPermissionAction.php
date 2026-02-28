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
 * Edit Permission Action
 */
final class EditPermissionAction extends Action
{
    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request, string $id): Response
    {
        $permission = $this->permissionService->findPermissionById($id);

        if ($permission === null) {
            return new Response('Permission not found', 404);
        }

        $content = $this->renderer->render(
            'admin/permissions/edit',
            ['title' => 'Edit Permission', 'permission' => $permission, 'error' => null],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function update(Request $request, string $id): Response
    {
        try {
            $description = $request->request->get('description', '');

            $this->permissionService->updatePermissionDescription($id, $description);

            return $this->redirect('/admin/permissions');
        } catch (\RuntimeException $e) {
            $permission = $this->permissionService->findPermissionById($id);

            $content = $this->renderer->render(
                'admin/permissions/edit',
                ['title' => 'Edit Permission', 'permission' => $permission, 'error' => $e->getMessage()],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        }
    }

    public function destroy(Request $request, string $id): Response
    {
        try {
            $this->permissionService->deletePermission($id);
            return $this->redirect('/admin/permissions');
        } catch (\RuntimeException $e) {
            return new Response($e->getMessage(), 400);
        }
    }

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        
        if ($request->getMethod() === 'GET') {
            return $this->show($request, $id);
        }

        if ($request->getMethod() === 'POST') {
            return $this->update($request, $id);
        }

        if ($request->getMethod() === 'DELETE') {
            return $this->destroy($request, $id);
        }

        return new Response('Method not allowed', 405);
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
