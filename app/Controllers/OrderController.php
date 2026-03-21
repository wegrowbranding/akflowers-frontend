<?php

class OrderController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/orders/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $orders     = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('orders.index', compact('orders', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        [$customers, $branches, $addresses] = $this->getRelated();
        $products = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('orders.form', compact('customers', 'branches', 'addresses', 'products') + ['order' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/orders/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Order created successfully.');
            $this->redirect('orders');
        }

        setOld($data);
        [$customers, $branches, $addresses] = $this->getRelated();
        $products = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('orders.form', compact('customers', 'branches', 'addresses', 'products') + ['order' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id    = (int)($_GET['id'] ?? 0);
        $order = $this->findById($id);
        if (!$order) { setFlash('danger', 'Order not found.'); $this->redirect('orders'); }

        // Fetch payments for this order
        $payRes   = ApiClient::get('/payments/list', ['limit' => 1000, 'search_term' => ''], true);
        $payments = array_filter($payRes['data']['data'] ?? [], fn($p) => (int)$p['order_id'] === $id);

        [$customers, $branches, $addresses] = $this->getRelated();
        $products = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('orders.form', compact('order', 'customers', 'branches', 'addresses', 'payments', 'products') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/orders/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Order updated successfully.');
            $this->redirect('orders');
        }

        setOld($data);
        $order = array_merge($data, ['id' => $id]);
        [$customers, $branches, $addresses] = $this->getRelated();
        $products = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $payments = [];
        $this->view('orders.form', compact('order', 'customers', 'branches', 'addresses', 'payments', 'products') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/orders/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('orders');
    }

    private function formData(): array {
        return [
            'order_number'    => trim($_POST['order_number'] ?? '') ?: null,
            'customer_id'     => (int)($_POST['customer_id'] ?? 0),
            'branch_id'       => (int)($_POST['branch_id'] ?? 0) ?: null,
            'total_amount'    => (float)($_POST['total_amount'] ?? 0),
            'discount_amount' => (float)($_POST['discount_amount'] ?? 0) ?: null,
            'final_amount'    => (float)($_POST['final_amount'] ?? 0),
            'payment_status'  => $_POST['payment_status'] ?? 'pending',
            'order_status'    => $_POST['order_status'] ?? 'pending',
            'payment_method'  => trim($_POST['payment_method'] ?? '') ?: null,
            'address_id'      => (int)($_POST['address_id'] ?? 0) ?: null,
            'products'        => $_POST['products'] ?? [],
        ];
    }

    private function getRelated(): array {
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $branches  = ApiClient::get('/branches/list',  ['limit' => 1000], true)['data']['data'] ?? [];
        $addresses = ApiClient::get('/customer-addresses/list', ['limit' => 1000], true)['data']['data'] ?? [];
        return [$customers, $branches, $addresses];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/orders/list', ['limit' => 1000], true);
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
