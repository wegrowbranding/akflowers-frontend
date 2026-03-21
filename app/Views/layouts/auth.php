<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); min-height: 100vh; }
        .auth-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .auth-brand { color: #1e293b; font-weight: 800; font-size: 1.4rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div style="width:100%;max-width:440px;padding:1rem">
        <div class="text-center mb-4">
            <span class="auth-brand text-white">
                <i class="bi bi-shop-window me-2"></i><?= APP_NAME ?>
            </span>
        </div>
        <div class="card auth-card p-4">
            <?= $content ?? '' ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
