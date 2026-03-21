<?php

class ReviewController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;

        $res        = ApiClient::get('/reviews/list', ['page' => $page, 'limit' => $limit], true);
        $reviews    = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('reviews.index', compact('reviews', 'total', 'page', 'totalPages', 'limit'));
    }

    public function create(): void {
        requireAuth();
        [$products, $customers] = $this->getRelated();
        $this->view('reviews.form', ['review' => null, 'products' => $products, 'customers' => $customers, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/reviews/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Review created successfully.');
            $this->redirect('reviews');
        }

        setOld($data);
        [$products, $customers] = $this->getRelated();
        $this->view('reviews.form', ['review' => null, 'products' => $products, 'customers' => $customers, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id     = (int)($_GET['id'] ?? 0);
        $review = $this->findById($id);
        if (!$review) { setFlash('danger', 'Review not found.'); $this->redirect('reviews'); }
        [$products, $customers] = $this->getRelated();
        $this->view('reviews.form', compact('review', 'products', 'customers') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/reviews/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Review updated successfully.');
            $this->redirect('reviews');
        }

        setOld($data);
        $review = array_merge($data, ['id' => $id]);
        [$products, $customers] = $this->getRelated();
        $this->view('reviews.form', compact('review', 'products', 'customers') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/reviews/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('reviews');
    }

    private function formData(): array {
        return [
            'product_id'  => (int)($_POST['product_id'] ?? 0),
            'customer_id' => (int)($_POST['customer_id'] ?? 0),
            'rating'      => (int)($_POST['rating'] ?? 5),
            'review'      => trim($_POST['review'] ?? '') ?: null,
        ];
    }

    private function getRelated(): array {
        $products  = ApiClient::get('/products/list',   ['limit' => 1000], true)['data']['data'] ?? [];
        $customers = ApiClient::get('/customers/list',  ['limit' => 1000], true)['data']['data'] ?? [];
        return [$products, $customers];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/reviews/list', ['limit' => 1000], true);
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
