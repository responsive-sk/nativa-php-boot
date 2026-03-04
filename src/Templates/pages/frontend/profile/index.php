<?php declare(strict_types = 1);

use App\Domain\User\User;

/**
 * @var User   $user
 * @var string $profileUpdateUrl
 * @var string $changePasswordUrl
 * @var string $logoutUrl
 * @var string $blogUrl
 * @var string $csrfToken
 */
$userInitials = strtoupper(substr($user->name ?? 'U', 0, 2));
?>

<section class="profile">
    <div class="profile__container">
        <div class="profile__header">
            <h1 class="profile__title">My Profile</h1>
            <p class="profile__subtitle">Manage your account settings</p>
        </div>

        <div class="profile__card">
            <div class="profile__user">
                <div class="profile__avatar"><?php echo htmlspecialchars($userInitials); ?></div>
                <div class="profile__user-info">
                    <h2 class="profile__user-name"><?php echo htmlspecialchars($user->name ?? 'User'); ?></h2>
                    <p class="profile__user-login">@<?php echo htmlspecialchars($user->login); ?></p>
                </div>
            </div>
        </div>

        <div class="profile__actions">
            <a href="<?php echo htmlspecialchars($profileUpdateUrl); ?>" class="profile__action">
                <div class="profile__action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </div>
                <div class="profile__action-content">
                    <h3 class="profile__action-title">Update Profile</h3>
                    <p class="profile__action-desc">Change your name and other details</p>
                </div>
                <span class="profile__action-arrow"></span>
            </a>

            <a href="<?php echo htmlspecialchars($changePasswordUrl); ?>" class="profile__action">
                <div class="profile__action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div class="profile__action-content">
                    <h3 class="profile__action-title">Change Password</h3>
                    <p class="profile__action-desc">Update your security password</p>
                </div>
                <span class="profile__action-arrow"></span>
            </a>

            <a href="<?php echo htmlspecialchars($blogUrl); ?>" class="profile__action">
                <div class="profile__action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div class="profile__action-content">
                    <h3 class="profile__action-title">My Articles</h3>
                    <p class="profile__action-desc">View your published articles</p>
                </div>
                <span class="profile__action-arrow"></span>
            </a>
        </div>

        <div class="profile__logout">
            <form method="post" action="<?php echo htmlspecialchars($logoutUrl); ?>">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">
                <button type="submit" class="btn btn--outline profile__logout-btn">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</section>
