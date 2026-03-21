<?php
$title  = $isEdit ? 'Edit Branch' : 'Add Branch';
$action = $isEdit ? url('branches/' . ($branch['id'] ?? '') . '/edit') : url('branches/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('branches') ?>">Branches</a></li>
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
            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Basic Info</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-medium">Branch Code <span class="text-danger">*</span></label>
                    <input type="text" name="branch_code" class="form-control" required value="<?= old('branch_code', $branch['branch_code'] ?? '') ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-medium">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" name="branch_name" class="form-control" required value="<?= old('branch_name', $branch['branch_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['active','inactive','suspended','closed'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($branch['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Opening Date</label>
                    <input type="date" name="opening_date" class="form-control" value="<?= old('opening_date', $branch['opening_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Timezone</label>
                    <input type="text" name="timezone" class="form-control" placeholder="Asia/Kolkata" value="<?= old('timezone', $branch['timezone'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Currency</label>
                    <input type="text" name="currency" class="form-control" placeholder="INR" value="<?= old('currency', $branch['currency'] ?? '') ?>">
                </div>
            </div>

            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Address</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Address Line 1 <span class="text-danger">*</span></label>
                    <input type="text" name="address_line1" class="form-control" required value="<?= old('address_line1', $branch['address_line1'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Address Line 2</label>
                    <input type="text" name="address_line2" class="form-control" value="<?= old('address_line2', $branch['address_line2'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" required value="<?= old('city', $branch['city'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control" required value="<?= old('state', $branch['state'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" class="form-control" required value="<?= old('pincode', $branch['pincode'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Country</label>
                    <input type="text" name="country" class="form-control" value="<?= old('country', $branch['country'] ?? '') ?>">
                </div>
            </div>

            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Contact</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-medium">Primary Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone_primary" class="form-control" required value="<?= old('phone_primary', $branch['phone_primary'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Secondary Phone</label>
                    <input type="text" name="phone_secondary" class="form-control" value="<?= old('phone_secondary', $branch['phone_secondary'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Primary Email <span class="text-danger">*</span></label>
                    <input type="email" name="email_primary" class="form-control" required value="<?= old('email_primary', $branch['email_primary'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Secondary Email</label>
                    <input type="email" name="email_secondary" class="form-control" value="<?= old('email_secondary', $branch['email_secondary'] ?? '') ?>">
                </div>
            </div>

            <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.06em">Legal</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">GST Number</label>
                    <input type="text" name="gst_number" class="form-control" value="<?= old('gst_number', $branch['gst_number'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">License Number</label>
                    <input type="text" name="license_number" class="form-control" value="<?= old('license_number', $branch['license_number'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Branch</button>
                <a href="<?= url('branches') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
