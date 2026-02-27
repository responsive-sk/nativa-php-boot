<?php
/**
 * Admin Edit Page Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var \Domain\Model\Page $page
 */
$page = $this->getCurrentData()['page'] ?? null;
?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <a href="/admin/pages" class="text-gray-600 hover:text-gray-900">← Back to Pages</a>
    </div>

    <form id="edit-page-form" class="space-y-6">
        <!-- Page Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Page Details</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" value="<?= $this->e($page->title()) ?>" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                    <input type="text" value="<?= $this->e($page->slug()) ?>" disabled
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500" />
                    <p class="text-xs text-gray-500 mt-1">Auto-generated from title</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea name="content" rows="12" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?= $this->e($page->content()) ?></textarea>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select name="template" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="default" <?= $page->template() === 'default' ? 'selected' : '' ?>>Default</option>
                        <option value="landing" <?= $page->template() === 'landing' ? 'selected' : '' ?>>Landing Page</option>
                        <option value="minimal" <?= $page->template() === 'minimal' ? 'selected' : '' ?>>Minimal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="mt-2">
                        <span class="px-3 py-1 text-sm rounded-full <?= $page->isPublished() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                            <?= $page->isPublished() ? 'Published' : 'Draft' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">SEO Settings</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="metaTitle" value="<?= $this->e($page->metaTitle() ?? '') ?>" maxlength="60"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="metaDescription" rows="3" maxlength="160"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?= $this->e($page->metaDescription() ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Content Blocks -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Content Blocks</h2>
                <button type="button" onclick="showAddBlockModal()" class="text-sm text-blue-600 hover:text-blue-800">+ Add Block</button>
            </div>
            
            <div id="blocks-container">
            <?php if (empty($blocks)): ?>
                <p class="text-gray-500 text-sm">No content blocks yet. Click "Add Block" to create reusable sections.</p>
            <?php else: ?>
                <div class="space-y-3" id="blocks-list">
                    <?php foreach ($blocks as $block): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center" data-block-id="<?= $block->id() ?>">
                        <div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded"><?= $this->e($block->type()) ?></span>
                            <span class="ml-2 font-medium"><?= $this->e($block->title() ?? 'Untitled Block') ?></span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editBlock('<?= $block->id() ?>')" class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                            <button onclick="deleteBlock('<?= $block->id() ?>')" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            </div>
        </div>

        <!-- Page Media -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Page Media</h2>
                <button type="button" onclick="attachMedia()" class="text-sm text-blue-600 hover:text-blue-800">+ Attach Media</button>
            </div>
            
            <?php if (empty($media)): ?>
                <p class="text-gray-500 text-sm">No media attached. Upload images or attach from media library.</p>
            <?php else: ?>
                <div class="grid grid-cols-4 gap-4">
                    <?php foreach ($media as $item): ?>
                    <div class="border rounded-lg overflow-hidden">
                        <img src="<?= $this->e($item->url()) ?>" alt="<?= $this->e($item->caption() ?? '') ?>" class="w-full h-32 object-cover" />
                        <div class="p-2">
                            <p class="text-xs text-gray-600 truncate"><?= $this->e($item->caption() ?? 'No caption') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Embedded Forms -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Embedded Forms</h2>
                <button type="button" onclick="embedForm()" class="text-sm text-blue-600 hover:text-blue-800">+ Embed Form</button>
            </div>
            
            <?php if (empty($forms)): ?>
                <p class="text-gray-500 text-sm">No forms embedded. Add contact forms or lead capture forms to this page.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($forms as $form): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <span class="font-medium"><?= $this->e($form->title() ?? $form->formName()) ?></span>
                            <span class="ml-2 text-xs text-gray-500">Position: <?= $this->e($form->position()) ?></span>
                        </div>
                        <div class="flex gap-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                            <button class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="/admin/pages" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
// Page ID from URL
const pageId = window.location.pathname.split('/').filter(Boolean).pop();
const currentPage = window.location.pathname;

// ============ BLOCKS ============
function showAddBlockModal() {
    const type = prompt('Block type (hero, features, cta, text_image, testimonials):');
    if (!type) return;
    
    const title = prompt('Block title (optional):');
    
    fetch(currentPage, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_action': 'add_block',
            'type': type,
            'title': title || '',
            'content': '',
            'sortOrder': '0'
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.error) {
            alert('Add failed: ' + result.error);
        } else if (result.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

function editBlock(blockId) {
    const title = prompt('Update block title:');
    if (!title) return;
    
    fetch(currentPage, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_action': 'update_block',
            'blockId': blockId,
            'title': title
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.error) {
            alert('Update failed: ' + result.error);
        } else if (result.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

function deleteBlock(blockId) {
    if (!confirm('Are you sure you want to delete this block?')) return;
    
    fetch(currentPage, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_action': 'delete_block',
            'blockId': blockId
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.error) {
            alert('Delete failed: ' + result.error);
        } else if (result.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

// ============ MEDIA ============
function attachMedia() {
    const mediaId = prompt('Enter media ID from library:');
    if (!mediaId) return;
    
    const caption = prompt('Caption (optional):');
    
    fetch(currentPage, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_action': 'attach_media',
            'mediaId': mediaId,
            'caption': caption || ''
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.error) {
            alert('Attach failed: ' + result.error);
        } else if (result.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

// ============ FORMS ============
function embedForm() {
    const formId = prompt('Enter form ID to embed:');
    if (!formId) return;
    
    const title = prompt('Form title (optional):');
    const position = prompt('Position (sidebar, content, bottom):', 'sidebar');
    
    fetch(currentPage, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '_action': 'embed_form',
            'formId': formId,
            'title': title || '',
            'position': position || 'sidebar'
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.error) {
            alert('Embed failed: ' + result.error);
        } else if (result.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

// Update existing handlers
document.getElementById('edit-page-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    
    try {
        const response = await fetch(currentPage, {
            method: 'POST',
            body: formData
        });

        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const result = await response.json();
            
            if (result.error) {
                alert('Update failed: ' + result.error);
            } else if (result.success) {
                alert('Page updated successfully!');
            }
        } else {
            if (response.ok) {
                alert('Page updated successfully!');
            } else {
                alert('Update failed. Please try again.');
            }
        }
    } catch (error) {
        alert('Update error: ' + error.message);
    }
});
</script>
