<?php
/**
 * Admin Media Library Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var array $media
 * @var string $provider
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $this->e($title) ?></h1>
        <div class="flex gap-2">
            <span class="text-sm text-gray-500">Provider: <strong><?= $this->e($provider) ?></strong></span>
            <button onclick="document.getElementById('file-input').click()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Upload Media
            </button>
        </div>
    </div>

    <!-- Hidden file input -->
    <input type="file" id="file-input" class="hidden" accept="image/*,video/*,application/pdf" multiple />

    <!-- Upload Progress -->
    <div id="upload-progress" class="hidden mb-4">
        <div class="bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
        </div>
        <p class="text-sm text-gray-600 mt-1">Uploading...</p>
    </div>

    <!-- Media Grid -->
    <?php if (empty($media)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No media yet.</p>
            <button onclick="document.getElementById('file-input').click()" 
                    class="text-blue-600 hover:underline mt-2 inline-block">Upload your first file</button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($media as $item): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden group relative" data-media-id="<?= $item->id() ?>">
                <!-- Preview -->
                <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                    <?php if ($item->isImage()): ?>
                        <img src="<?= $this->e($item->url()) ?>" alt="<?= $this->e($item->filename()) ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform" />
                    <?php elseif ($item->isVideo()): ?>
                        <video src="<?= $this->e($item->url()) ?>" class="w-full h-full object-cover"></video>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <span class="text-4xl">📄</span>
                            <p class="text-xs text-gray-500 mt-2"><?= $this->e(strtoupper(pathinfo($item->filename(), PATHINFO_EXTENSION))) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="p-3">
                    <p class="text-sm font-medium truncate" title="<?= $this->e($item->originalName()) ?>">
                        <?= $this->e($item->filename()) ?>
                    </p>
                    <p class="text-xs text-gray-500"><?= $item->getFormattedSize() ?></p>
                    <p class="text-xs text-gray-400"><?= $this->date($item->createdAt(), 'M d, Y') ?></p>
                </div>

                <!-- Actions Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <a href="<?= $this->e($item->url()) ?>" target="_blank" 
                       class="p-2 bg-white rounded-full hover:bg-gray-100" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <button onclick="deleteMedia('<?= $item->id() ?>')" 
                            class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Upload handler
document.getElementById('file-input').addEventListener('change', async function(e) {
    const files = e.target.files;
    if (!files.length) return;

    const progressDiv = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    
    progressDiv.classList.remove('hidden');
    
    for (let file of files) {
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const response = await fetch('/admin/media', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.error) {
                alert('Upload failed: ' + result.error);
            } else if (result.duplicate) {
                alert('File already exists! (Duplicate detected by hash)\n\n' + result.message);
            } else {
                // Reload page to show new media
                window.location.reload();
            }
        } catch (error) {
            alert('Upload error: ' + error.message);
        }
    }
    
    progressDiv.classList.add('hidden');
    progressBar.style.width = '0%';
});

// Delete handler
async function deleteMedia(id) {
    if (!confirm('Are you sure you want to delete this media?')) return;
    
    try {
        const response = await fetch('/admin/media/' + id, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            // Remove from DOM
            document.querySelector('[data-media-id="' + id + '"]').remove();
        } else {
            alert('Delete failed');
        }
    } catch (error) {
        alert('Delete error: ' + error.message);
    }
}
</script>
