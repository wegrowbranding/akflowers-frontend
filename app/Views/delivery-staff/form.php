<?php
$title = ($isEdit ? 'Edit' : 'Add') . ' Delivery Staff';
ob_start();
?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('delivery-staff') ?>">Delivery Staff</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Add' ?> Staff</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="card-title mb-0 fw-bold"><?= $isEdit ? 'Edit' : 'New' ?> Delivery Staff Form</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger px-3 py-2 mb-3">
                        <ul class="mb-0 small ps-2">
                        <?php foreach($errors as $e): ?>
                            <li><?= e($e) ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $isEdit ? url('delivery-staff/' . $deliveryStaff['id'] . '/edit') : url('delivery-staff/create') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Staff Account</label>
                        <select name="staff_id" class="form-select form-select-sm" required>
                            <option value="">Select Staff...</option>
                            <?php foreach($relatedStaff as $rs): ?>
                                <option value="<?= $rs['id'] ?>" <?= (int)($old['staff_id'] ?? $deliveryStaff['staff_id'] ?? 0) === (int)$rs['id'] ? 'selected' : '' ?>>
                                    <?= e($rs['full_name']) ?> (<?= e($rs['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Link this delivery staff to an existing branch staff user.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select form-select-sm" required>
                            <option value="bike"  <?= ($old['vehicle_type'] ?? $deliveryStaff['vehicle_type'] ?? '') === 'bike'  ? 'selected' : '' ?>>Bike</option>
                            <option value="cycle" <?= ($old['vehicle_type'] ?? $deliveryStaff['vehicle_type'] ?? '') === 'cycle' ? 'selected' : '' ?>>Cycle</option>
                            <option value="car"   <?= ($old['vehicle_type'] ?? $deliveryStaff['vehicle_type'] ?? '') === 'car'   ? 'selected' : '' ?>>Car</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Vehicle Number</label>
                        <input type="text" name="vehicle_number" class="form-control form-control-sm" placeholder="e.g. MH 12 AB 1234"
                               value="<?= e($old['vehicle_number'] ?? $deliveryStaff['vehicle_number'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_available" id="is_available"
                                   <?= ($old['is_available'] ?? $deliveryStaff['is_available'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold small" for="is_available">Available for delivery</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= url('delivery-staff') ?>" class="btn btn-light btn-sm px-3">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Save' ?> Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
