<?php
$title  = $isEdit ? 'Edit Coupon' : 'Add Coupon';
$action = $isEdit ? url('coupons/'.($coupon['id']??'').'/edit') : url('coupons/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('coupons') ?>">Coupons</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:680px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase fw-bold" required
                           style="letter-spacing:.1em" value="<?= old('code', $coupon['code'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Discount Type <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select" required>
                        <option value="percentage" <?= ($coupon['discount_type'] ?? 'percentage') === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                        <option value="fixed"      <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Value <span class="text-danger">*</span></label>
                    <input type="number" name="discount_value" class="form-control" step="0.01" min="0" required value="<?= old('discount_value', (string)($coupon['discount_value'] ?? '0')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Min Order Amount</label>
                    <div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" value="<?= old('min_order_amount', (string)($coupon['min_order_amount'] ?? '')) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Usage Limit</label>
                    <input type="number" name="usage_limit" class="form-control" min="0" placeholder="Unlimited" value="<?= old('usage_limit', (string)($coupon['usage_limit'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= ($coupon['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($coupon['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Valid From</label>
                    <input type="date" name="valid_from" class="form-control" value="<?= old('valid_from', $coupon['valid_from'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Valid To</label>
                    <input type="date" name="valid_to" class="form-control" value="<?= old('valid_to', $coupon['valid_to'] ?? '') ?>">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Coupon</button>
                <a href="<?= url('coupons') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
