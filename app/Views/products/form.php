<?php
$title  = $isEdit ? 'Edit Product' : 'Add Product';
$action = $isEdit
    ? url('products/' . ($product['id'] ?? '') . '/edit')
    : url('products/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('products') ?>">Products</a></li>
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
                <div class="col-md-4">
                    <label class="form-label fw-medium">Product Code <span class="text-danger">*</span></label>
                    <input type="text" name="product_code" class="form-control" required
                           value="<?= old('product_code', $product['product_code'] ?? '') ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="product_name" class="form-control" required
                           value="<?= old('product_name', $product['product_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">— Select Category —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= (string)($product['category_id'] ?? '') === (string)$cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Unit</label>
                    <input type="text" name="unit" class="form-control" placeholder="pcs, kg, ltr…"
                           value="<?= old('unit', $product['unit'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= ($product['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="price" class="form-control" step="0.01" min="0"
                               value="<?= old('price', (string)($product['price'] ?? '0')) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Cost Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="cost_price" class="form-control" step="0.01" min="0"
                               value="<?= old('cost_price', (string)($product['cost_price'] ?? '0')) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" min="0"
                           value="<?= old('stock_quantity', (string)($product['stock_quantity'] ?? '0')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= old('description', $product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Product Media</h5>
            
            <div class="mb-3">
                <input type="file" id="mediaUpload" class="form-control" accept=".png,.jpg,.jpeg,.mp4" multiple>
                <div class="form-text">Accepted format: png, jpg, jpeg, mp4 (Max 1MB)</div>
            </div>

            <div id="mediaPreviews" class="d-flex flex-wrap gap-3">
                <?php if (!empty($product['media'])): ?>
                    <?php foreach ($product['media'] as $media): ?>
                        <div class="position-relative border rounded p-2" id="media-<?= $media['id'] ?>" style="width: 140px;">
                            <input type="hidden" name="media_ids[]" value="<?= $media['id'] ?>">
                            <?php $vUrl = API_BASE . '/media/' . $media['id'] . '/view'; ?>
                            <?php if (in_array(strtolower($media['extension']), ['mp4'])): ?>
                                <video src="<?= $vUrl ?>" class="w-100 rounded" style="height: 100px; object-fit: cover;" controls></video>
                            <?php else: ?>
                                <img src="<?= $vUrl ?>" class="w-100 rounded" style="height: 100px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="mt-2 text-center">
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="primary_media_id" id="pri-<?= $media['id'] ?>" value="<?= $media['id'] ?>" <?= !empty($media['pivot']['is_primary']) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="pri-<?= $media['id'] ?>">Primary</label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" onclick="deleteMedia(<?= $media['id'] ?>)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Create' ?> Product
                </button>
                <a href="<?= url('products') ?>" class="btn btn-outline-secondary">Cancel</a>
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
        
        isUploading = true;
        $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading...');
        
        for (let file of e.target.files) {
            // Check file size (1MB)
            if (file.size > 1024 * 1024) {
                alert('File "' + file.name + '" exceeds the 1MB size limit!');
                continue;
            }
            
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
                    let isVideo = ['mp4'].includes(media.extension.toLowerCase());
                    let preview = isVideo ? 
                        `<video src="{$viewUrl}\${media.id}/view" class="w-100 rounded" style="height: 100px; object-fit: cover;" controls></video>` :
                        `<img src="{$viewUrl}\${media.id}/view" class="w-100 rounded" style="height: 100px; object-fit: cover;">`;
                        
                    let html = `
                        <div class="position-relative border rounded p-2" id="media-\${media.id}" style="width: 140px;">
                            <input type="hidden" name="media_ids[]" value="\${media.id}">
                            \${preview}
                            <div class="mt-2 text-center">
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="primary_media_id" id="pri-\${media.id}" value="\${media.id}">
                                    <label class="form-check-label small" for="pri-\${media.id}">Primary</label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" onclick="deleteMedia(\${media.id})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                    $('#mediaPreviews').append(html);
                } else {
                    alert('Failed to upload ' + file.name + ': ' + (json.message || 'Unknown error'));
                }
            } catch (err) {
                alert('Error uploading ' + file.name);
            }
        }
        
        $('#mediaUpload').val('');
        isUploading = false;
        $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> {$buttonText} Product');
    });
});

window.deleteMedia = async function(id) {
    if(!confirm('Are you sure you want to remove this media?')) return;
    
    // Optionally delete from server too
    try {
        const token = '{$token}';
        const JS_API_BASE = '{$apiBase}';
        await fetch(JS_API_BASE + '/media/' + id + '/delete', {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token }
        });
    } catch(e) {}
    
    $('#media-' + id).remove();
};
</script>
HTML;

clearOld();
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';

