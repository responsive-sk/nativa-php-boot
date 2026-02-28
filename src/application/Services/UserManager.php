<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Events\EventDispatcherInterface;
use Domain\Model\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Password;
use Domain\ValueObjects\Role;

/**
 * User Manager - Handles user operations
 */
final class UserManager
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(
        string $name,
        string $email,
        string $password,
        string $role = 'user',
        ?string $avatar = null,
    ): User {
        $user = User::create(
            name: $name,
            email: new Email($email),
            password: new Password($password),
            role: new Role($role),
            avatar: $avatar,
        );

        $this->userRepository->save($user);

        return $user;
    }

    public function update(
        string $userId,
        ?string $name = null,
        ?string $email = null,
        ?string $avatar = null,
    ): User {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $user->update(
            name: $name,
            email: $email,
            avatar: $avatar,
        );

        $this->userRepository->save($user);

        return $user;
    }

    public function changePassword(string $userId, string $newPassword): User
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $user->changePassword(new Password($newPassword));
        $this->userRepository->save($user);

        return $user;
    }

    public function delete(string $userId): void
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user === null) {
            throw new \RuntimeException('User not found');
        }
        
        $this->userRepository->delete($user);
    }

    public function findById(string $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function findByRole(string $role): array
    {
        return $this->userRepository->findByRole($role);
    }

    public function count(): int
    {
        return $this->userRepository->count();
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user->password()->hash())) {
            return null;
        }

        return $user;
    }
}
