<?php

class BranchRoleController extends Controller {

    public function index(): void {
        requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $search = trim($_GET['search'] ?? '');

        $res        = ApiClient::get('/branch-roles/list', ['page' => $page, 'limit' => $limit, 'search_term' => $search], true);
        $roles      = $res['data']['data']  ?? [];
        $total      = $res['data']['total'] ?? 0;
        $totalPages = (int)ceil($total / $limit);

        $this->view('branch-roles.index', compact('roles', 'total', 'page', 'totalPages', 'search', 'limit'));
    }

    public function create(): void {
        requireAuth();
        $branches = $this->getBranches();
        $config = require ROOT . '/config/app.php';
        $accessPages = $config['access_pages'] ?? [];
        $this->view('branch-roles.form', ['role' => null, 'branches' => $branches, 'accessPages' => $accessPages, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = $this->formData();
        $data['created_by'] = userId();

        $res = ApiClient::post('/branch-roles/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch role created successfully.');
            $this->redirect('branch-roles');
        }

        setOld($data);
        $branches = $this->getBranches();
        $config = require ROOT . '/config/app.php';
        $accessPages = $config['access_pages'] ?? [];
        $this->view('branch-roles.form', ['role' => null, 'branches' => $branches, 'accessPages' => $accessPages, 'isEdit' => false, 'errors' => $this->extractErrors($res)]);
    }

    public function edit(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $role = $this->findById($id);
        if (!$role) { setFlash('danger', 'Branch role not found.'); $this->redirect('branch-roles'); }
        $branches = $this->getBranches();
        $config = require ROOT . '/config/app.php';
        $accessPages = $config['access_pages'] ?? [];
        $this->view('branch-roles.form', compact('role', 'branches', 'accessPages') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->formData();

        $res = ApiClient::put("/branch-roles/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Branch role updated successfully.');
            $this->redirect('branch-roles');
        }

        setOld($data);
        $role     = array_merge($data, ['id' => $id]);
        $branches = $this->getBranches();
        $config = require ROOT . '/config/app.php';
        $accessPages = $config['access_pages'] ?? [];
        $this->view('branch-roles.form', compact('role', 'branches', 'accessPages') + ['isEdit' => true, 'errors' => $this->extractErrors($res)]);
    }

    public function destroy(): void {
        requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/branch-roles/{$id}/delete", true);
        setFlash(!empty($res['success']) ? 'success' : 'danger', $res['message'] ?? 'Operation failed.');
        $this->redirect('branch-roles');
    }

    private function formData(): array {
        $data = [
            'branch_id'        => (int)($_POST['branch_id'] ?? 0),
            'role_name'        => trim($_POST['role_name'] ?? ''),
            'role_description' => trim($_POST['role_description'] ?? ''),
            'is_default'       => isset($_POST['is_default']) ? 1 : 0,
            'status'           => $_POST['status'] ?? 'active',
        ];

        $accessPages = $_POST['access_pages'] ?? [];
        $module = is_array($accessPages) ? implode(', ', $accessPages) : '';

        $data['permission'] = [
            'module' => $module,
            'action' => 'Read, Write',
            'category' => 'Admin',
            'display_name' => $data['role_name'] . ' Permissions',
            'status' => 'active'
        ];

        return $data;
    }

    private function getBranches(): array {
        $res = ApiClient::get('/branches/list', ['limit' => 1000], true);
        return $res['data']['data'] ?? [];
    }

    private function findById(int $id): ?array {
        $res = ApiClient::get('/branch-roles/list', ['limit' => 1000], true);
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
