<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObjects\Password;
use Domain\Events\UserLoggedIn;
use Domain\Events\UserLoggedOut;
use Domain\Events\UserRegistered;
use Application\DTOs\CreateUserCommand;
use Application\DTOs\LoginCommand;
use Application\DTOs\UpdateUserCommand;
use Application\DTOs\PasswordResetCommand;
use Domain\Events\PasswordChanged;
use Domain\Events\PasswordResetRequested;
use Domain\Events\DomainEventInterface;
use Domain\Events\EventDispatcherInterface;

/**
 * Authentication Service
 *
 * Handles user authentication, registration, and session management
 */
class AuthService
{
    private const SESSION_KEY = 'auth_user_id';
    private const SESSION_KEY_EMAIL = 'auth_user_email';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SessionManager $sessionManager,
        private readonly TokenManager $tokenManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Attempt to authenticate user with email and password
     */
    public function attempt(LoginCommand $command, string $ipAddress): bool
    {
        $this->logDebug('[AuthService::attempt] Starting authentication for email: ' . $command->email);

        $user = $this->userRepository->findByEmail($command->email);

        if ($user === null) {
            $this->logDebug('[AuthService::attempt] User not found');
            return false;
        }

        if (!$user->isActive()) {
            $this->logDebug('[AuthService::attempt] User is not active');
            return false;
        }

        if (!$user->password()->verify($command->password)) {
            $this->logDebug('[AuthService::attempt] Invalid password');
            return false;
        }

        // Authentication successful
        $this->login($user, $command->rememberMe, $ipAddress);
        $this->logDebug('[AuthService::attempt] Authentication successful');

        return true;
    }

    /**
     * Login user and create session
     */
    public function login(User $user, bool $rememberMe = false, ?string $ipAddress = null): void
    {
        $this->logDebug('[AuthService::login] Logging in user: ' . $user->id());

        // Record login
        if ($ipAddress !== null) {
            $user->recordLogin($ipAddress);
            $this->userRepository->save($user);
        }

        // Start session and store user ID
        $this->sessionManager->start();
        $this->sessionManager->set(self::SESSION_KEY, $user->id());
        $this->sessionManager->set(self::SESSION_KEY_EMAIL, $user->emailString());

        // Create remember me token if requested
        if ($rememberMe) {
            $this->tokenManager->createRememberToken($user);
        }

        // Dispatch event
        $this->eventDispatcher->dispatch(
            UserLoggedIn::create($user->id(), $user->emailString(), $ipAddress ?? 'unknown')
        );

        $this->logDebug('[AuthService::login] Login complete');
    }

    /**
     * Logout current user
     */
    public function logout(): void
    {
        $user = $this->user();

        if ($user !== null) {
            $this->logDebug('[AuthService::logout] Logging out user: ' . $user->id());

            // Dispatch event
            $this->eventDispatcher->dispatch(
                UserLoggedOut::create($user->id(), $user->emailString())
            );
        }

        // Clear remember me token
        $this->tokenManager->clearRememberToken();

        // Destroy session
        $this->sessionManager->destroy();

        $this->logDebug('[AuthService::logout] Logout complete');
    }

    /**
     * Get current authenticated user
     */
    public function user(): ?User
    {
        $userId = $this->sessionManager->get(self::SESSION_KEY);

        if ($userId === null) {
            // Try remember me token
            $userId = $this->tokenManager->checkRememberToken();

            if ($userId !== null) {
                $this->logDebug('[AuthService::user] User authenticated via remember token');
            } else {
                return null;
            }
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null || !$user->isActive()) {
            $this->logout();
            return null;
        }

        $this->logDebug('[AuthService::user] Current user: ' . $user->id());

        return $user;
    }

    /**
     * Check if user is authenticated
     */
    public function check(): bool
    {
        $isAuth = $this->user() !== null;
        $this->logDebug('[AuthService::check] Is authenticated: ' . ($isAuth ? 'yes' : 'no'));
        return $isAuth;
    }

    /**
     * Get guest user (not authenticated)
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Register new user
     */
    public function register(CreateUserCommand $command): User
    {
        $this->logDebug('[AuthService::register] Registering user: ' . $command->email);

        // Check if email already exists
        if ($this->userRepository->emailExists($command->email)) {
            throw new \RuntimeException('Email already registered');
        }

        // Create user with value objects
        $valueObjects = $command->toValueObjects();
        $user = User::create(
            name: $command->name,
            email: $valueObjects['email'],
            password: $valueObjects['password'],
            role: $valueObjects['role'],
            avatar: $command->avatar,
        );

        $this->userRepository->save($user);

        // Dispatch event
        $this->eventDispatcher->dispatch(
            UserRegistered::create($user->id(), $user->emailString(), $user->name(), $user->roleString())
        );

        $this->logDebug('[AuthService::register] Registration successful');

        return $user;
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateUserCommand $command): User
    {
        $this->logDebug('[AuthService::updateProfile] Updating user: ' . $command->userId);

        $user = $this->userRepository->findById($command->userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        // Check email uniqueness if changing email
        if ($command->email !== null && $command->email !== $user->emailString()) {
            if ($this->userRepository->emailExists($command->email, $command->userId)) {
                throw new \RuntimeException('Email already in use');
            }
        }

        $user->update(
            name: $command->name,
            email: $command->email,
            avatar: $command->avatar,
        );

        $this->userRepository->save($user);

        $this->logDebug('[AuthService::updateProfile] Profile updated');

        return $user;
    }

    /**
     * Change user password
     */
    public function changePassword(string $userId, string $currentPassword, string $newPassword): void
    {
        $this->logDebug('[AuthService::changePassword] Changing password for user: ' . $userId);

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        // Verify current password
        if (!$user->password()->verify($currentPassword)) {
            throw new \RuntimeException('Current password is incorrect');
        }

        // Set new password
        $user->changePassword(Password::fromPlain($newPassword));
        $this->userRepository->save($user);

        // Dispatch event
        $this->eventDispatcher->dispatch(
            PasswordChanged::create($user->id(), $user->emailString())
        );

        $this->logDebug('[AuthService::changePassword] Password changed successfully');
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email): string
    {
        $this->logDebug('[AuthService::requestPasswordReset] Request for: ' . $email);

        $user = $this->userRepository->findByEmail($email);

        // Always return true to prevent email enumeration
        if ($user === null) {
            $this->logDebug('[AuthService::requestPasswordReset] User not found, but returning success');
            return '';
        }

        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        // Store reset token (will be implemented in repository)
        // For now, just dispatch event
        $this->eventDispatcher->dispatch(
            PasswordResetRequested::create($user->id(), $user->emailString(), $resetToken)
        );

        $this->logDebug('[AuthService::requestPasswordReset] Reset token generated');

        return $resetToken;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(PasswordResetCommand $command): void
    {
        $this->logDebug('[AuthService::resetPassword] Resetting password with token');

        // TODO: Validate token from database
        // For now, just create new password
        // Token validation will be implemented with password_resets table

        throw new \RuntimeException('Password reset not yet implemented - requires database table');
    }

    /**
     * Get user by ID (admin function)
     */
    public function getUserById(string $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * Get all users (admin function)
     *
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Deactivate user (admin function)
     */
    public function deactivateUser(string $userId): void
    {
        $this->logDebug('[AuthService::deactivateUser] Deactivating user: ' . $userId);

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $user->deactivate();
        $this->userRepository->save($user);

        // If current user is deactivated, logout
        if ($this->sessionManager->get(self::SESSION_KEY) === $userId) {
            $this->logout();
        }

        $this->logDebug('[AuthService::deactivateUser] User deactivated');
    }

    /**
     * Activate user (admin function)
     */
    public function activateUser(string $userId): void
    {
        $this->logDebug('[AuthService::activateUser] Activating user: ' . $userId);

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $user->activate();
        $this->userRepository->save($user);

        $this->logDebug('[AuthService::activateUser] User activated');
    }

    /**
     * Debug logging helper
     */
    private function logDebug(string $message): void
    {
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            error_log('[AuthDebug] ' . $message);
        }
    }
}
