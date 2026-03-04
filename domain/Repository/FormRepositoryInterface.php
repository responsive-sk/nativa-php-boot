<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Form;

/**
 * Form Repository Interface
 */
interface FormRepositoryInterface
{
    public function save(Form $form): void;

    public function delete(string $id): void;

    public function findById(string $id): ?Form;

    public function findBySlug(string $slug): ?Form;

    public function findAll(): array;

    public function count(): int;
}
