<?php
/**
 * Frontend Form Display Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var \Domain\Model\Form $form
 */
$form = $this->getCurrentData()['form'] ?? null;
$success = $this->getCurrentData()['success'] ?? ($this->getCurrentData()['_GET']['success'] ?? null);
?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $this->e($form->name()) ?></h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $this->e($form->successMessage()) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="/form/<?= $this->e($form->slug()) ?>" method="POST" class="space-y-6">
            <?php foreach ($form->schema() as $field): ?>
                <?php
                $type = $field['type'] ?? 'text';
                $name = $field['name'] ?? '';
                $label = $field['label'] ?? ucfirst($name);
                $required = $field['required'] ?? false;
                $options = isset($field['options']) ? explode(',', $field['options']) : [];
                ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?= $this->e($label) ?>
                        <?php if ($required): ?>
                            <span class="text-red-500">*</span>
                        <?php endif; ?>
                    </label>
                    
                    <?php if ($type === 'textarea'): ?>
                        <textarea name="<?= $this->e($name) ?>" 
                                  <?= $required ? 'required' : '' ?>
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    
                    <?php elseif ($type === 'select'): ?>
                        <select name="<?= $this->e($name) ?>" 
                                <?= $required ? 'required' : '' ?>
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select...</option>
                            <?php foreach ($options as $option): ?>
                                <option value="<?= $this->e(trim($option)) ?>"><?= $this->e(trim($option)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    
                    <?php elseif ($type === 'checkbox'): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="<?= $this->e($name) ?>" 
                                   value="1"
                                   <?= $required ? 'required' : '' ?>
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <span class="ml-2 text-sm text-gray-600">Yes, I agree</span>
                        </div>
                    
                    <?php else: ?>
                        <input type="<?= $this->e($type) ?>" name="<?= $this->e($name) ?>" 
                               <?= $required ? 'required' : '' ?>
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div>
                <button type="submit" name="submit" value="1"
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
