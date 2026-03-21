<?php
$title  = $isEdit ? 'Edit Customer' : 'Add Customer';
$action = $isEdit ? url('customers/'.($customer['id']??'').'/edit') : url('customers/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('customers') ?>">Customers</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:720px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Customer Code</label>
                    <input type="text" name="customer_code" class="form-control" value="<?= old('customer_code', $customer['customer_code'] ?? '') ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required value="<?= old('full_name', $customer['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email', $customer['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone', $customer['phone'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">— Select —</option>
                        <?php foreach (['male','female','other'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($customer['gender'] ?? '') === $g ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= old('date_of_birth', $customer['date_of_birth'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['active','inactive','blocked'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($customer['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Password <?= $isEdit ? '<span class="text-muted small">(leave blank to keep)</span>' : '' ?></label>
                    <input type="password" name="password" class="form-control" minlength="6">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Customer</button>
                <a href="<?= url('customers') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
