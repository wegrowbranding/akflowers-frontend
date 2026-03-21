<?php
$title  = $isEdit ? 'Edit Wishlist' : 'Add Wishlist';
$action = $isEdit ? url('wishlists/'.($wishlist['id']??'').'/edit') : url('wishlists/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('wishlists') ?>">Wishlists</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:480px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="mb-3">
                <label class="form-label fw-medium">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select" required>
                    <option value="">— Select Customer —</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (string)($wishlist['customer_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['full_name']) ?> (#<?= $c['id'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Wishlist Items</label>
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light"><tr><th>Product</th><th style="width:50px;"></th></tr></thead>
                    <tbody id="itemsBody">
                    <?php 
                    $items = $_SESSION['old']['products'] ?? $wishlist['items'] ?? [];
                    if (!empty($items)):
                        foreach ($items as $idx => $item): ?>
                        <tr>
                            <td>
                                <select name="products[<?= $idx ?>][product_id]" class="form-select form-select-sm" required>
                                    <option value="">— Select Product —</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= (string)($item['product_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= e($p['product_name'] ?? 'Product') ?> (₹<?= $p['price'] ?? 0 ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addItem"><i class="bi bi-plus"></i> Add Item</button>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Wishlist</button>
                <a href="<?= url('wishlists') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php clearOld(); $content = ob_get_clean(); 
$optionsHtml = '<option value="">— Select Product —</option>';
foreach ($products as $p) {
    $optionsHtml .= '<option value="' . htmlspecialchars($p['id']) . '">' . addslashes(htmlspecialchars($p['product_name'] ?? 'Product')) . ' (₹' . htmlspecialchars($p['price'] ?? 0) . ')</option>';
}
$scripts = '
<script type="text/template" id="rowTemplate">
<tr>
    <td>
        <select name="products[{idx}][product_id]" class="form-select form-select-sm" required>
            ' . $optionsHtml . '
        </select>
    </td>
    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
</tr>
</script>
<script>
$(document).ready(function() {
    let itemIdx = ' . count($_SESSION["old"]["products"] ?? $wishlist["items"] ?? []) . ';
    $("#addItem").click(function() {
        let tpl = $("#rowTemplate").html().replace(/{idx}/g, itemIdx++);
        $("#itemsBody").append(tpl);
    });
    $(document).on("click", ".remove-item", function() {
        $(this).closest("tr").remove();
    });
});
</script>
';
require APP . "/Views/layouts/app.php"; ?>
