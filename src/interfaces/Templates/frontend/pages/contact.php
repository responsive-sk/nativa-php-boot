<?php
/**
 * Contact Page Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array|null $errors
 * @var array|null $old
 */
?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $this->e($title) ?></h1>

    <?php if ($this->getCurrentData()['success'] ?? null): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $this->e($this->getCurrentData()['success']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="/contact" method="POST" class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= $this->e($old['name'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['name']) ? 'border-red-500' : '' ?>"
                    required
                />
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $this->e($errors['name'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= $this->e($old['email'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['email']) ? 'border-red-500' : '' ?>"
                    required
                />
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $this->e($errors['email'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Subject
                </label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="<?= $this->e($old['subject'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['subject']) ? 'border-red-500' : '' ?>"
                />
                <?php if (isset($errors['subject'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $this->e($errors['subject'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['message']) ? 'border-red-500' : '' ?>"
                    required
                ><?= $this->e($old['message'] ?? '') ?></textarea>
                <?php if (isset($errors['message'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $this->e($errors['message'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Send Message
                </button>
            </div>
        </form>
    </div>

    <!-- Contact Info -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Other Ways to Reach Us</h2>
        <div class="space-y-2 text-gray-600">
            <p><strong>Email:</strong> info@phpcms.local</p>
            <p><strong>Phone:</strong> +421 900 000 000</p>
            <p><strong>Address:</strong> Bratislava, Slovakia</p>
        </div>
    </div>
</div>
