<?php $title = 'Carts'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Carts</li>
    </ol></nav>
    <a href="<?= url('carts/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Cart</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= url('carts') ?>" class="mb-3 d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label fw-medium small mb-1">Filter by Customer</label>
                <select name="customer_id" class="form-select form-select-sm" style="min-width:220px" onchange="this.form.submit()">
                    <option value="">— Select Customer —</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int)$customerId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['full_name']) ?> (#<?= $c['id'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-search"></i> Load</button>
            <?php if ($customerId): ?><a href="<?= url('carts') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a><?php endif; ?>
        </form>

        <?php if (!$customerId): ?>
            <div class="text-center text-muted py-5"><i class="bi bi-cart3 fs-1 d-block mb-2"></i>Select a customer to view their carts.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="cartsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Customer ID</th><th>Items</th><th>Created At</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($carts)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No carts found for this customer.</td></tr>
                <?php else: foreach ($carts as $cart): ?>
                    <tr>
                        <td class="text-muted small"><?= $cart['id'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $cart['customer_id'] ?></span></td>
                        <td>
                            <?php $itemCount = count($cart['items'] ?? []); ?>
                            <span class="badge bg-info text-dark"><?= $itemCount ?> item<?= $itemCount !== 1 ? 's' : '' ?></span>
                            <?php if (!empty($cart['items'])): ?>
                                <small class="text-muted ms-1">
                                    (Products: <?= implode(', ', array_column($cart['items'], 'product_id')) ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= $cart['created_at'] ? date('d M Y H:i', strtotime($cart['created_at'])) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= url('carts/'.$cart['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('carts/'.$cart['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this cart and all its items?')">
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
                <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&customer_id=<?= $customerId ?>"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
                <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&customer_id=<?= $customerId ?>"><?= $i ?></a></li><?php endfor; ?>
                <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&customer_id=<?= $customerId ?>"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
            </ul></nav>
        </div>
        <?php else: ?><small class="text-muted d-block mt-2">Total: <?= $total ?> records</small><?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php
$scripts = '<script>$(function(){ if($("#cartsTable").length){ $("#cartsTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[4]}] }); } });</script>';
$content = ob_get_clean(); require APP . '/Views/layouts/app.php';
