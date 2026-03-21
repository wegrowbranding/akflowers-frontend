<?php

class CouponController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/coupons/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $coupons    = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('coupons.index', compact('coupons', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $this->view('coupons.form', ['coupon' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/coupons/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Coupon created successfully.');
            $this->redirect('coupons');
        }

        setOld($data);
        $this->view('coupons.form', ['coupon' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id     = (int)($_GET['id'] ?? 0);
        $coupon = $this->findById($id);
        if (!$coupon) { setFlash('danger', 'Coupon not found.'); $this->redirect('coupons'); }
        $this->view('coupons.form', compact('coupon') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/coupons/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Coupon updated successfully.');
            $this->redirect('coupons');
        }

        setOld($data);
        $coupon = array_merge($data, ['id' => $id]);
        $this->view('coupons.form', compact('coupon') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/coupons/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('coupons');
    }

    private function formData(): array {
        return [
            'code'             => strtoupper(trim($_POST['code'] ?? '')),
            'discount_type'    => $_POST['discount_type'] ?? 'percentage',
            'discount_value'   => (float)($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0) ?: null,
            'valid_from'       => $_POST['valid_from'] ?: null,
            'valid_to'         => $_POST['valid_to'] ?: null,
            'usage_limit'      => (int)($_POST['usage_limit'] ?? 0) ?: null,
            'status'           => $_POST['status'] ?? 'active',
        ];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/coupons/list', ['limit' => 1000], true);
        foreach ($res['data']['data'] ?? [] as $item) {
            if ((int)$item['id'] === $id) return $item;
        }
        return null;
    }

    private function extractErrors(array $res): array {
        if (!empty($res['data']) && is_array($res['data'])) {
            $msgs = [];
            foreach ($res['data'] as $errs) {
                foreach ((array)$errs as $e) { $msgs[] = $e; }
            }
            return $msgs;
        }
        return [$res['message'] ?? 'Something went wrong.'];
    }
}
