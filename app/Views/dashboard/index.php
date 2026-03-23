<?php
$title = 'Dashboard';
ob_start();

$modules = [
    ['key' => 'categories',         'label' => 'Categories',         'icon' => 'bi-tags-fill',           'color' => 'primary',   'url' => 'categories', 'perm' => 'Categories'],
    ['key' => 'products',           'label' => 'Products',           'icon' => 'bi-box-seam-fill',        'color' => 'success',   'url' => 'products', 'perm' => 'Products'],
    ['key' => 'branches',           'label' => 'Branches',           'icon' => 'bi-building-fill',        'color' => 'info',      'url' => 'branches', 'perm' => 'Branches'],
    ['key' => 'branch_admins',      'label' => 'Branch Admins',      'icon' => 'bi-person-badge-fill',    'color' => 'warning',   'url' => 'branch-admins', 'perm' => 'Branch Admins'],
    ['key' => 'branch_roles',       'label' => 'Branch Roles',       'icon' => 'bi-shield-fill',          'color' => 'secondary', 'url' => 'branch-roles', 'perm' => 'Branch Roles'],
    ['key' => 'branch_staff',       'label' => 'Branch Staff',       'icon' => 'bi-people-fill',          'color' => 'dark',      'url' => 'branch-staff', 'perm' => 'Branch Staff'],
    ['key' => 'customers',          'label' => 'Customers',          'icon' => 'bi-person-fill',          'color' => 'primary',   'url' => 'customers', 'perm' => 'Customers'],
    ['key' => 'customer_addresses', 'label' => 'Addresses',          'icon' => 'bi-geo-alt-fill',         'color' => 'success',   'url' => 'customer-addresses', 'perm' => 'Customer Addresses'],
    ['key' => 'orders',             'label' => 'Orders',             'icon' => 'bi-cart-fill',            'color' => 'danger',    'url' => 'orders', 'perm' => 'Orders'],
    ['key' => 'payments',           'label' => 'Payments',           'icon' => 'bi-credit-card-fill',     'color' => 'info',      'url' => 'payments', 'perm' => 'Payments'],
    ['key' => 'coupons',            'label' => 'Coupons',            'icon' => 'bi-ticket-perforated-fill','color' => 'warning',  'url' => 'coupons', 'perm' => 'Coupons'],
    ['key' => 'reviews',            'label' => 'Reviews',            'icon' => 'bi-star-fill',            'color' => 'secondary', 'url' => 'reviews', 'perm' => 'Reviews'],
];

$modules = array_filter($modules, fn($m) => hasPermission($m['perm']));

$orderStatusColors = [
    'pending'    => 'warning',
    'processing' => 'info',
    'shipped'    => 'primary',
    'delivered'  => 'success',
    'cancelled'  => 'danger',
];
?>

<!-- Stat Summary Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="fw-bold fs-5">₹<?= number_format($revenue, 2) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-cart-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Orders</div>
                    <div class="fw-bold fs-5"><?= number_format($counts['orders']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-person-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Customers</div>
                    <div class="fw-bold fs-5"><?= number_format($counts['customers']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-star-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Avg. Rating</div>
                    <div class="fw-bold fs-5"><?= $avgRating ?> / 5</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Successful Payments</div>
            <div class="fw-bold fs-5 text-success"><?= max(0, $counts['payments'] - $pendingPayments - $failedPayments) ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Pending Payments</div>
            <div class="fw-bold fs-5 text-warning"><?= $pendingPayments ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Failed Payments</div>
            <div class="fw-bold fs-5 text-danger"><?= $failedPayments ?></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Order Status Breakdown -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-bottom">Order Status Breakdown</div>
            <div class="card-body">
                <?php if (empty($orderStats)): ?>
                    <p class="text-muted small">No orders yet.</p>
                <?php else: ?>
                    <?php foreach ($orderStats as $status => $cnt): ?>
                        <?php $color = $orderStatusColors[$status] ?? 'secondary'; ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-capitalize small"><?= e($status) ?></span>
                                <span class="badge bg-<?= $color ?>"><?= $cnt ?></span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar bg-<?= $color ?>"
                                     style="width:<?= $counts['orders'] ? round($cnt / $counts['orders'] * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                Recent Orders
                <a href="<?= url('orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Order No.</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No orders found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $i => $o): ?>
                                    <?php $sc = $orderStatusColors[$o['order_status'] ?? ''] ?? 'secondary'; ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td>
                                            <a href="<?= url('orders/' . $o['id'] . '/edit') ?>" class="text-decoration-none">
                                                <?= e($o['order_number'] ?? '#' . $o['id']) ?>
                                            </a>
                                        </td>
                                        <td>₹<?= number_format((float)($o['final_amount'] ?? 0), 2) ?></td>
                                        <td><span class="badge bg-<?= $sc ?> text-capitalize"><?= e($o['order_status'] ?? '') ?></span></td>
                                        <td><span class="badge bg-<?= $o['payment_status'] === 'paid' ? 'success' : 'warning' ?> text-capitalize"><?= e($o['payment_status'] ?? '') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Module Cards -->
<h6 class="text-muted fw-semibold mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">All Modules</h6>
<div class="row g-3">
    <?php foreach ($modules as $m): ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= url($m['url']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 module-card">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-3 p-2 bg-<?= $m['color'] ?> bg-opacity-10 text-<?= $m['color'] ?>" style="min-width:44px;text-align:center">
                            <i class="bi <?= $m['icon'] ?> fs-4"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-muted small text-truncate"><?= $m['label'] ?></div>
                            <div class="fw-bold fs-5 text-dark"><?= number_format($counts[$m['key']] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<style>
.module-card { transition: transform .15s, box-shadow .15s; }
.module-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1) !important; }
</style>

<?php
$content = ob_get_clean();
require APP . '/Views/layouts/app.php';
