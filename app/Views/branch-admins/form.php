<?php
$title  = $isEdit ? 'Edit Branch Admin' : 'Add Branch Admin';
$action = $isEdit ? url('branch-admins/' . ($admin['id'] ?? '') . '/edit') : url('branch-admins/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('branch-admins') ?>">Branch Admins</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>
</div>

<div class="card" style="max-width:720px">
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
                            <option value="<?= $b['id'] ?>" <?= (string)($admin['branch_id'] ?? '') === (string)$b['id'] ? 'selected' : '' ?>>
                                <?= e($b['branch_name']) ?> (<?= e($b['branch_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['active','inactive','suspended','password_reset_required'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($admin['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required value="<?= old('username', $admin['username'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required value="<?= old('full_name', $admin['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required value="<?= old('email', $admin['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" required value="<?= old('phone', $admin['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Password <?= $isEdit ? '<span class="text-muted small">(leave blank to keep)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Admin</button>
                <a href="<?= url('branch-admins') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
