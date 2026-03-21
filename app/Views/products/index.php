<?php
$title = 'Products';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>
    <a href="<?= url('products/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search -->
        <form method="GET" action="<?= url('products') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search products..." value="<?= e($search) ?>">
            <button class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-search"></i>
            </button>
            <?php if ($search): ?>
                <a href="<?= url('products') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table id="productsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Cost</th>
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="10" class="text-center text-muted py-4">No products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $idx => $p): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td><code><?= e($p['product_code']) ?></code></td>
                        <td class="fw-medium"><?= e($p['product_name']) ?></td>
                        <td><?= e($p['category']['category_name'] ?? '—') ?></td>
                        <td><?= number_format((float)($p['price'] ?? 0), 2) ?></td>
                        <td class="text-muted"><?= number_format((float)($p['cost_price'] ?? 0), 2) ?></td>
                        <td>
                            <span class="badge bg-<?= (int)($p['stock_quantity'] ?? 0) > 0 ? 'info' : 'warning' ?> text-dark">
                                <?= (int)($p['stock_quantity'] ?? 0) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= e($p['unit'] ?? '—') ?></td>
                        <td>
                            <span class="badge rounded-pill bg-<?= ($p['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($p['status'] ?? 'active') ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('products/' . $p['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= url('products/' . $p['id'] . '/delete') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete \'<?= e(addslashes($p['product_name'])) ?>\'?')">
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
                            <a class="page-link" href="<?= url('products') ?>?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('products') ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('products') ?>?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
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
    $('#productsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        columnDefs: [{ orderable: false, targets: [9] }]
    });
});
</script>
JS;
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
