<?php
$title = 'Delivery Proofs';
ob_start();
?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Delivery Proofs</li>
        </ol>
    </nav>
</div>

<!-- Order Search Header -->
<div class="card mb-4 border-0 shadow-sm bg-info text-white">
    <div class="card-body py-4 text-center">
        <h5 class="mb-3">Select an Order to View Proofs</h5>
        <form method="GET" action="<?= url('delivery-proofs') ?>" class="row g-2 justify-content-center">
            <div class="col-md-6">
                <select name="order_id" class="form-select select2" required onchange="this.form.submit()">
                    <option value="">Search by Order # or Final Amount...</option>
                    <?php foreach($orders as $o): ?>
                        <option value="<?= $o['id'] ?>" <?= (int)$orderId === (int)$o['id'] ? 'selected' : '' ?>>
                            Order #<?= e($o['order_number'] ?? $o['id']) ?> - ₹<?= number_format($o['final_amount'] ?? 0, 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($orderId): ?>
                <div class="col-auto">
                    <a href="<?= url('delivery-proofs') ?>" class="btn btn-light"><i class="bi bi-x-lg"></i></a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($orderId): ?>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table id="proofsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Assignment</th>
                        <th>Type</th>
                        <th>Code/Image</th>
                        <th>Verified</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($proofs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No proofs found for this order.</td></tr>
                <?php else: ?>
                    <?php foreach ($proofs as $idx => $p): ?>
                    <tr>
                        <td class="text-muted small"><?= (($page - 1) * $limit) + $idx + 1 ?></td>
                        <td>
                            <div class="fw-bold">Assignment #<?= e($p['assignment_id']) ?></div>
                        </td>
                        <td class="text-capitalize"><?= e($p['proof_type']) ?></td>
                        <td>
                            <?php if ($p['proof_type'] === 'otp'): ?>
                                <span class="badge bg-light text-dark font-monospace"><?= e($p['otp_code']) ?></span>
                            <?php elseif ($p['image_path']): ?>
                                <a href="<?= e($p['image_path']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-image me-1"></i> View Image
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-<?= $p['verified'] ? 'success' : 'warning' ?>">
                                <?= $p['verified'] ? 'Verified' : 'Pending' ?>
                            </span>
                        </td>
                        <td class="small"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <small class="text-muted">
                Showing page <?= $page ?> of <?= $totalPages ?> (<?= $total ?> total)
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('delivery-proofs') ?>?page=<?= $page - 1 ?>&order_id=<?= $orderId ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('delivery-proofs') ?>?page=<?= $i ?>&order_id=<?= $orderId ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('delivery-proofs') ?>?page=<?= $page + 1 ?>&order_id=<?= $orderId ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php else: ?>
            <small class="text-muted d-block mt-2">Total: <?= $total ?> records</small>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="text-center py-5">
    <img src="https://illustrations.popsy.co/blue/uploading.svg" style="max-width:300px; opacity:0.8; filter:grayscale(0.3)" alt="Select Order">
    <h5 class="text-muted mt-4">Please select an order from the dropdown above to view its delivery proofs.</h5>
</div>
<?php endif; ?>

<?php
$scripts = <<<JS
<script>
$(function() {
    $('#proofsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true
    });
});
</script>
JS;
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
