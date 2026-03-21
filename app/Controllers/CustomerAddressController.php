<?php

class CustomerAddressController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res       = ApiClient::get('/customer-addresses/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $addresses = $res['data']['data']  ?? [];
        $total     = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('customer-addresses.index', compact('addresses', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $customers = $this->getCustomers();
        $this->view('customer-addresses.form', ['address' => null, 'customers' => $customers, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();

        $res = ApiClient::post('/customer-addresses/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Address created successfully.');
            $this->redirect('customer-addresses');
        }

        setOld($data);
        $customers = $this->getCustomers();
        $this->view('customer-addresses.form', ['address' => null, 'customers' => $customers, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id      = (int)($_GET['id'] ?? 0);
        $address = $this->findById($id);
        if (!$address) { setFlash('danger', 'Address not found.'); $this->redirect('customer-addresses'); }
        $customers = $this->getCustomers();
        $this->view('customer-addresses.form', compact('address', 'customers') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/customer-addresses/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Address updated successfully.');
            $this->redirect('customer-addresses');
        }

        setOld($data);
        $address   = array_merge($data, ['id' => $id]);
        $customers = $this->getCustomers();
        $this->view('customer-addresses.form', compact('address', 'customers') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/customer-addresses/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('customer-addresses');
    }

    private function formData(): array {
        return [
            'customer_id'   => (int)($_POST['customer_id'] ?? 0),
            'name'          => trim($_POST['name'] ?? '') ?: null,
            'phone'         => trim($_POST['phone'] ?? '') ?: null,
            'address_line1' => trim($_POST['address_line1'] ?? '') ?: null,
            'address_line2' => trim($_POST['address_line2'] ?? '') ?: null,
            'city'          => trim($_POST['city'] ?? '') ?: null,
            'state'         => trim($_POST['state'] ?? '') ?: null,
            'pincode'       => trim($_POST['pincode'] ?? '') ?: null,
            'country'       => trim($_POST['country'] ?? '') ?: null,
            'is_default'    => isset($_POST['is_default']) ? 1 : 0,
        ];
    }

    private function getCustomers(): array {
        $res = ApiClient::get('/customers/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/customer-addresses/list', ['limit' => 1000], true);
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
