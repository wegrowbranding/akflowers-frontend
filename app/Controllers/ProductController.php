<?php

class ProductController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res = ApiClient::get('/products/list', [
            'page'        => $page,
            'limit'       => $limit,
            'search_term' => $search,
        ], true);

        $products   = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('products.index', compact('products', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $categories = $this->getCategories();
        $this->view('products.form', ['product' => null, 'categories' => $categories, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = [
            'product_code'   => trim($_POST['product_code'] ?? ''),
            'product_name'   => trim($_POST['product_name'] ?? ''),
            'category_id'    => (int)($_POST['category_id'] ?? 0),
            'price'          => (float)($_POST['price'] ?? 0),
            'cost_price'     => (float)($_POST['cost_price'] ?? 0),
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'unit'           => trim($_POST['unit'] ?? ''),
            'description'    => trim($_POST['description'] ?? ''),
            'status'         => $_POST['status'] ?? 'active',
            'created_by'     => userId(),
        ];

        $res = ApiClient::post('/products/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Product created successfully.');
            $this->redirect('products');
        }

        setOld($data);
        $errors     = $this->extractErrors($res);
        $categories = $this->getCategories();
        $this->view('products.form', ['product' => null, 'categories' => $categories, 'isEdit' => false, 'errors' => $errors]);
    }

    public function edit(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::get('/products/list', ['limit' => 1000], true);
        $all = $res['data']['data'] ?? [];

        $product = null;
        foreach ($all as $p) {
            if ((int)$p['id'] === $id) { $product = $p; break; }
        }

        if (!$product) {
            setFlash('danger', 'Product not found.');
            $this->redirect('products');
        }

        $categories = $this->getCategories();
        $this->view('products.form', compact('product', 'categories') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = [
            'product_code'   => trim($_POST['product_code'] ?? ''),
            'product_name'   => trim($_POST['product_name'] ?? ''),
            'category_id'    => (int)($_POST['category_id'] ?? 0),
            'price'          => (float)($_POST['price'] ?? 0),
            'cost_price'     => (float)($_POST['cost_price'] ?? 0),
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'unit'           => trim($_POST['unit'] ?? ''),
            'description'    => trim($_POST['description'] ?? ''),
            'status'         => $_POST['status'] ?? 'active',
        ];

        $res = ApiClient::put("/products/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Product updated successfully.');
            $this->redirect('products');
        }

        setOld($data);
        $errors     = $this->extractErrors($res);
        $categories = $this->getCategories();
        $product    = array_merge($data, ['id' => $id]);
        $this->view('products.form', compact('product', 'categories', 'errors') + ['isEdit' => true]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/products/{$id}/delete", true);

        if (!empty($res['success'])) {
            setFlash('success', 'Product deleted.');
        } else {
            setFlash('danger', $res['message'] ?? 'Delete failed.');
        }
        $this->redirect('products');
    }

    private function getCategories(): array {
        $res = ApiClient::get('/categories/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
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
