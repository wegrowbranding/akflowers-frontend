<?php
$title  = $isEdit ? 'Edit Banner' : 'Add Banner';
$action = $isEdit
    ? url('banners/' . ($banner['id'] ?? '') . '/edit')
    : url('banners/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('banners') ?>">Banners</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>
</div>

<div class="card" style="max-width:800px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2 small">
                <?php foreach ($errors as $err): ?>
                    <div><i class="bi bi-exclamation-circle me-1"></i><?= e($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="1" <?= ($banner['status'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($banner['status'] ?? '') == 0 && isset($banner['status']) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <label class="form-label fw-medium">Banner Image <span class="text-danger">*</span></label>
                    <div class="mb-3">
                        <input type="file" id="mediaUpload" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text">Accepted format: png, jpg, jpeg (Max 1MB)</div>
                    </div>
                    
                    <input type="hidden" name="image" id="media_id" value="<?= old('image', $banner['image'] ?? '') ?>" required>
                    
                    <div id="mediaPreviews" class="d-flex flex-wrap gap-3">
                        <?php if (!empty($banner['media'])): ?>
                            <div class="position-relative border rounded p-2" id="media-<?= $banner['media']['id'] ?>" style="width: 100%; max-width: 400px;">
                                <?php $vUrl = API_BASE . '/media/' . $banner['media']['id'] . '/view'; ?>
                                <img src="<?= $vUrl ?>" class="w-100 rounded" style="height: 180px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" onclick="deleteMedia(<?= $banner['media']['id'] ?>)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="btnSubmit" <?= empty($banner['image']) ? 'disabled' : '' ?>>
                    <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Create' ?> Banner
                </button>
                <a href="<?= url('banners') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
$token = $_SESSION['token'] ?? '';
$uploadUrl = API_BASE . '/media/upload';
$viewUrl = API_BASE . '/media/';
$buttonText = $isEdit ? 'Update' : 'Create';
$apiBase = API_BASE;
$scripts = <<<HTML
<script>
$(function() {
    const token = '{$token}';
    const JS_API_BASE = '{$apiBase}';
    let isUploading = false;
    
    $('#mediaUpload').on('change', async function(e) {
        if (!e.target.files.length) return;
        
        let file = e.target.files[0];
        
        // Final check for image
        if (!file.type.startsWith('image/')) {
            alert('Only images are allowed!');
            $(this).val('');
            return;
        }
        
        // Size check (1MB)
        if (file.size > 1024 * 1024) {
            alert('The file "' + file.name + '" exceeds the 1MB size limit!');
            $(this).val('');
            return;
        }

        isUploading = true;
        $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading...');
        
        let formData = new FormData();
        formData.append('file', file);
        
        try {
            let res = await fetch('{$uploadUrl}', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            
            let json = await res.json();
            
            if (json.success) {
                let media = json.data;
                $('#media_id').val(media.id);
                
                let preview = `<img src="{$viewUrl}\${media.id}/view" class="w-100 rounded" style="height: 180px; object-fit: cover;">`;
                    
                let html = `
                    <div class="position-relative border rounded p-2" id="media-\${media.id}" style="width: 100%; max-width: 400px;">
                        \${preview}
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" onclick="deleteMedia(\${media.id})">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                $('#mediaPreviews').empty().append(html);
                $('#btnSubmit').prop('disabled', false);
            } else {
                alert('Failed to upload ' + file.name + ': ' + (json.message || 'Unknown error'));
            }
        } catch (err) {
            alert('Error uploading ' + file.name);
        }
        
        $('#mediaUpload').val('');
        isUploading = false;
        $('#btnSubmit').html('<i class="bi bi-check-lg me-1"></i> {$buttonText} Banner');
    });
});

window.deleteMedia = async function(id) {
    if(!confirm('Are you sure you want to remove this image?')) return;
    
    $('#media-' + id).remove();
    $('#media_id').val('');
    $('#btnSubmit').prop('disabled', true);
};
</script>
HTML;

clearOld();
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
