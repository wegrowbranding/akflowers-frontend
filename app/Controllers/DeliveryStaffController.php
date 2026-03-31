<?php

class DeliveryStaffController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/delivery-staff/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $staff      = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('delivery-staff.index', compact('staff', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $relatedStaff = ApiClient::get('/branch-staff-users/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-staff.form', compact('relatedStaff') + ['deliveryStaff' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $res = ApiClient::post('/delivery-staff/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Delivery staff added successfully.');
            $this->redirect('delivery-staff');
        }

        setOld($data);
        $relatedStaff = ApiClient::get('/branch-staff-users/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-staff.form', compact('relatedStaff') + ['deliveryStaff' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $deliveryStaff = $this->findById($id);
        if (!$deliveryStaff) { setFlash('danger', 'Delivery staff not found.'); $this->redirect('delivery-staff'); }

        $relatedStaff = ApiClient::get('/branch-staff-users/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-staff.form', compact('deliveryStaff', 'relatedStaff') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $data = $this->formData();
        $res = ApiClient::put("/delivery-staff/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Delivery staff updated successfully.');
            $this->redirect('delivery-staff');
        }

        setOld($data);
        $deliveryStaff = array_merge($data, ['id' => $id]);
        $relatedStaff = ApiClient::get('/branch-staff-users/list', ['limit' => 1000], true)['data']['data'] ?? [];
        $this->view('delivery-staff.form', compact('deliveryStaff', 'relatedStaff') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/delivery-staff/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('delivery-staff');
    }

    private function formData(): array {
        return [
            'staff_id'       => (int)($_POST['staff_id'] ?? 0),
            'vehicle_type'   => $_POST['vehicle_type'] ?? 'bike',
            'vehicle_number' => trim($_POST['vehicle_number'] ?? ''),
            'is_available'   => isset($_POST['is_available']) ? 1 : 0,
        ];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/delivery-staff/list', ['limit' => 1000], true);
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
