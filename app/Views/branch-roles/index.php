<?php $title = 'Branch Roles'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Branch Roles</li>
        </ol>
    </nav>
    <a href="<?= url('branch-roles/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Role
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= url('branch-roles') ?>" class="mb-3 d-flex gap-2" style="max-width:380px">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search roles..." value="<?= e($search) ?>">
            <button class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="<?= url('branch-roles') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a><?php endif; ?>
        </form>

        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Branch</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($roles)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No roles found.</td></tr>
                <?php else: foreach ($roles as $idx => $r): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td class="fw-medium"><?= e($r['role_name']) ?></td>
                        <td class="text-muted small"><?= e(mb_strimwidth($r['role_description'] ?? '', 0, 60, '…')) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($r['branch']['branch_name'] ?? $r['branch_id'] ?? '—') ?></span></td>
                        <td>
                            <?php if ($r['is_default']): ?>
                                <span class="badge bg-primary">Default</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-<?= ($r['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($r['status'] ?? 'active') ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('branch-roles/' . $r['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('branch-roles/' . $r['id'] . '/delete') ?>" class="d-inline"
                                  onsubmit="return confirm('Delete \'<?= e(addslashes($r['role_name'])) ?>\'?')">
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
                <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
                    <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
            </ul></nav>
        </div>
        <?php else: ?><small class="text-muted d-block mt-2">Total: <?= $total ?> records</small><?php endif; ?>
    </div>
</div>
<?php
$scripts = '<script>$(function(){ $("#rolesTable").DataTable({ paging:false, searching:false, info:false, columnDefs:[{orderable:false,targets:[6]}] }); });</script>';
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
