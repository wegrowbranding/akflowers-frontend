<?php $title = 'Coupons'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Coupons</li>
    </ol></nav>
    <a href="<?= url('coupons/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Coupon</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= url('coupons') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search coupon code..." value="<?= e($search) ?>">
            <button class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= url('coupons') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a><?php endif; ?>
        </form>
        <div class="table-responsive">
            <table id="couponsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Valid From</th><th>Valid To</th><th>Limit</th><th>Used</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($coupons)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">No coupons found.</td></tr>
                <?php else: foreach ($coupons as $idx => $c): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td><span class="badge bg-dark fs-6 fw-bold"><?= e($c['code']) ?></span></td>
                        <td>
                            <?php if ($c['discount_type'] === 'percentage'): ?>
                                <span class="badge bg-info text-dark">%</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Fixed</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= $c['discount_type'] === 'percentage' ? $c['discount_value'].'%' : '₹'.number_format((float)$c['discount_value'],2) ?></td>
                        <td><?= $c['min_order_amount'] ? '₹'.number_format((float)$c['min_order_amount'],2) : '—' ?></td>
                        <td class="text-muted small"><?= $c['valid_from'] ? date('d M Y', strtotime($c['valid_from'])) : '—' ?></td>
                        <td class="text-muted small"><?= $c['valid_to'] ? date('d M Y', strtotime($c['valid_to'])) : '—' ?></td>
                        <td><?= $c['usage_limit'] ?? '∞' ?></td>
                        <td><?= $c['used_count'] ?? 0 ?></td>
                        <td><span class="badge rounded-pill bg-<?= ($c['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($c['status'] ?? 'active') ?></span></td>
                        <td class="text-end">
                            <a href="<?= url('coupons/'.$c['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('coupons/'.$c['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete coupon <?= e($c['code']) ?>?')">
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
$scripts = '<script>$(function(){ $("#couponsTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[10]}] }); });</script>';
$content = ob_get_clean(); require APP . '/Views/layouts/app.php';
