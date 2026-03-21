<?php

class WishlistController extends Controller {

    public function index(): void {
        requireAuth();
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $limit      = 10;
        $customerId = (int)($_GET['customer_id'] ?? 0);
        $wishlists  = [];
        $total      = 0;
        $totalPages = 0;

        if ($customerId) {
            $res        = ApiClient::get('/wishlists/list', ['page' => $page, 'limit' => $limit, 'customer_id' => $customerId], true);
            $wishlists  = $res['data']['data']  ?? [];
            $total      = $res['data']['total'] ?? 0;
            $totalPages = (int)ceil($total / $limit);
        }

        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('wishlists.index', compact('wishlists', 'total', 'page', 'totalPages', 'limit', 'customerId', 'customers'));
    }

    public function create(): void {
        requireAuth();
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('wishlists.form', compact('customers', 'products') + ['wishlist' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = [
            'customer_id' => (int)($_POST['customer_id'] ?? 0),
            'products'    => $_POST['products'] ?? []
        ];

        $res = ApiClient::post('/wishlists/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Wishlist created successfully.');
            $this->redirect('wishlists' . (!empty($data['customer_id']) ? '?customer_id=' . $data['customer_id'] : ''));
        }

        setOld($data);
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('wishlists.form', compact('customers', 'products') + ['wishlist' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id       = (int)($_GET['id'] ?? 0);
        $wishlist = $this->findById($id);
        if (!$wishlist) { setFlash('danger', 'Wishlist not found.'); $this->redirect('wishlists'); }
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('wishlists.form', compact('wishlist', 'customers', 'products') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = [
            'customer_id' => (int)($_POST['customer_id'] ?? 0),
            'products'    => $_POST['products'] ?? []
        ];

        $res = ApiClient::put("/wishlists/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Wishlist updated successfully.');
            $this->redirect('wishlists' . (!empty($data['customer_id']) ? '?customer_id=' . $data['customer_id'] : ''));
        }

        setOld($data);
        $wishlist  = array_merge($data, ['id' => $id]);
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $products  = ApiClient::get('/products/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('wishlists.form', compact('wishlist', 'customers', 'products') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $wl = $this->findById($id);
        $custId = $wl['customer_id'] ?? '';
        $res = ApiClient::delete("/wishlists/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('wishlists' . ($custId ? '?customer_id=' . $custId : ''));
    }

    private function findById(int $id): ?array {
        $customers = ApiClient::get('/customers/list', ['limit' => 1000], true)['data']['data'] ?? [];
        foreach ($customers as $c) {
            $res = ApiClient::get('/wishlists/list', ['limit' => 1000, 'customer_id' => $c['id']], true);
            foreach ($res['data']['data'] ?? [] as $wl) {
                if ((int)$wl['id'] === $id) return $wl;
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
