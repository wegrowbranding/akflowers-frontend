<?php
$title  = $isEdit ? 'Edit Review' : 'Add Review';
$action = $isEdit ? url('reviews/'.($review['id']??'').'/edit') : url('reviews/create');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('reviews') ?>">Reviews</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol></nav>
</div>
<div class="card" style="max-width:640px">
    <div class="card-header bg-white fw-semibold py-3"><?= $title ?></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-1"></i><?= e($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select" required>
                        <option value="">— Select Product —</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= (string)($review['product_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= e($p['product_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">— Select Customer —</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (string)($review['customer_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Rating <span class="text-danger">*</span></label>
                    <div class="d-flex gap-1 align-items-center mt-1" id="starRating">
                        <?php $currentRating = (int)old('rating', (string)($review['rating'] ?? 5)); ?>
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="bi bi-star<?= $s <= $currentRating ? '-fill' : '' ?> fs-4 text-warning star-btn"
                               data-val="<?= $s ?>" style="cursor:pointer"></i>
                        <?php endfor; ?>
                        <input type="hidden" name="rating" id="ratingInput" value="<?= $currentRating ?>">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Review Text</label>
                    <textarea name="review" class="form-control" rows="4"><?= old('review', $review['review'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Submit' ?> Review</button>
                <a href="<?= url('reviews') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
$scripts = <<<JS
<script>
$(function(){
    $('.star-btn').on('click', function(){
        var val = $(this).data('val');
        $('#ratingInput').val(val);
        $('.star-btn').each(function(){
            var s = $(this).data('val');
            $(this).removeClass('bi-star-fill bi-star').addClass(s <= val ? 'bi-star-fill' : 'bi-star');
        });
    });
});
</script>
JS;
clearOld(); $content = ob_get_clean(); require APP . '/Views/layouts/app.php'; ?>
