<?php
$title = 'Delivery Staff';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Delivery Staff</li>
        </ol>
    </nav>
    <a href="<?= url('delivery-staff/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Delivery Staff
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search -->
        <form method="GET" action="<?= url('delivery-staff') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search staff..." value="<?= e($search ?? '') ?>">
            <button class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?= url('delivery-staff') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table id="staffTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Vehicle Type</th>
                        <th>Vehicle Number</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($staff)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No delivery staff found.</td></tr>
                <?php else: ?>
                    <?php foreach ($staff as $idx => $s): ?>
                    <tr>
                        <td class="text-muted small"><?= (($page - 1) * $limit) + $idx + 1 ?></td>
                        <td class="fw-medium">
                            <?= e($s['staff']['full_name'] ?? 'Unknown') ?>
                            <div class="small text-muted"><?= e($s['staff']['email'] ?? '') ?></div>
                        </td>
                        <td class="text-capitalize"><?= e($s['vehicle_type']) ?></td>
                        <td><?= e($s['vehicle_number'] ?: '—') ?></td>
                        <td>
                            <span class="badge rounded-pill bg-<?= $s['is_available'] ? 'success' : 'danger' ?>">
                                <?= $s['is_available'] ? 'Available' : 'Busy/Offline' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('delivery-staff/' . $s['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= url('delivery-staff/' . $s['id'] . '/delete') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this delivery staff?')">
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
                            <a class="page-link" href="<?= url('delivery-staff') ?>?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('delivery-staff') ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('delivery-staff') ?>?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
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
    $('#staffTable').DataTable({
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
