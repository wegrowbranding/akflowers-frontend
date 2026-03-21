<?php $title = 'Payments'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Payments</li>
    </ol></nav>
    <a href="<?= url('payments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Payment</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= url('payments') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search transaction ID..." value="<?= e($search) ?>">
            <button class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= url('payments') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a><?php endif; ?>
        </form>
        <div class="table-responsive">
            <table id="paymentsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Order ID</th><th>Transaction ID</th><th>Gateway</th><th>Amount</th><th>Status</th><th>Paid At</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No payments found.</td></tr>
                <?php else:
                    $sc = ['pending'=>'warning','success'=>'success','failed'=>'danger'];
                    foreach ($payments as $idx => $p): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td><a href="<?= url('orders/'.$p['order_id'].'/edit') ?>" class="badge bg-light text-dark border text-decoration-none">#<?= $p['order_id'] ?></a></td>
                        <td><code><?= e($p['transaction_id'] ?? '—') ?></code></td>
                        <td><?= e($p['payment_gateway'] ?? '—') ?></td>
                        <td class="fw-semibold"><?= number_format((float)($p['amount'] ?? 0), 2) ?></td>
                        <td><span class="badge bg-<?= $sc[$p['status'] ?? 'pending'] ?? 'secondary' ?>"><?= ucfirst($p['status'] ?? 'pending') ?></span></td>
                        <td class="text-muted small"><?= $p['paid_at'] ? date('d M Y H:i', strtotime($p['paid_at'])) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= url('payments/'.$p['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('payments/'.$p['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this payment?')">
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
$scripts = '<script>$(function(){ $("#paymentsTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[7]}] }); });</script>';
$content = ob_get_clean(); require APP . '/Views/layouts/app.php';
