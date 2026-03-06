<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin;

use Domain\Repository\FormSubmissionRepositoryInterface;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Admin Form Submissions List Action.
 */
final class FormSubmissionsAction extends Action
{
    public function __construct(
        private readonly FormSubmissionRepositoryInterface $submissionRepository,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $formId = (string) $this->param($request, 'id');
        $page = max(1, (int) $request->getQueryParam('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $submissions = $this->submissionRepository->findByFormId($formId, $limit, $offset);
        $total = $this->submissionRepository->countByFormId($formId);
        $totalPages = (int) ceil($total / $limit);

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/pages/forms/submissions',
            [
                'title'       => 'Form Submissions',
                'submissions' => $submissions,
                'currentPage' => $page,
                'totalPages'  => $totalPages,
            ],
            'admin'
        );
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(FormSubmissionRepositoryInterface::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
