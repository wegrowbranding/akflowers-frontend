<?php
$title  = $isEdit ? 'Edit Order' : 'Add Order';
$action = $isEdit ? url('orders/'.($order['id']??'').'/edit') : url('orders/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('orders') ?>">Orders</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
    <?php if ($isEdit): ?>
        <a href="<?= url('payments/create') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Payment</a>
    <?php endif; ?>
</div>

<?php if ($isEdit): ?>
<!-- Tabs -->
<ul class="nav nav-tabs mb-0" id="orderTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOrder">Order Details</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPayments">
        Payments <span class="badge bg-secondary ms-1"><?= count($payments ?? []) ?></span>
    </button></li>

</ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="tabOrder">
<?php endif; ?>

<div class="card <?= $isEdit ? 'border-top-0 rounded-top-0' : '' ?>" style="max-width:860px">
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Order Number</label>
                    <input type="text" name="order_number" class="form-control" value="<?= old('order_number', $order['order_number'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (string)($order['customer_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['full_name']) ?> (#<?= $c['id'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">— None —</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (string)($order['branch_id'] ?? '') === (string)$b['id'] ? 'selected' : '' ?>><?= e($b['branch_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Delivery Address</label>
                    <select name="address_id" class="form-select">
                        <option value="">— None —</option>
                        <?php foreach ($addresses as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= (string)($order['address_id'] ?? '') === (string)$a['id'] ? 'selected' : '' ?>><?= e($a['name'] ?? 'Address') ?> — <?= e($a['city'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" placeholder="cash, card, upi…" value="<?= old('payment_method', $order['payment_method'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <?php foreach (['pending','paid','failed','refunded'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($order['payment_status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Order Status</label>
                    <select name="order_status" class="form-select">
                        <?php foreach (['pending','confirmed','packed','shipped','delivered','cancelled','returned'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($order['order_status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Total Amount <span class="text-danger">*</span></label>
                    <div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" name="total_amount" class="form-control bg-light" step="0.01" min="0" required value="<?= old('total_amount', (string)($order['total_amount'] ?? '0')) ?>" readonly tabindex="-1">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Discount Amount</label>
                    <div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" name="discount_amount" class="form-control" step="0.01" min="0" value="<?= old('discount_amount', (string)($order['discount_amount'] ?? '0')) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Final Amount <span class="text-danger">*</span></label>
                    <div class="input-group"><span class="input-group-text">₹</span>
                        <input type="number" name="final_amount" class="form-control bg-light" step="0.01" min="0" required value="<?= old('final_amount', (string)($order['final_amount'] ?? '0')) ?>" readonly tabindex="-1">
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            <h6 class="fw-semibold mb-3">Order Items</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light"><tr><th>Product</th><th style="width:100px;">Qty</th><th style="width:120px;">Price</th><th style="width:120px;">Total</th><th style="width:50px;"></th></tr></thead>
                    <tbody id="itemsBody">
                    <?php 
                    $items = $_SESSION['old']['products'] ?? $order['items'] ?? [];
                    if (!empty($items)):
                        foreach ($items as $idx => $item): ?>
                        <tr>
                            <td>
                                <select name="products[<?= $idx ?>][product_id]" class="form-select form-select-sm p_select" required>
                                    <option value="">— Select Product —</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?? 0 ?>" <?= (string)($item['product_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= e($p['product_name'] ?? 'Product') ?> (₹<?= $p['price'] ?? 0 ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="products[<?= $idx ?>][quantity]" class="form-control form-control-sm p_qty" min="1" value="<?= $item['quantity'] ?? 1 ?>" required></td>
                            <td><input type="number" name="products[<?= $idx ?>][price]" class="form-control form-control-sm p_price" step="0.01" value="<?= $item['price'] ?? 0 ?>" required></td>
                            <td><input type="number" name="products[<?= $idx ?>][total_price]" class="form-control form-control-sm p_total" step="0.01" value="<?= $item['total_price'] ?? 0 ?>" required readonly></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addItem"><i class="bi bi-plus"></i> Add Item</button>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Order</button>
                <a href="<?= url('orders') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php if ($isEdit): ?>
</div><!-- #tabOrder -->

<div class="tab-pane fade" id="tabPayments">
<div class="card border-top-0 rounded-top-0" style="max-width:860px">
    <div class="card-body">
        <?php if (empty($payments)): ?>
            <p class="text-muted text-center py-3">No payments recorded for this order.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Transaction ID</th><th>Gateway</th><th>Amount</th><th>Status</th><th>Paid At</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                <?php
                $psc = ['pending'=>'warning','success'=>'success','failed'=>'danger'];
                foreach ($payments as $p): ?>
                    <tr>
                        <td class="text-muted small"><?= $p['id'] ?></td>
                        <td><code><?= e($p['transaction_id'] ?? '—') ?></code></td>
                        <td><?= e($p['payment_gateway'] ?? '—') ?></td>
                        <td class="fw-semibold"><?= number_format((float)($p['amount'] ?? 0), 2) ?></td>
                        <td><span class="badge bg-<?= $psc[$p['status'] ?? 'pending'] ?? 'secondary' ?>"><?= ucfirst($p['status'] ?? 'pending') ?></span></td>
                        <td class="text-muted small"><?= $p['paid_at'] ? date('d M Y H:i', strtotime($p['paid_at'])) : '—' ?></td>
                        <td class="text-end">
                            <a href="<?= url('payments/'.$p['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('payments/'.$p['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</div><!-- #tabPayments -->



</div><!-- .tab-content -->
<?php endif; ?>

<?php clearOld(); $content = ob_get_clean(); 
$optionsHtml = '<option value="">— Select Product —</option>';
foreach ($products as $p) {
    $optionsHtml .= '<option value="' . htmlspecialchars($p['id']) . '" data-price="' . htmlspecialchars($p['price'] ?? 0) . '">' . addslashes(htmlspecialchars($p['product_name'] ?? 'Product')) . ' (₹' . htmlspecialchars($p['price'] ?? 0) . ')</option>';
}
$scripts = '
<script type="text/template" id="rowTemplate">
<tr>
    <td>
        <select name="products[{idx}][product_id]" class="form-select form-select-sm p_select" required>
            ' . $optionsHtml . '
        </select>
    </td>
    <td><input type="number" name="products[{idx}][quantity]" class="form-control form-control-sm p_qty" min="1" value="1" required></td>
    <td><input type="number" name="products[{idx}][price]" class="form-control form-control-sm p_price" step="0.01" value="0" required></td>
    <td><input type="number" name="products[{idx}][total_price]" class="form-control form-control-sm p_total" step="0.01" value="0" required readonly></td>
    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
</tr>
</script>
<script>
$(document).ready(function() {
    let itemIdx = ' . count($_SESSION["old"]["products"] ?? $order["items"] ?? []) . ';
    $("#addItem").click(function() {
        let tpl = $("#rowTemplate").html().replace(/{idx}/g, itemIdx++);
        $("#itemsBody").append(tpl);
    });
    $(document).on("click", ".remove-item", function() {
        $(this).closest("tr").remove();
        calcTotal();
    });
    $(document).on("change", ".p_select", function() {
        let price = $(this).find("option:selected").data("price") || 0;
        let tr = $(this).closest("tr");
        tr.find(".p_price").val(price);
        calcRow(tr);
    });
    $(document).on("input", ".p_qty, .p_price", function() {
        calcRow($(this).closest("tr"));
    });
    function calcRow(tr) {
        let qty = parseFloat(tr.find(".p_qty").val()) || 0;
        let price = parseFloat(tr.find(".p_price").val()) || 0;
        tr.find(".p_total").val((qty * price).toFixed(2));
        calcTotal();
    }
    function calcTotal() {
        let total = 0;
        $(".p_total").each(function() { total += parseFloat($(this).val()) || 0; });
        $("input[name=total_amount]").val(total.toFixed(2));
        let discount = parseFloat($("input[name=discount_amount]").val()) || 0;
        $("input[name=final_amount]").val((total - discount).toFixed(2));
    }
    $("input[name=discount_amount]").on("input", calcTotal);
});
</script>
';
require APP . "/Views/layouts/app.php"; ?>
