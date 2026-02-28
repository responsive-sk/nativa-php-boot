<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\MediaManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Media Library Action
 */
final class MediaAction extends Action
{
    public function __construct(
        private readonly MediaManager $mediaManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->upload($request);
        }
        return $this->index($request);
    }

    private function index(Request $request): Response
    {
        $media = $this->mediaManager->findAll(50, 0);

        $content = $this->renderer->render(
            'admin/pages/media/index',
            [
                'title' => 'Media Library',
                'media' => $media,
                'provider' => $this->mediaManager->getProviderName(),
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function upload(Request $request): Response
    {
        // Use $_FILES directly instead of Symfony's UploadedFile
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES['file']) ? $this->getUploadErrorMessage($_FILES['file']['error']) : 'No file uploaded';
            return $this->json(['error' => $error], 400);
        }

        try {
            $result = $this->mediaManager->upload($_FILES['file']);
            
            // Return duplicate flag for frontend handling
            return $this->json($result);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload',
            default => 'Unknown upload error',
        };
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(MediaManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
