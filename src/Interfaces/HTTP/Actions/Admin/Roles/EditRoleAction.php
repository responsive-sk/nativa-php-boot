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
 * Edit Role Action.
 */
final class EditRoleAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function show(Request $request, string $id): Response
    {
        $role = $this->roleService->findRoleById($id);

        if (null === $role) {
            return new Response('Role not found', 404);
        }

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/roles/edit',
            ['title' => 'Edit Role', 'role' => $role, 'error' => null],
            'admin'
        );
    }

    public function update(Request $request, string $id): Response
    {
        try {
            $description = $request->getRequestParam('description', '');

            $this->roleService->updateRoleDescription($id, $description);

            return $this->redirect('/admin/roles');
        } catch (\RuntimeException $e) {
            $role = $this->roleService->findRoleById($id);

            return $this->renderPage(
                $request,
                $this->renderer,
                'admin/roles/edit',
                ['title' => 'Edit Role', 'role' => $role, 'error' => $e->getMessage()],
                'admin',
                400
            );
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

    #[\Override]
    public function handle(Request $request): Response
    {
        $id = $request->getAttribute('id');

        if ('GET' === $request->getMethod()) {
            return $this->show($request, $id);
        }

        if ('POST' === $request->getMethod()) {
            return $this->update($request, $id);
        }

        if ('DELETE' === $request->getMethod()) {
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
