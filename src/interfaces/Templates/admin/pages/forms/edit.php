<?php
/**
 * Admin Edit Form Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var \Domain\Model\Form $form
 */
$form = $this->getCurrentData()['form'] ?? null;
$formName = $form ? $form->name() : '';
$slug = $form ? $form->slug() : '';
$emailNotification = $form ? $form->emailNotification() : '';
$successMessage = $form ? $form->successMessage() : 'Thank you for your submission!';
$schema = $form ? json_encode($form->schema()) : '[]';
?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/forms" class="text-gray-600 hover:text-gray-900">← Back to Forms</a>
    </div>

    <form action="/admin/forms/<?= $this->e($form->id()) ?>/edit" method="POST" x-data="formBuilder(<?= $schema ?>)" class="space-y-6">
        <!-- Form Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Form Details</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Form Name *</label>
                    <input type="text" name="name" x-model="formName" @input="generateSlug()" 
                           value="<?= $this->e($formName) ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                    <input type="text" name="slug" x-model="slug" 
                           value="<?= $this->e($slug) ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required />
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Notification</label>
                <input type="email" name="emailNotification" 
                       value="<?= $this->e($emailNotification) ?>"
                       placeholder="admin@example.com"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Success Message</label>
                <input type="text" name="successMessage" 
                       value="<?= $this->e($successMessage) ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
        </div>

        <!-- Form Builder -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Form Fields</h2>
            
            <!-- Field Types -->
            <div class="flex gap-2 mb-4">
                <button type="button" @click="addField('text')" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">+ Text</button>
                <button type="button" @click="addField('email')" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">+ Email</button>
                <button type="button" @click="addField('textarea')" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">+ Textarea</button>
                <button type="button" @click="addField('select')" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">+ Select</button>
                <button type="button" @click="addField('checkbox')" class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">+ Checkbox</button>
            </div>

            <!-- Fields List -->
            <div class="space-y-3">
                <template x-for="(field, index) in fields" :key="index">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium" x-text="field.label || 'Field ' + (index + 1)"></span>
                            <button type="button" @click="removeField(index)" class="text-red-600 hover:text-red-900">Remove</button>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" x-model="field.name" placeholder="Field name" 
                                   class="px-3 py-2 border rounded text-sm" />
                            <input type="text" x-model="field.label" placeholder="Label" 
                                   class="px-3 py-2 border rounded text-sm" />
                            <select x-model="field.type" class="px-3 py-2 border rounded text-sm">
                                <option value="text">Text</option>
                                <option value="email">Email</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>
                        
                        <div class="mt-2 flex items-center gap-2">
                            <label class="text-sm text-gray-600">Required:</label>
                            <input type="checkbox" x-model="field.required" class="rounded" />
                            
                            <template x-if="field.type === 'select'">
                                <input type="text" x-model="field.options" placeholder="Options (comma separated)" 
                                       class="ml-4 px-3 py-1 border rounded text-sm flex-1" />
                            </template>
                        </div>
                    </div>
                </template>
                
                <div x-show="fields.length === 0" class="text-center py-8 text-gray-500">
                    No fields yet. Click a button above to add fields.
                </div>
            </div>
        </div>

        <!-- Hidden schema input -->
        <input type="hidden" name="schema" :value="JSON.stringify(fields)" />

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="/admin/forms" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Form
            </button>
        </div>
    </form>
</div>

<script>
function formBuilder(initialFields = []) {
    return {
        formName: '<?= $this->e($formName) ?>',
        slug: '<?= $this->e($slug) ?>',
        fields: initialFields || [],
        
        generateSlug() {
            this.slug = this.formName.toLowerCase()
                .replace(/[^a-z0-9-]/g, '')
                .replace(/-+/g, '-')
                .trim('-');
        },
        
        addField(type) {
            this.fields.push({
                name: '',
                label: '',
                type: type,
                required: false,
                options: ''
            });
        },
        
        removeField(index) {
            this.fields.splice(index, 1);
        }
    }
}
</script>
