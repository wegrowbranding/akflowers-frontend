<?php
$title  = $isEdit ? 'Edit Branch Role' : 'Add Branch Role';
$action = $isEdit ? url('branch-roles/' . ($role['id'] ?? '') . '/edit') : url('branch-roles/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('branch-roles') ?>">Branch Roles</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>
</div>

<div class="card" style="max-width:620px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2 small">
                <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">— Select Branch —</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (string)($role['branch_id'] ?? '') === (string)$b['id'] ? 'selected' : '' ?>>
                                <?= e($b['branch_name']) ?> (<?= e($b['branch_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="role_name" class="form-control" required value="<?= old('role_name', $role['role_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= ($role['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($role['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_default" id="isDefault" value="1"
                               <?= !empty($role['is_default']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="isDefault">Set as Default Role</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="role_description" class="form-control" rows="3"><?= old('role_description', $role['role_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12 mt-4">
                    <label class="form-label fw-medium">Access Pages (Permissions) <span class="text-danger">*</span></label>
                    <div class="row mt-2 g-2">
                        <?php
                        $selectedPages = [];
                        if (isset($role['permission']['module'])) {
                            $selectedPages = array_map('trim', explode(',', $role['permission']['module']));
                        }
                        $oldPages = $_SESSION['old']['access_pages'] ?? $selectedPages;
                        if (!is_array($oldPages)) $oldPages = [];
                        foreach ($accessPages as $page): 
                            $pageId = 'page_' . md5($page);
                        ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="access_pages[]" value="<?= e($page) ?>" id="<?= $pageId ?>" <?= in_array($page, $oldPages) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?= $pageId ?>">
                                        <?= e($page) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Role</button>
                <a href="<?= url('branch-roles') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
