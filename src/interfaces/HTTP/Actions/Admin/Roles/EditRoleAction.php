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
 * Edit Role Action
 */
final class EditRoleAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request, string $id): Response
    {
        $role = $this->roleService->findRoleById($id);

        if ($role === null) {
            return new Response('Role not found', 404);
        }

        $content = $this->renderer->render(
            'admin/roles/edit',
            ['title' => 'Edit Role', 'role' => $role, 'error' => null],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function update(Request $request, string $id): Response
    {
        try {
            $description = $request->request->get('description', '');

            $this->roleService->updateRoleDescription($id, $description);

            return $this->redirect('/admin/roles');
        } catch (\RuntimeException $e) {
            $role = $this->roleService->findRoleById($id);

            $content = $this->renderer->render(
                'admin/roles/edit',
                ['title' => 'Edit Role', 'role' => $role, 'error' => $e->getMessage()],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        }
    }

    public function destroy(Request $request, string $id): Response
    {
        try {
            $this->roleService->deleteRole($id);
            return $this->redirect('/admin/roles');
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
            $container->get(RoleService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
