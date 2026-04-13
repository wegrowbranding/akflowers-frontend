<?php

class DeliveryAssignmentController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $orderId = (int)($_GET['order_id'] ?? 0);

        $res         = ApiClient::get('/delivery-assignments/list', ['page' => $page, 'limit' => $limit, 'order_id' => $orderId ?: ''], true);
        $assignments = $res['data']['data']  ?? [];
        $total       = $res['data']['total'] ?? 0;
        $totalPages  = (int)ceil($total / $limit);

        // Fetch orders for filter
        $orders = ApiClient::get('/orders/list', ['limit' => 1000], true)['data']['data'] ?? [];

        $this->view('delivery-assignments.index', compact('assignments', 'total', 'page', 'totalPages', 'limit', 'orderId', 'orders'));
    }

    public function create(): void {
        requireAuth();
        $orders = ApiClient::get('/orders/list', ['limit' => 1000, 'unassigned_only' => 1], true)['data']['data'] ?? [];
        $staff = ApiClient::get('/delivery-staff/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-assignments.form', compact('orders', 'staff') + ['assignment' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $res = ApiClient::post('/delivery-assignments/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Delivery assignment added successfully.');
            $this->redirect('delivery-assignments');
        }

        setOld($data);
        $orders = ApiClient::get('/orders/list', ['limit' => 1000, 'unassigned_only' => 1], true)['data']['data'] ?? [];
        $staff = ApiClient::get('/delivery-staff/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-assignments.form', compact('orders', 'staff') + ['assignment' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $assignment = $this->findById($id);
        if (!$assignment) { setFlash('danger', 'Delivery assignment not found.'); $this->redirect('delivery-assignments'); }

        $orders = ApiClient::get('/orders/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $staff = ApiClient::get('/delivery-staff/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-assignments.form', compact('assignment', 'orders', 'staff') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $data = $this->formData();
        $res = ApiClient::put("/delivery-assignments/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Delivery assignment updated successfully.');
            $this->redirect('delivery-assignments');
        }

        setOld($data);
        $assignment = array_merge($data, ['id' => $id]);
        $orders = ApiClient::get('/orders/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $staff = ApiClient::get('/delivery-staff/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-assignments.form', compact('assignment', 'orders', 'staff') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/delivery-assignments/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('delivery-assignments');
    }

    private function formData(): array {
        return [
            'order_id'          => (int)($_POST['order_id'] ?? 0),
            'delivery_staff_id' => (int)($_POST['delivery_staff_id'] ?? 0),
            'status'            => $_POST['status'] ?? 'assigned',
            'assigned_at'       => $_POST['assigned_at'] ?: date('Y-m-d H:i:s'),
        ];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/delivery-assignments/list', ['limit' => 1000], true);
        foreach ($res['data']['data'] ?? [] as $item) {
            if ((int)$item['id'] === $id) return $item;
        }
        return null;
    }

    private function extractErrors(array $res): array {
        if (!empty($res['errors']) && is_array($res['errors'])) {
            $msgs = [];
            foreach ($res['errors'] as $errs) {
                foreach ((array)$errs as $e) { $msgs[] = $e; }
            }
            return $msgs;
        }
        return [$res['message'] ?? 'Something went wrong.'];
    }
}
