<?php
/**
 * Admin Create Page Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 */
?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/pages" class="text-gray-600 hover:text-gray-900">← Back to Pages</a>
    </div>

    <form id="create-page-form" class="space-y-6">
        <!-- Page Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Page Details</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" id="title" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select name="template" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="default">Default</option>
                        <option value="landing">Landing Page</option>
                        <option value="minimal">Minimal</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea name="content" id="content" rows="12" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="mt-4 flex items-center">
                <input type="checkbox" name="isPublished" id="isPublished" value="1"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label for="isPublished" class="ml-2 text-sm text-gray-700">Publish immediately</label>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">SEO Settings</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="metaTitle" maxlength="60"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                    <p class="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="metaDescription" rows="3" maxlength="160"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="/admin/pages" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Page
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('create-page-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/admin/pages', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.error) {
            alert('Create failed: ' + result.error);
        } else if (result.success) {
            window.location.href = '/admin/pages';
        }
    } catch (error) {
        alert('Create error: ' + error.message);
    }
});
</script>
