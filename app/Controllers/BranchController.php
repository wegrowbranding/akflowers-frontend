<?php

class BranchController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/branches/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $branches   = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('branches.index', compact('branches', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $this->view('branches.form', ['branch' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $data['created_by'] = userId();

        $res = ApiClient::post('/branches/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch created successfully.');
            $this->redirect('branches');
        }

        setOld($data);
        $this->view('branches.form', ['branch' => null, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id     = (int)($_GET['id'] ?? 0);
        $branch = $this->findById($id);
        if (!$branch) { setFlash('danger', 'Branch not found.'); $this->redirect('branches'); }
        $this->view('branches.form', compact('branch') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/branches/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch updated successfully.');
            $this->redirect('branches');
        }

        setOld($data);
        $branch = array_merge($data, ['id' => $id]);
        $this->view('branches.form', compact('branch') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/branches/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('branches');
    }

    private function formData(): array {
        return [
            'branch_code'      => trim($_POST['branch_code'] ?? ''),
            'branch_name'      => trim($_POST['branch_name'] ?? ''),
            'address_line1'    => trim($_POST['address_line1'] ?? ''),
            'address_line2'    => trim($_POST['address_line2'] ?? ''),
            'city'             => trim($_POST['city'] ?? ''),
            'state'            => trim($_POST['state'] ?? ''),
            'pincode'          => trim($_POST['pincode'] ?? ''),
            'country'          => trim($_POST['country'] ?? ''),
            'phone_primary'    => trim($_POST['phone_primary'] ?? ''),
            'phone_secondary'  => trim($_POST['phone_secondary'] ?? ''),
            'email_primary'    => trim($_POST['email_primary'] ?? ''),
            'email_secondary'  => trim($_POST['email_secondary'] ?? ''),
            'gst_number'       => trim($_POST['gst_number'] ?? ''),
            'license_number'   => trim($_POST['license_number'] ?? ''),
            'opening_date'     => $_POST['opening_date'] ?: null,
            'timezone'         => trim($_POST['timezone'] ?? ''),
            'currency'         => trim($_POST['currency'] ?? ''),
            'status'           => $_POST['status'] ?? 'active',
        ];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/branches/list', ['limit' => 1000], true);
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
