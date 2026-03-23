<?php

class BranchStaffController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/branch-staff-users/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $staff      = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('branch-staff.index', compact('staff', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $branches = $this->getBranches();
        $roles    = $this->getRoles();
        $this->view('branch-staff.form', ['staff' => null, 'branches' => $branches, 'roles' => $roles, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $data['created_by'] = userId();

        $res = ApiClient::post('/branch-staff-users/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Staff user created successfully.');
            $this->redirect('branch-staff');
        }

        setOld($data);
        $branches = $this->getBranches();
        $roles    = $this->getRoles();
        $this->view('branch-staff.form', ['staff' => null, 'branches' => $branches, 'roles' => $roles, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id    = (int)($_GET['id'] ?? 0);
        $staff = $this->findById($id);
        if (!$staff) { setFlash('danger', 'Staff user not found.'); $this->redirect('branch-staff'); }
        $branches = $this->getBranches();
        $roles    = $this->getRoles();
        $this->view('branch-staff.form', compact('staff', 'branches', 'roles') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData(false);

        $res = ApiClient::put("/branch-staff-users/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Staff user updated successfully.');
            $this->redirect('branch-staff');
        }

        setOld($data);
        $staff    = array_merge($data, ['id' => $id]);
        $branches = $this->getBranches();
        $roles    = $this->getRoles();
        $this->view('branch-staff.form', compact('staff', 'branches', 'roles') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/branch-staff-users/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('branch-staff');
    }

    private function formData(bool $requirePassword = true): array {
        $data = [
            'branch_id'       => (int)($_POST['branch_id'] ?? 0),
            'username'        => trim($_POST['username'] ?? ''),
            'email'           => trim($_POST['email'] ?? ''),
            'full_name'       => trim($_POST['full_name'] ?? ''),
            'phone'           => trim($_POST['phone'] ?? ''),
            'role_id'         => (int)($_POST['role_id'] ?? 0),
            'employee_id'     => trim($_POST['employee_id'] ?? '') ?: null,
            'date_of_joining' => $_POST['date_of_joining'] ?: null,
            'date_of_birth'   => $_POST['date_of_birth'] ?: null,
            'address'         => trim($_POST['address'] ?? '') ?: null,
            'status'          => $_POST['status'] ?? 'active',
        ];
        $password = trim($_POST['password'] ?? '');
        if ($requirePassword || $password !== '') {
            $data['password'] = $password;
        }
        return $data;
    }

    private function getBranches(): array {
        $res = ApiClient::get('/branches/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
    }

    private function getRoles(): array {
        $res = ApiClient::get('/branch-roles/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/branch-staff-users/list', ['limit' => 1000], true);
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
