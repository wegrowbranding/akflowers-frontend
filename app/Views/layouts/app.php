<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --sidebar-width: 240px; }

        body {
            background: #f1f5f9;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #1e293b;
            position: fixed;
            top: 0;
            left: 0;

            display: flex;
            flex-direction: column;

            padding: 1.5rem 1rem;
            z-index: 100;

            overflow-y: auto; /* ✅ FIXED SCROLL */
            overflow-x: hidden;

            transition: transform 0.3s ease;
        }

        #sidebar .brand {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }

        #sidebar .nav-link {
            color: #94a3b8;
            border-radius: 8px;
            padding: .55rem .85rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .92rem;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

            transition: background .15s, color .15s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: #334155;
            color: #fff;
        }

        #sidebar .nav-link.text-danger:hover {
            background: #450a0a;
            color: #fca5a5;
        }

        /* Scrollbar (optional nice UI) */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        /* MAIN */
        #main {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .topbar {
            background: #fff;
            border-radius: 12px;
            padding: .75rem 1.25rem;
            margin-bottom: 1.5rem;

            display: flex;
            align-items: center;
            justify-content: space-between;

            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }

        .table th {
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
        }

        /* OVERLAY */
        #sidebarOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 99;
            display: none;
        }

        #sidebarOverlay.show {
            display: block;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #main {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <a href="<?= url('dashboard') ?>" class="brand">
        <i class="bi bi-shop-window"></i> <?= APP_NAME ?>
    </a>

    <?php
    function isActive($routes) {
        $uri = trim($_SERVER['REQUEST_URI'], '/');

        foreach ((array)$routes as $route) {
            if (str_contains($uri, trim($route, '/'))) {
                return 'active';
            }
        }

        return '';
    }
    ?>

    <ul class="nav flex-column gap-1">

        <!-- Dashboard -->
        <li>
            <a href="<?= url('dashboard') ?>" class="nav-link <?= isActive('dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <?php if (hasPermission('Categories')): ?>
        <!-- Categories -->
        <li>
            <a href="<?= url('categories') ?>" class="nav-link <?= isActive('categories') ?>">
                <i class="bi bi-tags-fill"></i> Categories
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Products')): ?>
        <!-- Products -->
        <li>
            <a href="<?= url('products') ?>" class="nav-link <?= isActive('products') ?>">
                <i class="bi bi-box-seam-fill"></i> Products
            </a>
        </li>
        <?php endif; ?>

        <!-- Branch Section -->
        <?php if (hasPermission('Branches') || hasPermission('Branch Admins') || hasPermission('Branch Roles') || hasPermission('Branch Staff')): ?>
        <li class="mt-3 mb-1 px-2">
            <span style="font-size:.7rem;font-weight:700;color:#475569">Branch</span>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Branches')): ?>
        <li>
            <a href="<?= url('branches') ?>" class="nav-link <?= isActive('branches') ?>">
                <i class="bi bi-building"></i> Branches
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Branch Admins')): ?>
        <li>
            <a href="<?= url('branch-admins') ?>" class="nav-link <?= isActive('branch-admins') ?>">
                <i class="bi bi-person-badge-fill"></i> Branch Admins
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Branch Roles')): ?>
        <li>
            <a href="<?= url('branch-roles') ?>" class="nav-link <?= isActive('branch-roles') ?>">
                <i class="bi bi-shield-fill"></i> Roles
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Branch Staff')): ?>
        <li>
            <a href="<?= url('branch-staff') ?>" class="nav-link <?= isActive('branch-staff') ?>">
                <i class="bi bi-people-fill"></i> Staff
            </a>
        </li>
        <?php endif; ?>

        <!-- Commerce Section -->
        <?php if (hasPermission('Orders') || hasPermission('Payments') || hasPermission('Customers') || hasPermission('Customer Addresses') || hasPermission('Coupons') || hasPermission('Carts') || hasPermission('Reviews')): ?>
        <li class="mt-3 mb-1 px-2">
            <span style="font-size:.7rem;font-weight:700;color:#475569">Commerce</span>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Orders')): ?>
        <li>
            <a href="<?= url('orders') ?>" class="nav-link <?= isActive(['orders', 'order-details']) ?>">
                <i class="bi bi-receipt"></i> Orders
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Payments')): ?>
        <li>
            <a href="<?= url('payments') ?>" class="nav-link <?= isActive('payments') ?>">
                <i class="bi bi-credit-card-fill"></i> Payments
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Customers')): ?>
        <li>
            <a href="<?= url('customers') ?>" class="nav-link <?= isActive('customers') ?>">
                <i class="bi bi-people"></i> Customers
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Customer Addresses')): ?>
        <li>
            <a href="<?= url('customer-addresses') ?>" class="nav-link <?= isActive('customer-addresses') ?>">
                <i class="bi bi-geo-alt-fill"></i> Customer Address
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Coupons')): ?>
        <li>
            <a href="<?= url('coupons') ?>" class="nav-link <?= isActive('coupons') ?>">
                <i class="bi bi-ticket-perforated-fill"></i> Coupons
            </a>
        </li>
        <?php endif; ?>

        <!-- Wait, 'Carts', 'Wishlists' aren't strictly in access_pages or yes? I'll check if they are in config/app.php -->
        <?php if (hasPermission('Carts')): ?>
        <li>
            <a href="<?= url('carts') ?>" class="nav-link <?= isActive('carts') ?>">
                <i class="bi bi-cart-fill"></i> Carts
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Wishlists')): ?>
        <li>
            <a href="<?= url('wishlists') ?>" class="nav-link <?= isActive('wishlists') ?>">
                <i class="bi bi-heart-fill"></i> Wishlists
            </a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('Reviews')): ?>
        <li>
            <a href="<?= url('reviews') ?>" class="nav-link <?= isActive('reviews') ?>">
                <i class="bi bi-star-fill"></i> Reviews
            </a>
        </li>
        <?php endif; ?>

    </ul>

    <a href="<?= url('auth/logout') ?>" class="btn btn-danger w-100 mt-3 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-box-arrow-left"></i> Logout
    </a>
</nav>

<!-- OVERLAY -->
<div id="sidebarOverlay"></div>

<!-- MAIN -->
<div id="main">

    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <h5 class="mb-0 fw-semibold"><?= e($title ?? '') ?></h5>
        </div>

        <span class="text-muted small">
            <i class="bi bi-person-circle me-1"></i>
            <?= e(authUser()['full_name'] ?? 'Admin') ?>
        </span>
    </div>

    <?= $content ?? '' ?>

</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $.fn.dataTable.ext.errMode = 'none';
</script>

<?= $scripts ?? '' ?>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    document.getElementById('sidebarToggle').addEventListener('click', function () {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
</script>

</body>
</html>