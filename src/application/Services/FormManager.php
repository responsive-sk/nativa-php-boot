<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\Form;
use Domain\Repository\FormRepositoryInterface;

/**
 * Form Manager
 */
class FormManager
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository,
    ) {
    }

    /**
     * Create a new form
     */
    public function create(
        string $name,
        string $slug,
        array $schema,
        ?string $emailNotification = null,
        ?string $successMessage = null,
    ): Form {
        $form = Form::create($name, $slug, $schema, $emailNotification, $successMessage);
        $this->formRepository->save($form);
        return $form;
    }

    /**
     * Update form
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

    /**
     * Delete form
     */
    public function delete(string $formId): void
    {
        $this->formRepository->delete($formId);
    }

    /**
     * Find form by ID
     */
    public function findById(string $id): ?Form
    {
        return $this->formRepository->findById($id);
    }

    /**
     * Find form by slug
     */
    public function findBySlug(string $slug): ?Form
    {
        return $this->formRepository->findBySlug($slug);
    }

    /**
     * Get all forms
     *
     * @return array<Form>
     */
    public function findAll(): array
    {
        return $this->formRepository->findAll();
    }
}
