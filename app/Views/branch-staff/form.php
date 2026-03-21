<?php
$title  = $isEdit ? 'Edit Staff User' : 'Add Staff User';
$action = $isEdit ? url('branch-staff/' . ($staff['id'] ?? '') . '/edit') : url('branch-staff/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('branch-staff') ?>">Branch Staff</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>
</div>

<div class="card" style="max-width:860px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2 small">
                <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $action ?>">
            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Account Info</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">— Select Branch —</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (string)($staff['branch_id'] ?? '') === (string)$b['id'] ? 'selected' : '' ?>>
                                <?= e($b['branch_name']) ?> (<?= e($b['branch_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select" required>
                        <option value="">— Select Role —</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (string)($staff['role_id'] ?? '') === (string)$r['id'] ? 'selected' : '' ?>>
                                <?= e($r['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['active','inactive','suspended','resigned'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($staff['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required value="<?= old('username', $staff['username'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required value="<?= old('email', $staff['email'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">
                        Password <?= $isEdit ? '<span class="text-muted small">(leave blank to keep)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6">
                </div>
            </div>

            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Personal Info</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required value="<?= old('full_name', $staff['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" required value="<?= old('phone', $staff['phone'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= old('employee_id', $staff['employee_id'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control" value="<?= old('date_of_joining', $staff['date_of_joining'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= old('date_of_birth', $staff['date_of_birth'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= old('address', $staff['address'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Staff User</button>
                <a href="<?= url('branch-staff') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
