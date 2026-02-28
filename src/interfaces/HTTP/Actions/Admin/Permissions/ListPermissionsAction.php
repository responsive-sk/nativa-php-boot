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
 * List Permissions Action
 */
final class ListPermissionsAction extends Action
{
    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $group = $request->query->get('group', '');
        
        if (!empty($group)) {
            $permissions = $this->permissionService->getPermissionsByGroup($group);
        } else {
            $permissions = $this->permissionService->getAllPermissions();
        }

        $content = $this->renderer->render(
            'admin/permissions/index',
            [
                'title' => 'Permissions Management',
                'permissions' => $permissions,
                'currentGroup' => $group,
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
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
