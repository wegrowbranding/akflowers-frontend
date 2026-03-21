<?php

class CartController extends Controller {

    public function index(): void {
        requireAuth();
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $limit      = 10;
        $customerId = (int)($_GET['customer_id'] ?? 0);

        $carts = $total = $totalPages = 0;
        $carts = [];

        if ($customerId) {
            $res        = ApiClient::get('/carts/list', ['page' => $page, 'limit' => $limit, 'customer_id' => $customerId], true);
            $carts      = $res['data']['data']  ?? [];
            $total      = $res['data']['total'] ?? 0;
            $totalPages = (int)ceil($total / $limit);
        }

        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('carts.index', compact('carts', 'total', 'page', 'totalPages', 'limit', 'customerId', 'customers'));
    }

    public function create(): void {
        requireAuth();
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('carts.form', compact('customers', 'products') + ['cart' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = [
            'customer_id' => (int)($_POST['customer_id'] ?? 0),
            'products'    => $_POST['products'] ?? []
        ];

        $res = ApiClient::post('/carts/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Cart created successfully.');
            $this->redirect('carts' . (!empty($data['customer_id']) ? '?customer_id=' . $data['customer_id'] : ''));
        }

        setOld($data);
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('carts.form', compact('customers', 'products') + ['cart' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $cart = $this->findById($id);
        if (!$cart) { setFlash('danger', 'Cart not found.'); $this->redirect('carts'); }
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('carts.form', compact('cart', 'customers', 'products') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = [
            'customer_id' => (int)($_POST['customer_id'] ?? 0),
            'products'    => $_POST['products'] ?? []
        ];

        $res = ApiClient::put("/carts/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Cart updated successfully.');
            $this->redirect('carts' . (!empty($data['customer_id']) ? '?customer_id=' . $data['customer_id'] : ''));
        }

        setOld($data);
        $cart      = array_merge($data, ['id' => $id]);
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('carts.form', compact('cart', 'customers', 'products') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $cart = $this->findById($id);
        $custId = $cart['customer_id'] ?? '';
        $res = ApiClient::delete("/carts/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('carts' . ($custId ? '?customer_id=' . $custId : ''));
    }

    private function findById(int $id): ?array {
        // We need customer_id to list, so try fetching all customers and their carts
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        foreach ($customers as $c) {
            $res = ApiClient::get('/carts/list', ['limit' => 1000, 'customer_id' => $c['id']], true);
            foreach ($res['data']['data'] ?? [] as $cart) {
                if ((int)$cart['id'] === $id) return $cart;
            }
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
