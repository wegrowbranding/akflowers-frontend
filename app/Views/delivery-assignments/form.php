<?php
$title = ($isEdit ? 'Edit' : 'Add') . ' Delivery Assignment';
ob_start();
?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('delivery-assignments') ?>">Assignments</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'New' ?> Assignment</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="card-title mb-0 fw-bold"><?= $isEdit ? 'Edit' : 'New' ?> Delivery Assignment</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger px-3 py-2 mb-3 small">
                        <ul class="mb-0 ps-2">
                            <?php foreach($errors as $e): ?>
                                <li><?= e($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $isEdit ? url('delivery-assignments/' . $assignment['id'] . '/edit') : url('delivery-assignments/create') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Order ID</label>
                        <select name="order_id" class="form-select form-select-sm" required>
                            <option value="">Select Order...</option>
                            <?php foreach($orders as $o): ?>
                                <option value="<?= $o['id'] ?>" <?= (int)($old['order_id'] ?? $assignment['order_id'] ?? 0) === (int)$o['id'] ? 'selected' : '' ?>>
                                    Order #<?= e($o['order_number'] ?? $o['id']) ?> - ₹<?= number_format($o['final_amount'] ?? 0, 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Delivery Staff</label>
                        <select name="delivery_staff_id" class="form-select form-select-sm" required>
                            <option value="">Select Staff...</option>
                            <?php foreach($staff as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= (int)($old['delivery_staff_id'] ?? $assignment['delivery_staff_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['staff']['full_name'] ?? 'Unknown') ?> (<?= e($s['vehicle_number'] ?: $s['vehicle_type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <?php
                                $statuses = ['assigned', 'accepted', 'picked_up', 'out_for_delivery', 'delivered', 'rejected'];
                                foreach($statuses as $st): ?>
                                    <option value="<?= $st ?>" <?= ($old['status'] ?? $assignment['status'] ?? 'assigned') === $st ? 'selected' : '' ?>>
                                        <?= str_replace('_', ' ', ucfirst($st)) ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Assigned At</label>
                        <input type="datetime-local" name="assigned_at" class="form-control form-control-sm" 
                               value="<?= date('Y-m-d\TH:i', strtotime($old['assigned_at'] ?? $assignment['assigned_at'] ?? date('Y-m-d H:i'))) ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= url('delivery-assignments') ?>" class="btn btn-light btn-sm px-3">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update' : 'Save' ?> Assignment
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
