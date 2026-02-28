<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\Page;
use Domain\Repository\PageRepositoryInterface;
use Domain\ValueObjects\Slug;

/**
 * Page Manager
 */
final class PageManager
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    /**
     * Create a new page
     */
    public function create(
        string $title,
        string $content,
        string $template = 'default',
        ?string $metaTitle = null,
        ?string $metaDescription = null,
        bool $isPublished = false,
    ): Page {
        // Validate slug is not reserved
        $slug = Slug::fromString($title);
        $this->validateSlug($slug->value());
        
        $page = Page::create($title, $content, $template, $metaTitle, $metaDescription);
        
        if ($isPublished) {
            $page->publish();
        }
        
        $this->pageRepository->save($page);
        
        return $page;
    }

    /**
     * Validate slug doesn't conflict with reserved routes
     */
    private function validateSlug(string $slug): void
    {
        $reserved = [
            'admin', 'articles', 'article', 'tags', 'tag', 'search',
            'contact', 'form', 'forms', 'page', 'pages',
            'api', 'storage', 'static', 'assets', 'public',
            'login', 'logout', 'register', 'signup', 'signin',
            'dashboard', 'profile', 'settings', 'account',
        ];

        if (in_array(strtolower($slug), $reserved)) {
            throw new \RuntimeException("Slug '{$slug}' is reserved and cannot be used");
        }

        // Check if page with this slug already exists
        $existing = $this->pageRepository->findBySlug($slug);
        if ($existing) {
            throw new \RuntimeException("Page with slug '{$slug}' already exists");
        }
    }

    /**
     * Update an existing page
     */
    public function update(
        string $pageId,
        ?string $title = null,
        ?string $content = null,
        ?string $template = null,
        ?string $metaTitle = null,
        ?string $metaDescription = null,
    ): Page {
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw new \RuntimeException('Page not found');
        }

        $page->update($title, $content, $template, $metaTitle, $metaDescription);
        $this->pageRepository->save($page);

        return $page;
    }

    /**
     * Publish a page
     */
    public function publish(string $pageId): Page
    {
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw new \RuntimeException('Page not found');
        }

        $page->publish();
        $this->pageRepository->save($page);

        return $page;
    }

    /**
     * Unpublish a page
     */
    public function unpublish(string $pageId): Page
    {
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw new \RuntimeException('Page not found');
        }

        $page->unpublish();
        $this->pageRepository->save($page);

        return $page;
    }

    /**
     * Delete a page
     */
    public function delete(string $pageId): void
    {
        $this->pageRepository->delete($pageId);
    }

    /**
     * Find page by ID
     */
    public function findById(string $id): ?Page
    {
        return $this->pageRepository->findById($id);
    }

    /**
     * Find page by slug
     */
    public function findBySlug(string $slug): ?Page
    {
        return $this->pageRepository->findBySlug($slug);
    }

    /**
     * Get all pages
     *
     * @return array<Page>
     */
    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return $this->pageRepository->findAll($limit, $offset);
    }

    /**
     * Get published pages
     *
     * @return array<Page>
     */
    public function findPublished(int $limit = 50): array
    {
        return $this->pageRepository->findPublished($limit);
    }

    /**
     * Count all pages
     */
    public function count(): int
    {
        return $this->pageRepository->count();
    }

    /**
     * Add content block to page
     */
    public function addBlock(
        string $pageId,
        string $type,
        ?string $title = null,
        ?string $content = null,
        array $data = [],
        int $sortOrder = 0,
    ): \Domain\Model\PageBlock {
        $block = \Domain\Model\PageBlock::create($pageId, $type, $title, $content, $data, $sortOrder);
        // TODO: Save block via BlockRepository
        return $block;
    }

    /**
     * Attach media to page
     */
    public function attachMedia(
        string $pageId,
        string $mediaId,
        ?string $caption = null,
        int $sortOrder = 0,
    ): \Domain\Model\PageMedia {
        $pageMedia = \Domain\Model\PageMedia::create($pageId, $mediaId, $caption, $sortOrder);
        // TODO: Save via MediaRepository
        return $pageMedia;
    }

    /**
     * Embed form on page
     */
    public function embedForm(
        string $pageId,
        string $formId,
        ?string $title = null,
        string $position = 'sidebar',
        int $sortOrder = 0,
    ): \Domain\Model\PageForm {
        $pageForm = \Domain\Model\PageForm::create($pageId, $formId, $title, $position, $sortOrder);
        // TODO: Save via FormRepository
        return $pageForm;
    }

    /**
     * Get page with all relationships
     */
    public function getPageWithRelations(string $pageId): array
    {
        $page = $this->pageRepository->findById($pageId);
        
        if ($page === null) {
            return [];
        }

        return [
            'page' => $page,
            'blocks' => $this->pageRepository->getBlocks($pageId),
            'media' => $this->pageRepository->getMedia($pageId),
            'forms' => $this->pageRepository->getForms($pageId),
        ];
    }
}
