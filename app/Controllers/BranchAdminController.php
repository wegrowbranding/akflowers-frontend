<?php

class BranchAdminController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/branch-admins/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $admins     = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('branch-admins.index', compact('admins', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $branches = $this->getBranches();
        $this->view('branch-admins.form', ['admin' => null, 'branches' => $branches, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $data['created_by'] = userId();

        $res = ApiClient::post('/branch-admins/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch admin created successfully.');
            $this->redirect('branch-admins');
        }

        setOld($data);
        $branches = $this->getBranches();
        $this->view('branch-admins.form', ['admin' => null, 'branches' => $branches, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id    = (int)($_GET['id'] ?? 0);
        $admin = $this->findById($id);
        if (!$admin) { setFlash('danger', 'Branch admin not found.'); $this->redirect('branch-admins'); }
        $branches = $this->getBranches();
        $this->view('branch-admins.form', compact('admin', 'branches') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData(false);

        $res = ApiClient::put("/branch-admins/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch admin updated successfully.');
            $this->redirect('branch-admins');
        }

        setOld($data);
        $admin    = array_merge($data, ['id' => $id]);
        $branches = $this->getBranches();
        $this->view('branch-admins.form', compact('admin', 'branches') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/branch-admins/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('branch-admins');
    }

    private function formData(bool $requirePassword = true): array {
        $data = [
            'branch_id' => (int)($_POST['branch_id'] ?? 0),
            'username'  => trim($_POST['username'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'phone'     => trim($_POST['phone'] ?? ''),
            'status'    => $_POST['status'] ?? 'active',
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

    private function findById(int $id): ?array {
        $res = ApiClient::get('/branch-admins/list', ['limit' => 1000], true);
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
