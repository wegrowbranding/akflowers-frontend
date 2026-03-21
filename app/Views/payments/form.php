<?php
$title  = $isEdit ? 'Edit Payment' : 'Add Payment';
$action = $isEdit ? url('payments/'.($payment['id']??'').'/edit') : url('payments/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('payments') ?>">Payments</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:640px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Order <span class="text-danger">*</span></label>
                    <select name="order_id" class="form-select" required>
                        <option value="">— Select Order —</option>
                        <?php foreach ($orders as $o): ?>
                            <option value="<?= $o['id'] ?>" <?= (string)($payment['order_id'] ?? '') === (string)$o['id'] ? 'selected' : '' ?>>
                                #<?= $o['id'] ?> — <?= e($o['order_number'] ?? 'Order '.$o['id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['pending','success','failed'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($payment['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Transaction ID</label>
                    <input type="text" name="transaction_id" class="form-control" value="<?= old('transaction_id', $payment['transaction_id'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Payment Gateway</label>
                    <input type="text" name="payment_gateway" class="form-control" placeholder="stripe, razorpay…" value="<?= old('payment_gateway', $payment['payment_gateway'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Amount <span class="text-danger">*</span></label>
                    <div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required value="<?= old('amount', (string)($payment['amount'] ?? '0')) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Paid At</label>
                    <input type="datetime-local" name="paid_at" class="form-control" value="<?= old('paid_at', isset($payment['paid_at']) ? date('Y-m-d\TH:i', strtotime($payment['paid_at'])) : '') ?>">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Record' ?> Payment</button>
                <a href="<?= url('payments') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
