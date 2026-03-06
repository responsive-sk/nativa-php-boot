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
 * List Roles Action.
 */
final class ListRolesAction extends Action
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $roles = $this->roleService->getAllRoles();

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/roles/index',
            ['title' => 'Roles Management', 'roles' => $roles],
            'admin'
        );
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
