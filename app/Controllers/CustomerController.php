<?php

class CustomerController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/customers/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $customers  = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('customers.index', compact('customers', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $this->view('customers.form', ['customer' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/customers/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Customer created successfully.');
            $this->redirect('customers');
        }

        setOld($data);
        $this->view('customers.form', ['customer' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id       = (int)($_GET['id'] ?? 0);
        $customer = $this->findById($id);
        if (!$customer) { setFlash('danger', 'Customer not found.'); $this->redirect('customers'); }
        $this->view('customers.form', compact('customer') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData(false);

        $res = ApiClient::put("/customers/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Customer updated successfully.');
            $this->redirect('customers');
        }

        setOld($data);
        $customer = array_merge($data, ['id' => $id]);
        $this->view('customers.form', compact('customer') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/customers/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('customers');
    }

    private function formData(bool $withPassword = true): array {
        $data = [
            'customer_code' => trim($_POST['customer_code'] ?? '') ?: null,
            'full_name'     => trim($_POST['full_name'] ?? ''),
            'email'         => trim($_POST['email'] ?? '') ?: null,
            'phone'         => trim($_POST['phone'] ?? '') ?: null,
            'gender'        => $_POST['gender'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'status'        => $_POST['status'] ?? 'active',
        ];
        $pw = trim($_POST['password'] ?? '');
        if ($withPassword || $pw !== '') {
            $data['password_hash'] = $pw;
        }
        return $data;
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/customers/list', ['limit' => 1000], true);
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
