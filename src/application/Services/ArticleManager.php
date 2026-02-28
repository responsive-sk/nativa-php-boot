<?php

declare(strict_types=1);

namespace Application\Services;

use Application\DTOs\CreateArticleCommand;
use Application\DTOs\UpdateArticleCommand;
use Domain\Events\EventDispatcherInterface;
use Domain\Model\Article;
use Domain\Repository\ArticleRepositoryInterface;
use Infrastructure\Queue\Handlers\OutboxProcessor;

/**
 * Article Manager - Handles article operations and events
 */
final class ArticleManager
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?OutboxProcessor $outboxProcessor = null,
    ) {
    }

    /**
     * Create article (for CQRS compatibility)
     *
     * @param array<string>|null $tags
     * @return Article
     */
    public function create(
        string $title,
        string $content,
        string $authorId,
        ?string $categoryId = null,
        ?string $excerpt = null,
        ?array $tags = null,
        ?string $image = null,
    ): Article {
        $article = Article::create(
            title: $title,
            content: $content,
            authorId: $authorId,
            categoryId: $categoryId,
            excerpt: $excerpt,
            image: $image,
        );

        if ($tags !== null) {
            $article->setTags($tags);
        }

        $this->articleRepository->save($article);
        $this->dispatchEvents($article);

        return $article;
    }

    public function createFromCommand(CreateArticleCommand $command): Article
    {
        return $this->create(
            title: $command->title,
            content: $command->content,
            authorId: $command->authorId,
            categoryId: $command->categoryId,
            excerpt: $command->excerpt,
            tags: $command->tags,
            image: $command->image,
        );
    }

    public function updateFromCommand(UpdateArticleCommand $command): Article
    {
        $article = $this->articleRepository->findById($command->articleId);

        if ($article === null) {
            throw new \RuntimeException('Article not found');
        }

        $article->update(
            title: $command->title,
            content: $command->content,
            excerpt: $command->excerpt,
            categoryId: $command->categoryId,
            image: $command->image,
        );

        if ($command->tags !== null) {
            $article->setTags($command->tags);
        }

        $this->articleRepository->save($article);
        $this->dispatchEvents($article);

        return $article;
    }

    public function publish(string $articleId): Article
    {
        $article = $this->articleRepository->findById($articleId);

        if ($article === null) {
            throw new \RuntimeException('Article not found');
        }

        $article->publish();
        $this->articleRepository->save($article);
        $this->dispatchEvents($article);

        return $article;
    }

    public function unpublish(string $articleId): Article
    {
        $article = $this->articleRepository->findById($articleId);

        if ($article === null) {
            throw new \RuntimeException('Article not found');
        }

        $article->unpublish();
        $this->articleRepository->save($article);
        $this->dispatchEvents($article);

        return $article;
    }

    public function delete(string $articleId): void
    {
        $article = $this->articleRepository->findById($articleId);

        if ($article === null) {
            throw new \RuntimeException('Article not found');
        }

        // Record delete event before deletion
        $article->recordDeleteEvent();

        $this->dispatchEvents($article);
        $this->articleRepository->delete($articleId);
    }

    public function findById(string $articleId): ?Article
    {
        return $this->articleRepository->findById($articleId);
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->articleRepository->findBySlug($slug);
    }

    public function update(string $articleId, string $title, string $content, ?string $excerpt = null, ?string $categoryId = null, ?string $image = null): Article
    {
        $article = $this->articleRepository->findById($articleId);

        if ($article === null) {
            throw new \RuntimeException('Article not found');
        }

        $article->update(
            title: $title,
            content: $content,
            excerpt: $excerpt,
            categoryId: $categoryId,
            image: $image,
        );

        $this->articleRepository->save($article);
        $this->dispatchEvents($article);

        return $article;
    }

    /**
     * @return array<Article>
     */
    public function findByTag(string $tag, int $limit = 10, int $offset = 0): array
    {
        return $this->articleRepository->findByTag($tag, $limit, $offset);
    }

    /**
     * @return array<Article>
     */
    public function listPublished(int $limit = 10, int $offset = 0): array
    {
        return $this->articleRepository->findPublished($limit, $offset);
    }

    /**
     * @return array<Article>
     */
    public function listLatest(int $limit = 5): array
    {
        return $this->articleRepository->findLatest($limit);
    }

    /**
     * @return array<Article>
     */
    public function search(string $query): array
    {
        return $this->articleRepository->search($query);
    }

    public function countPublished(): int
    {
        return $this->articleRepository->countPublished();
    }

    /**
     * Dispatch all pending events from entity
     */
    private function dispatchEvents(Article $article): void
    {
        foreach ($article->releaseEvents() as $event) {
            // Dispatch synchronously
            $this->eventDispatcher->dispatch($event);

            // Also add to outbox for async processing
            if ($this->outboxProcessor !== null) {
                $this->outboxProcessor->add(
                    get_class($event),
                    $event->payload()
                );
            }
        }
    }
}
