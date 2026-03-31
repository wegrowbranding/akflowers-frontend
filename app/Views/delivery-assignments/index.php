<?php
$title = 'Delivery Assignments';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Delivery Assignments</li>
        </ol>
    </nav>
    <a href="<?= url('delivery-assignments/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Assignment
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- Order Filter -->
        <form method="GET" action="<?= url('delivery-assignments') ?>" class="mb-3 d-flex gap-2" style="max-width:500px">
            <select name="order_id" class="form-select form-select-sm select2">
                <option value="">All Orders</option>
                <?php foreach($orders as $o): ?>
                    <option value="<?= $o['id'] ?>" <?= (int)$orderId === (int)$o['id'] ? 'selected' : '' ?>>
                        Order #<?= e($o['order_number'] ?? $o['id']) ?> - ₹<?= number_format($o['final_amount'] ?? 0, 2) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-filter"></i>
            </button>
            <?php if ($orderId): ?>
                <a href="<?= url('delivery-assignments') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table id="assignmentsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Staff Name</th>
                        <th>Assigned At</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No delivery assignments found.</td></tr>
                <?php else: ?>
                    <?php foreach ($assignments as $idx => $a): ?>
                    <tr>
                        <td class="text-muted small"><?= (($page - 1) * $limit) + $idx + 1 ?></td>
                        <td>
                            <a href="<?= url('orders/' . $a['order']['id'] . '/edit') ?>" class="text-decoration-none fw-bold">
                                #<?= e($a['order']['order_number'] ?? $a['order']['id']) ?>
                            </a>
                        </td>
                        <td class="fw-medium">
                            <?= e($a['delivery_staff']['staff']['full_name'] ?? 'Unknown') ?>
                        </td>
                        <td class="small">
                            <?= date('d M Y H:i', strtotime($a['assigned_at'])) ?>
                        </td>
                        <td>
                            <?php 
                                $statusClasses = [
                                    'assigned'         => 'secondary',
                                    'accepted'         => 'info',
                                    'picked_up'        => 'warning',
                                    'out_for_delivery' => 'primary',
                                    'delivered'        => 'success',
                                    'failed'           => 'danger'
                                ];
                                $cls = $statusClasses[$a['status']] ?? 'secondary';
                            ?>
                            <span class="badge rounded-pill bg-<?= $cls ?> text-capitalize">
                                <?= str_replace('_', ' ', $a['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('delivery-assignments/' . $a['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= url('delivery-assignments/' . $a['id'] . '/delete') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this assignment?')">
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
                            <a class="page-link" href="<?= url('delivery-assignments') ?>?page=<?= $page - 1 ?>&order_id=<?= $orderId ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('delivery-assignments') ?>?page=<?= $i ?>&order_id=<?= $orderId ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('delivery-assignments') ?>?page=<?= $page + 1 ?>&order_id=<?= $orderId ?>">
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

<?php
$scripts = <<<JS
<script>
$(function() {
    $('#assignmentsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        columnDefs: [{ orderable: false, targets: [5] }]
    });
});
</script>
JS;
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
