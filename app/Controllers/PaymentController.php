<?php

class PaymentController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/payments/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $payments   = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('payments.index', compact('payments', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $orders = $this->getOrders();
        $this->view('payments.form', ['payment' => null, 'orders' => $orders, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/payments/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Payment recorded successfully.');
            $this->redirect('payments');
        }

        setOld($data);
        $orders = $this->getOrders();
        $this->view('payments.form', ['payment' => null, 'orders' => $orders, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id      = (int)($_GET['id'] ?? 0);
        $payment = $this->findById($id);
        if (!$payment) { setFlash('danger', 'Payment not found.'); $this->redirect('payments'); }
        $orders = $this->getOrders();
        $this->view('payments.form', compact('payment', 'orders') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/payments/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Payment updated successfully.');
            $this->redirect('payments');
        }

        setOld($data);
        $payment = array_merge($data, ['id' => $id]);
        $orders  = $this->getOrders();
        $this->view('payments.form', compact('payment', 'orders') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/payments/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('payments');
    }

    private function formData(): array {
        return [
            'order_id'         => (int)($_POST['order_id'] ?? 0),
            'transaction_id'   => trim($_POST['transaction_id'] ?? '') ?: null,
            'payment_gateway'  => trim($_POST['payment_gateway'] ?? '') ?: null,
            'amount'           => (float)($_POST['amount'] ?? 0),
            'status'           => $_POST['status'] ?? 'pending',
            'paid_at'          => $_POST['paid_at'] ?: null,
        ];
    }

    private function getOrders(): array {
        $res = ApiClient::get('/orders/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/payments/list', ['limit' => 1000], true);
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
