<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Contact;

/**
 * Contact Repository Interface
 */
interface ContactRepositoryInterface
{
    /**
     * Save contact
     */
    public function save(Contact $contact): void;

    /**
     * Find contact by ID
     */
    public function findById(string $id): ?Contact;

    /**
     * Find all contacts
     *
     * @return array<Contact>
     */
    public function findAll(int $limit = 50, int $offset = 0): array;

    /**
     * Find contacts by status
     *
     * @return array<Contact>
     */
    public function findByStatus(string $status, int $limit = 50): array;

    /**
     * Count contacts by status
     */
    public function countByStatus(string $status): int;

    /**
     * Count all contacts
     */
    public function count(): int;
}
