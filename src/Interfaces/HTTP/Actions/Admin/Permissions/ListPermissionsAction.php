<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Permissions;

use Application\Services\PermissionService;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * List Permissions Action.
 */
final class ListPermissionsAction extends Action
{
    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $group = $request->getQueryParam('group', '');

        if (!empty($group)) {
            $permissions = $this->permissionService->getPermissionsByGroup($group);
        } else {
            $permissions = $this->permissionService->getAllPermissions();
        }

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/permissions/index',
            [
                'title'        => 'Permissions Management',
                'permissions'  => $permissions,
                'currentGroup' => $group,
            ],
            'admin'
        );
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
