<?php
/**
 * Register Page.
 *
 * @var TemplateRenderer     $this
 * @var string               $title
 * @var string|null          $error
 * @var array<string, mixed> $old
 */

use Application\Middleware\CsrfMiddleware;
use Infrastructure\View\AssetHelper;
use Interfaces\HTTP\View\TemplateRenderer;

$authCss = AssetHelper::css('auth.css');
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - App</title>
    <link rel="stylesheet" href="<?php echo $authCss; ?>">
</head>
<body>
<!-- Theme Toggle -->
<div class="theme-toggle">
    <span class="theme-toggle__label">Theme</span>
    <button class="theme-toggle__button" id="themeToggle" aria-label="Toggle theme" type="button">
        <!-- Sun Icon (for light mode) -->
        <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <!-- Moon Icon (for dark mode) -->
        <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>
</div>

<script>
// Theme Toggle Logic
(function() {
    const toggle = document.getElementById('themeToggle');
    const body = document.body;
    const STORAGE_KEY = 'theme';

    // Check for saved theme preference or default to dark
    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;

    if (savedTheme === 'light' || (!savedTheme && prefersLight)) {
        body.classList.add('light-mode');
        toggle.classList.add('theme-toggle__button--light');
    }

    // Toggle theme on click
    toggle.addEventListener('click', function() {
        body.classList.toggle('light-mode');
        toggle.classList.toggle('theme-toggle__button--light');

        const isLight = body.classList.contains('light-mode');
        localStorage.setItem(STORAGE_KEY, isLight ? 'light' : 'dark');
    });
})();
</script>

<div class="auth-layout">
    <div class="auth-layout__container">
        <!-- Logo -->
        <div class="auth-layout__header">
            <a href="/" class="auth-layout__logo">
                <span class="auth-layout__logo-text">App</span><span class="auth-layout__logo-dot">.</span>
            </a>
        </div>

        <!-- Register Card -->
        <div class="auth-card">
            <div class="auth-card__header">
                <h1 class="auth-card__title">Create your account</h1>
                <p class="auth-card__subtitle">Start your 14-day free trial. No credit card required.</p>
            </div>

            <?php if (!empty($error)) { ?>
            <div class="auth-card__alert auth-card__alert--error">
                <span class="auth-alert__message"><?php echo $this->e((string) $error); ?></span>
            </div>
            <?php } ?>

            <form class="auth-card__form" method="post" action="/register">
                <?php echo CsrfMiddleware::tokenField(); ?>

                <!-- Name Field -->
                <div class="auth-form__group">
                    <label for="name" class="auth-form__label">Full Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        required
                        value="<?php echo $this->e($old['name'] ?? ''); ?>"
                        class="auth-form__input"
                        placeholder="John Doe"
                    >
                </div>

                <!-- Email Field -->
                <div class="auth-form__group">
                    <label for="email" class="auth-form__label">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="<?php echo $this->e($old['email'] ?? ''); ?>"
                        class="auth-form__input"
                        placeholder="john@example.com"
                    >
                </div>

                <!-- Password Field -->
                <div class="auth-form__group">
                    <label for="password" class="auth-form__label">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        minlength="8"
                        class="auth-form__input"
                        placeholder="••••••••"
                    >
                    <p class="auth-form__hint">Minimum 8 characters</p>
                </div>

                <!-- Confirm Password Field -->
                <div class="auth-form__group">
                    <label for="password_confirm" class="auth-form__label">Confirm Password</label>
                    <input
                        id="password_confirm"
                        name="password_confirm"
                        type="password"
                        autocomplete="new-password"
                        required
                        minlength="8"
                        class="auth-form__input"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Terms & Submit -->
                <div class="auth-form__actions">
                    <label class="auth-form__checkbox-wrapper">
                        <input
                            type="checkbox"
                            name="terms"
                            class="auth-form__checkbox"
                            required
                        >
                        <span class="auth-form__checkbox-label">
                            I agree to the <a href="#" class="auth-form__link">Terms of Service</a> and <a href="#" class="auth-form__link">Privacy Policy</a>
                        </span>
                    </label>

                    <button type="submit" class="auth-form__btn auth-form__btn--primary">
                        Create account
                    </button>
                </div>
            </form>

            <div class="auth-card__footer">
                <p class="auth-card__text">
                    Already have an account?
                    <a href="/login" class="auth-card__link">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
