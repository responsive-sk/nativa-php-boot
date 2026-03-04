<?php declare(strict_types = 1);

use App\UseCase\Profile\UpdateProfile\Form;

/**
 * @var Form   $form
 * @var string $profileUrl
 * @var string $successMessage
 */
$name = $form->name ?? '';
$errors = $form->isValidated() && !$form->isValid()
    ? $form->getValidationResult()->getErrorMessages()
    : [];
?>

<section class="profile-edit">
    <div class="profile-edit__container">
        <div class="profile-edit__header">
            <a href="<?php echo htmlspecialchars($profileUrl); ?>" class="profile-edit__back">Back to Profile</a>
            <h1 class="profile-edit__title">Update Profile</h1>
        </div>

        <div class="profile-edit__form-container">
            <?php if (!empty($successMessage)) { ?>
            <div class="profile-edit__success">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
            <?php } ?>

            <?php if (!empty($errors)) { ?>
            <div class="profile-edit__errors">
                <?php foreach ($errors as $error) { ?>
                <p class="profile-edit__error"><?php echo htmlspecialchars($error); ?></p>
                <?php } ?>
            </div>
            <?php } ?>

            <form method="post" class="profile-edit__form">
                <div class="profile-edit__form-group">
                    <label for="name" class="profile-edit__label">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo htmlspecialchars($name); ?>"
                        class="profile-edit__input"
                        placeholder="Enter your name"
                        required
                    >
                </div>

                <div class="profile-edit__actions">
                    <button type="submit" class="btn btn--primary profile-edit__submit">
                        Save Changes
                    </button>
                    <a href="<?php echo htmlspecialchars($profileUrl); ?>" class="btn btn--outline profile-edit__cancel">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
