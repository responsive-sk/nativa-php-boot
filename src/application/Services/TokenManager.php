<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\User;
use Domain\Repository\UserRepositoryInterface;

/**
 * Token Manager
 *
 * Handles remember me tokens and password reset tokens
 */
final class TokenManager
{
    private const REMEMBER_COOKIE_NAME = 'remember_token';
    private const REMEMBER_TOKEN_LENGTH = 64;

    // TODO: Inject repository when implemented
    // private readonly PasswordResetRepositoryInterface $passwordResetRepository,
    // private readonly RememberTokenRepositoryInterface $rememberTokenRepository,

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Create remember me token for user
     */
    public function createRememberToken(User $user): string
    {
        $this->logDebug('[TokenManager] Creating remember token for user: ' . $user->id());

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + (int) ($_ENV['AUTH_REMEMBER_LIFETIME'] ?? '2592000')); // 30 days

        // TODO: Save to database
        // $this->rememberTokenRepository->create($user->id(), $token, $expiresAt);

        // Set cookie
        $this->setRememberCookie($token, strtotime($expiresAt));

        $this->logDebug('[TokenManager] Remember token created');

        return $token;
    }

    /**
     * Check remember me token and return user ID if valid
     */
    public function checkRememberToken(): ?string
    {
        $token = $this->getRememberCookie();

        if ($token === null) {
            return null;
        }

        $this->logDebug('[TokenManager] Checking remember token');

        // TODO: Validate token from database
        // $rememberToken = $this->rememberTokenRepository->findByToken($token);
        //
        // if ($rememberToken === null || $rememberToken->isExpired()) {
        //     $this->clearRememberCookie();
        //     return null;
        // }
        //
        // return $rememberToken->userId();

        // For now, return null - remember me requires database
        return null;
    }

    /**
     * Clear remember me token
     */
    public function clearRememberToken(): void
    {
        $this->logDebug('[TokenManager] Clearing remember token');
        $this->clearRememberCookie();

        // TODO: Delete from database
        // $token = $this->getRememberCookie();
        // if ($token !== null) {
        //     $this->rememberTokenRepository->deleteByToken($token);
        // }
    }

    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate password reset token
     * Returns user ID if valid
     */
    public function validatePasswordResetToken(string $token): ?string
    {
        $this->logDebug('[TokenManager] Validating password reset token');

        // TODO: Validate from database
        // $resetToken = $this->passwordResetRepository->findByToken($token);
        //
        // if ($resetToken === null || $resetToken->isExpired() || $resetToken->isUsed()) {
        //     return null;
        // }
        //
        // return $resetToken->userId();

        return null;
    }

    /**
     * Mark password reset token as used
     */
    public function markPasswordResetTokenUsed(string $token): void
    {
        $this->logDebug('[TokenManager] Marking password reset token as used');

        // TODO: Update database
        // $this->passwordResetRepository->markAsUsed($token);
    }

    /**
     * Set remember me cookie
     */
    private function setRememberCookie(string $token, int $expiresAt): void
    {
        $secure = ($_ENV['SESSION_SECURE_COOKIE'] ?? 'false') === 'true';
        $httpOnly = true;
        $sameSite = ($_ENV['SESSION_SAME_SITE'] ?? 'Lax');

        setcookie(
            self::REMEMBER_COOKIE_NAME,
            $token,
            [
                'expires' => $expiresAt,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite,
            ]
        );
    }

    /**
     * Get remember me cookie value
     */
    private function getRememberCookie(): ?string
    {
        return $_COOKIE[self::REMEMBER_COOKIE_NAME] ?? null;
    }

    /**
     * Clear remember me cookie
     */
    private function clearRememberCookie(): void
    {
        setcookie(
            self::REMEMBER_COOKIE_NAME,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    /**
     * Debug logging
     */
    private function logDebug(string $message): void
    {
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            error_log($message);
        }
    }
}
