<?php
$title = 'Page Not Found';
http_response_code(404);
ob_start();
?>
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-warning"><i class="bi bi-question-circle"></i> 404</h1>
        <h3 class="mb-4">Page Not Found</h3>
        <p class="text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="<?= url('dashboard') ?>" class="btn btn-primary"><i class="bi bi-house me-1"></i> Return to Dashboard</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
