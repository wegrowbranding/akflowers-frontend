<?php
$title = 'Register';
ob_start();
?>
<h4 class="fw-bold mb-1">Create account</h4>
<p class="text-muted mb-4 small">Fill in the details below</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger py-2 small">
        <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('auth/register') ?>">
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label fw-medium">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control" required value="<?= old('username') ?>">
        </div>
        <div class="col-6">
            <label class="form-label fw-medium">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?>">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label fw-medium">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
    </div>
    <div class="mb-4">
        <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <button class="btn btn-success w-100 fw-semibold">Create Account</button>
</form>
<p class="text-center mt-3 mb-0 small">
    Already have an account? <a href="<?= url('auth/login') ?>">Sign in</a>
</p>
<?php
$content = ob_get_clean();
require APP . '/Views/layouts/auth.php';
