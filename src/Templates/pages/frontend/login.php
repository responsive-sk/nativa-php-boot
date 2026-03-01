<?php declare(strict_types=1);

use App\UseCase\Login\Form;

/**
 * @var Form $form
 * @var string $loginUrl
 * @var string $homeUrl
 * @var string $csrfToken
 */

$login = $form->login ?? '';
$rememberMe = $form->rememberMe ?? false;
$errors = $form->isValidated() && !$form->isValid() 
    ? $form->getValidationResult()->getErrorMessages() 
    : [];
?>

<section class="login">
    <div class="login__container">
        <div class="login__header">
            <a href="<?= htmlspecialchars($homeUrl) ?>" class="login__logo">
                App<span class="login__logo-dot">.</span>
            </a>
            <h1 class="login__title">Sign in to your account</h1>
            <p class="login__subtitle">Welcome back. Please enter your details.</p>
        </div>

        <div class="login__form-container">
            <?php if (!empty($errors)): ?>
            <div class="login__errors">
                <?php foreach ($errors as $error): ?>
                <p class="login__error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars($loginUrl) ?>" class="login__form">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="login__form-group">
                    <label for="login" class="login__label">Login</label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        value="<?= htmlspecialchars($login) ?>"
                        class="login__input"
                        placeholder="Enter your login"
                        required
                    >
                </div>

                <div class="login__form-group">
                    <label for="password" class="login__label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="login__input"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="login__options">
                    <label class="login__checkbox">
                        <input type="checkbox" name="rememberMe" value="1" <?= $rememberMe ? 'checked' : '' ?>>
                        <span class="login__checkbox-mark"></span>
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn--primary login__submit">
                    Sign in
                </button>
            </form>
        </div>

        <div class="login__footer">
            <p>Don't have an account? <a href="/register" class="login__link">Sign up</a></p>
        </div>
    </div>
</section>
