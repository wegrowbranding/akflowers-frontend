<?php $title = 'Orders'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Orders</li>
    </ol></nav>
    <a href="<?= url('orders/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Order</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= url('orders') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search order number..." value="<?= e($search) ?>">
            <button class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= url('orders') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a><?php endif; ?>
        </form>
        <div class="table-responsive">
            <table id="ordersTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Order No.</th><th>Customer ID</th><th>Total</th><th>Discount</th><th>Final</th><th>Payment</th><th>Order Status</th><th>Method</th><th>Placed At</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">No orders found.</td></tr>
                <?php else:
                    $payColors   = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'info'];
                    $orderColors = ['pending'=>'secondary','confirmed'=>'primary','packed'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger','returned'=>'warning'];
                    foreach ($orders as $idx => $o): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td class="fw-medium"><code><?= e($o['order_number'] ?? '—') ?></code></td>
                        <td><span class="badge bg-light text-dark border"><?= $o['customer_id'] ?></span></td>
                        <td><?= number_format((float)($o['total_amount'] ?? 0), 2) ?></td>
                        <td class="text-muted"><?= number_format((float)($o['discount_amount'] ?? 0), 2) ?></td>
                        <td class="fw-semibold"><?= number_format((float)($o['final_amount'] ?? 0), 2) ?></td>
                        <td><span class="badge bg-<?= $payColors[$o['payment_status'] ?? 'pending'] ?? 'secondary' ?>"><?= ucfirst($o['payment_status'] ?? 'pending') ?></span></td>
                        <td><span class="badge bg-<?= $orderColors[$o['order_status'] ?? 'pending'] ?? 'secondary' ?>"><?= ucfirst($o['order_status'] ?? 'pending') ?></span></td>
                        <td class="text-muted small"><?= e($o['payment_method'] ?? '—') ?></td>
                        <td class="text-muted small"><?= $o['placed_at'] ? date('d M Y', strtotime($o['placed_at'])) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= url('orders/'.$o['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('orders/'.$o['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete order #<?= e($o['order_number'] ?? $o['id']) ?>?')">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <small class="text-muted">Page <?= $page ?> of <?= $totalPages ?> (<?= $total ?> total)</small>
            <nav><ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
                <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li><?php endfor; ?>
                <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
            </ul></nav>
        </div>
        <?php else: ?><small class="text-muted d-block mt-2">Total: <?= $total ?> records</small><?php endif; ?>
    </div>
</div>
<?php
$scripts = '<script>$(function(){ $("#ordersTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[10]}] }); });</script>';
$content = ob_get_clean(); require APP . '/Views/layouts/app.php';
