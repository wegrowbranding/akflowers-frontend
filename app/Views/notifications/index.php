<?php
$title = 'Custom Notifications';
ob_start();

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-primary">
                    <i class="bi bi-broadcast fs-4 me-2"></i> Send Broadcast Notification
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info border-0 shadow-sm mb-4 py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Broadcast to all active users:</strong> This will send a real-time push notification to all customers who have been active on their mobile devices in the last 30 days.
                        </div>
                    </div>
                </div>

                <form action="<?= url('notifications/send') ?>" method="POST">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Notification Title</label>
                        <input type="text" class="form-control form-control-lg border-2" id="title" name="title" 
                               placeholder="e.g. Special Weekend Sale! 🏷️" required>
                        <div class="form-text mt-2 text-muted">Keep it catchy and under 50 characters.</div>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">Message Body</label>
                        <textarea class="form-control border-2" id="message" name="message" rows="4" 
                                  placeholder="e.g. Get 20% off on all flowers this weekend. Use code FLOWERS20." required></textarea>
                        <div class="form-text mt-2 text-muted">A clear message increases click-through rates.</div>
                    </div>

                    <div class="mb-4">
                        <label for="image_url" class="form-label fw-semibold">Image URL (Optional)</label>
                        <input type="url" class="form-control border-2" id="image_url" name="image_url" 
                               placeholder="https://example.com/banner.jpg">
                        <div class="form-text mt-2 text-muted">Enter a direct link to an image (JPG/PNG).</div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm"
                                onclick="return confirm('Are you sure you want to broadcast this notification to all users?')">
                            <i class="bi bi-send-fill me-2"></i> Send to All Users
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="card-title mb-0 fw-semibold text-muted">
                    <i class="bi bi-lightbulb me-2"></i> Content Tips
                </h6>
            </div>
            <div class="card-body p-4 pt-0">
                <ul class="text-muted small mb-0">
                    <li class="mb-2"><strong>Emojis:</strong> Using emojis (like 🌸, 🚚, 🛍️) can significantly improve engagement.</li>
                    <li class="mb-2"><strong>Timing:</strong> Best times to send are usually midday or early evening.</li>
                    <li class="mb-0"><strong>Frequency:</strong> Avoid sending more than one broad notification per day.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .15);
    border-color: #0d6efd;
}
.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
    border: none;
    transition: all .2s;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
}
</style>

<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
