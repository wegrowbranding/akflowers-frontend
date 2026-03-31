<?php
$title = 'Delivery Tracking';
ob_start();
?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Delivery Tracking</li>
        </ol>
    </nav>
</div>

<!-- Order Search Header -->
<div class="card mb-4 border-0 shadow-sm bg-dark text-white">
    <div class="card-body py-4 text-center">
        <h5 class="mb-3">Select an Order to Track</h5>
        <form method="GET" action="<?= url('delivery-tracking') ?>" class="row g-2 justify-content-center">
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
                    <a href="<?= url('delivery-tracking') ?>" class="btn btn-outline-light"><i class="bi bi-x-lg"></i></a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($orderId): ?>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table id="trackingTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Assignment</th>
                        <th>Staff</th>
                        <th>Last Latitude</th>
                        <th>Last Longitude</th>
                        <th>Last Recorded</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($tracking)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No tracking data found for this order.</td></tr>
                <?php else: ?>
                    <?php 
                    // Note: API already filters by order_id, so we just list what we get
                    foreach ($tracking as $idx => $t): 
                    ?>
                    <tr>
                        <td class="text-muted small"><?= (($page - 1) * $limit) + $idx + 1 ?></td>
                        <td>
                            <div class="fw-bold">Assignment #<?= e($t['assignment_id']) ?></div>
                        </td>
                        <td><?= e($t['assignment']['delivery_staff']['staff']['full_name'] ?? 'Unknown') ?></td>
                        <td class="small font-monospace"><?= e($t['latitude']) ?></td>
                        <td class="small font-monospace"><?= e($t['longitude']) ?></td>
                        <td class="small"><?= date('d M Y H:i', strtotime($t['recorded_at'])) ?></td>
                        <td class="text-end">
                            <a href="<?= url('delivery-tracking/' . $t['assignment_id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-geo-alt me-1"></i> Track Live
                            </a>
                        </td>
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
                            <a class="page-link" href="<?= url('delivery-tracking') ?>?page=<?= $page - 1 ?>&order_id=<?= $orderId ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('delivery-tracking') ?>?page=<?= $i ?>&order_id=<?= $orderId ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('delivery-tracking') ?>?page=<?= $page + 1 ?>&order_id=<?= $orderId ?>">
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
    <img src="https://illustrations.popsy.co/blue/map.svg" style="max-width:300px; opacity:0.8; filter:grayscale(0.3)" alt="Select Order">
    <h5 class="text-muted mt-4">Please select an order from the dropdown above to start tracking.</h5>
</div>
<?php endif; ?>

<?php
$scripts = <<<JS
<script>
$(function() {
    $('#trackingTable').DataTable({
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
