<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\Form;
use Domain\Repository\FormRepositoryInterface;

/**
 * Form Application Service
 */
final class FormAppService
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $schema
     */
    public function create(
        string $name,
        string $slug,
        array $schema,
        ?string $emailNotification = null,
        string $successMessage = 'Thank you for your submission!',
    ): Form {
        $form = Form::create($name, $slug, $schema, $emailNotification, $successMessage);
        $this->formRepository->save($form);
        return $form;
    }

    /**
     * @param array<int, array<string, mixed>>|null $schema
     */
    public function update(
        string $formId,
        ?string $name = null,
        ?array $schema = null,
        ?string $emailNotification = null,
        ?string $successMessage = null,
    ): Form {
        $form = $this->formRepository->findById($formId);

        if ($form === null) {
            throw new \RuntimeException('Form not found');
        }

        $form->update($name, $schema, $emailNotification, $successMessage);
        $this->formRepository->save($form);

        return $form;
    }

    public function delete(string $formId): void
    {
        $this->formRepository->delete($formId);
    }

    public function findById(string $formId): ?Form
    {
        return $this->formRepository->findById($formId);
    }

    public function findBySlug(string $slug): ?Form
    {
        return $this->formRepository->findBySlug($slug);
    }

    /**
     * @return array<Form>
     */
    public function listAll(): array
    {
        return $this->formRepository->findAll();
    }

    public function count(): int
    {
        return $this->formRepository->count();
    }
}
