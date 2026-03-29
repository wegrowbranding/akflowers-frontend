<?php
$title = 'Banners';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Banners</li>
        </ol>
    </nav>
    <a href="<?= url('banners/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Banner
    </a>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold py-3">All Banners</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">S.No</th>
                    <th width="200">Image</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th width="120" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No banners found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($banners as $index => $banner): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($banner['media'])): ?>
                                    <img src="<?= API_BASE . '/media/' . $banner['media']['id'] . '/view' ?>" 
                                         class="rounded border" style="width: 150px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($banner['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y H:i', strtotime($banner['created_at'])) ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('banners/' . $banner['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= url('banners/' . $banner['id'] . '/delete') ?>" 
                                          style="display:inline" onsubmit="return confirm('Delete this banner?')">
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
