<?php
$title = 'Track Assignment #' . $id;
ob_start();
?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('delivery-tracking') ?>">Tracking</a></li>
            <li class="breadcrumb-item active">Assignment #<?= e($id) ?></li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-9">
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold">Live Tracking Map</h6>
                <span class="badge bg-success bullet">Live Updates Active</span>
            </div>
            <!-- Mock Tracking Map Area -->
            <div id="tracking-map" style="height: 500px; background: #e2e8f0; display:flex; align-items:center; justify-content:center; flex-direction:column; position:relative;">
                <i class="bi bi-geo-alt-fill text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-3 text-secondary">Google Maps Integration Placeholder</h4>
                <p class="text-muted small">Tracking Assignment #<?= $id ?>... Current Location: <?= e($tracking[0]['latitude'] ?? '—') ?>, <?= e($tracking[0]['longitude'] ?? '—') ?></p>
                <div class="position-absolute bottom-0 w-100 p-3 bg-white border-top small text-muted">
                    <i class="bi bi-info-circle me-1"></i> Staff: <?= e($tracking[0]['assignment']['delivery_staff']['staff']['full_name'] ?? 'Loading...') ?> | Vehicle: <?= e($tracking[0]['assignment']['delivery_staff']['vehicle_number'] ?? '—') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="card-title mb-0 fw-bold">Timeline</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small">
                    <?php if (empty($tracking)): ?>
                        <div class="p-4 text-center text-muted">No coordinate history</div>
                    <?php else: ?>
                        <?php foreach($tracking as $idx => $t): ?>
                        <div class="list-group-item py-3 <?= $idx === 0 ? 'bg-light' : '' ?>">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <span class="fw-bold <?= $idx === 0 ? 'text-primary' : '' ?>">Point Recorded</span>
                                <span class="text-muted" style="font-size:.7rem;"><?= date('H:i:s', strtotime($t['recorded_at'])) ?></span>
                            </div>
                            <div class="font-monospace text-muted smaller d-flex justify-content-between">
                                <span>Lat: <?= round($t['latitude'], 4) ?></span>
                                <span>Lon: round($t['longitude'], 4) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
