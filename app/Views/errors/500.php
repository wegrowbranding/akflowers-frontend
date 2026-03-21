<?php
$title = 'Server Error';
http_response_code(500);
ob_start();
?>
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger"><i class="bi bi-exclamation-triangle"></i> 500</h1>
        <h3 class="mb-4">Internal Server Error</h3>
        <p class="text-muted mb-4">Oops! Something went wrong on our end.</p>
        <?php if (!empty($exceptionMessage)): ?>
            <div class="alert alert-danger text-start mx-auto" style="max-width: 600px;">
                <strong>Error details:</strong> <?= e($exceptionMessage) ?>
            </div>
        <?php endif; ?>
        <a href="<?= url('dashboard') ?>" class="btn btn-primary"><i class="bi bi-house me-1"></i> Return to Dashboard</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
