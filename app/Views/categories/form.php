<?php
$title  = $isEdit ? 'Edit Category' : 'Add Category';
$action = $isEdit
    ? url('categories/' . ($category['id'] ?? '') . '/edit')
    : url('categories/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('categories') ?>">Categories</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>
</div>

<div class="card" style="max-width:720px">
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
                <div class="col-md-8">
                    <label class="form-label fw-medium">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="category_name" class="form-control" required
                           value="<?= old('category_name', $category['category_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Display Order</label>
                    <input type="number" name="display_order" class="form-control" min="0"
                           value="<?= old('display_order', (string)($category['display_order'] ?? 0)) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Parent Category</label>
                    <select name="parent_category_id" class="form-select">
                        <option value="">— None —</option>
                        <?php foreach ($parentCategories as $pc): ?>
                            <option value="<?= $pc['id'] ?>"
                                <?= (string)($category['parent_category_id'] ?? '') === (string)$pc['id'] ? 'selected' : '' ?>>
                                <?= e($pc['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= ($category['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= old('description', $category['description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="show_in_menu" id="showInMenu" value="1"
                               <?= !empty($category['show_in_menu']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="showInMenu">Show in menu</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Create' ?> Category
                </button>
                <a href="<?= url('categories') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
clearOld();
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
