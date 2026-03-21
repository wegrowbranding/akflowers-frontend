<?php

class CategoryController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res = ApiClient::get('/categories/list', [
            'page'        => $page,
            'limit'       => $limit,
            'search_term' => $search,
        ], true);

        $categories = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('categories.index', compact('categories', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $parentCategories = $this->getAllCategories();
        $this->view('categories.form', ['category' => null, 'parentCategories' => $parentCategories, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = [
            'category_name'      => trim($_POST['category_name'] ?? ''),
            'parent_category_id' => $_POST['parent_category_id'] ?: null,
            'description'        => trim($_POST['description'] ?? ''),
            'display_order'      => (int)($_POST['display_order'] ?? 0),
            'show_in_menu'       => isset($_POST['show_in_menu']) ? 1 : 0,
            'status'             => $_POST['status'] ?? 'active',
            'created_by'         => userId(),
        ];

        $res = ApiClient::post('/categories/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Category created successfully.');
            $this->redirect('categories');
        }

        setOld($data);
        $errors           = $this->extractErrors($res);
        $parentCategories = $this->getAllCategories();
        $this->view('categories.form', ['category' => null, 'parentCategories' => $parentCategories, 'isEdit' => false, 'errors' => $errors]);
    }

    public function edit(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::get('/categories/list', ['limit' => 1000], true);
        $all = $res['data']['data'] ?? [];

        $category = null;
        foreach ($all as $cat) {
            if ((int)$cat['id'] === $id) { $category = $cat; break; }
        }

        if (!$category) {
            setFlash('danger', 'Category not found.');
            $this->redirect('categories');
        }

        $parentCategories = array_filter($all, fn($c) => (int)$c['id'] !== $id);
        $this->view('categories.form', compact('category', 'parentCategories') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = [
            'category_name'      => trim($_POST['category_name'] ?? ''),
            'parent_category_id' => $_POST['parent_category_id'] ?: null,
            'description'        => trim($_POST['description'] ?? ''),
            'display_order'      => (int)($_POST['display_order'] ?? 0),
            'show_in_menu'       => isset($_POST['show_in_menu']) ? 1 : 0,
            'status'             => $_POST['status'] ?? 'active',
        ];

        $res = ApiClient::put("/categories/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Category updated successfully.');
            $this->redirect('categories');
        }

        setOld($data);
        $errors           = $this->extractErrors($res);
        $parentCategories = $this->getAllCategories();
        $category         = array_merge($data, ['id' => $id]);
        $this->view('categories.form', compact('category', 'parentCategories', 'errors') + ['isEdit' => true]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/categories/{$id}/delete", true);

        if (!empty($res['success'])) {
            setFlash('success', 'Category deleted.');
        } else {
            setFlash('danger', $res['message'] ?? 'Delete failed.');
        }
        $this->redirect('categories');
    }

    private function getAllCategories(): array {
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
