<?php
$title = 'Login';
ob_start();
?>
<h4 class="fw-bold mb-1">Welcome back</h4>
<p class="text-muted mb-4 small">Sign in to your account</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger py-2 small">
        <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $flash = getFlash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> py-2 small"><?= e($flash['message']) ?></div>
<?php endif; ?>

<form method="POST" action="<?= url('auth/login') ?>">
    <div class="mb-3">
        <label class="form-label fw-medium">Email</label>
        <input type="email" name="email" class="form-control" required
               value="<?= old('email') ?>" placeholder="admin@example.com">
    </div>
    <div class="mb-4">
        <label class="form-label fw-medium">Password</label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>
    <button class="btn btn-primary w-100 fw-semibold">Sign In</button>
</form>
<p class="text-center mt-3 mb-0 small">
    No account? <a href="<?= url('auth/register') ?>">Register here</a>
</p>
<?php
$content = ob_get_clean();
require APP . '/Views/layouts/auth.php';
