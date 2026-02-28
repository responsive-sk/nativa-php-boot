<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Roles;

use Application\Services\RoleService;
use Application\Services\RbacService;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * List Roles Action
 */
final class ListRolesAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $roles = $this->roleService->getAllRoles();

        $content = $this->renderer->render(
            'admin/roles/index',
            ['title' => 'Roles Management', 'roles' => $roles],
            'admin/layouts/base'
        );

        return $this->html($content);
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
