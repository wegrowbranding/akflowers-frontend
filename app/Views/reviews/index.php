<?php $title = 'Reviews'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Reviews</li>
    </ol></nav>
    <a href="<?= url('reviews/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Review</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="reviewsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Product ID</th><th>Customer ID</th><th>Rating</th><th>Review</th><th>Created At</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($reviews)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No reviews found.</td></tr>
                <?php else: foreach ($reviews as $idx => $r): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $r['product_id'] ?></span></td>
                        <td><span class="badge bg-light text-dark border"><?= $r['customer_id'] ?></span></td>
                        <td>
                            <span class="text-warning">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="bi bi-star<?= $s <= (int)$r['rating'] ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <small class="text-muted ms-1">(<?= $r['rating'] ?>)</small>
                        </td>
                        <td class="text-muted small" style="max-width:220px"><?= e(mb_strimwidth($r['review'] ?? '', 0, 80, '…')) ?></td>
                        <td class="text-muted small"><?= $r['created_at'] ? date('d M Y', strtotime($r['created_at'])) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= url('reviews/'.$r['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('reviews/'.$r['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this review?')">
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
                <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
                <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?>
                <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
            </ul></nav>
        </div>
        <?php else: ?><small class="text-muted d-block mt-2">Total: <?= $total ?> records</small><?php endif; ?>
    </div>
</div>
<?php
$scripts = '<script>$(function(){ $("#reviewsTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[6]}] }); });</script>';
$content = ob_get_clean(); require APP . '/Views/layouts/app.php';
