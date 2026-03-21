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

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Create' ?> Product
                </button>
                <a href="<?= url('products') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
clearOld();
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
