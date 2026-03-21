<?php
$title  = $isEdit ? 'Edit Address' : 'Add Address';
$action = $isEdit ? url('customer-addresses/'.($address['id']??'').'/edit') : url('customer-addresses/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('customer-addresses') ?>">Customer Addresses</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:720px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">— Select Customer —</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (string)($address['customer_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['full_name']) ?> (#<?= $c['id'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Contact Name</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name', $address['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone', $address['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="<?= old('pincode', $address['pincode'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Address Line 1</label>
                    <input type="text" name="address_line1" class="form-control" value="<?= old('address_line1', $address['address_line1'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Address Line 2</label>
                    <input type="text" name="address_line2" class="form-control" value="<?= old('address_line2', $address['address_line2'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">City</label>
                    <input type="text" name="city" class="form-control" value="<?= old('city', $address['city'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">State</label>
                    <input type="text" name="state" class="form-control" value="<?= old('state', $address['state'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Country</label>
                    <input type="text" name="country" class="form-control" value="<?= old('country', $address['country'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" id="isDefault" value="1" <?= !empty($address['is_default']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="isDefault">Set as default address</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Address</button>
                <a href="<?= url('customer-addresses') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
