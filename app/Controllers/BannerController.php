<?php

class BannerController extends Controller {

    public function index(): void {
        requireAuth();
        $res = ApiClient::get('/banners/list', [], true);
        $banners = $res['data'] ?? [];

        $this->view('banners.index', compact('banners'));
    }

    public function create(): void {
        requireAuth();
        $this->view('banners.form', ['banner' => null, 'isEdit' => false]);
    }

    public function store(): void {
        requireAuth();
        $data = [
            'image'  => (int)($_POST['image'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
        ];

        $res = ApiClient::post('/banners/add', $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Banner created successfully.');
            $this->redirect('banners');
        }

        setOld($data);
        $errors = $this->extractErrors($res);
        $this->view('banners.form', ['banner' => null, 'isEdit' => false, 'errors' => $errors]);
    }

    public function edit(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $res = ApiClient::get('/banners/list', [], true);
        $all = $res['data'] ?? [];

        $banner = null;
        foreach ($all as $b) {
            if ((int)$b['id'] === $id) { $banner = $b; break; }
        }

        if (!$banner) {
            setFlash('danger', 'Banner not found.');
            $this->redirect('banners');
        }

        $this->view('banners.form', compact('banner') + ['isEdit' => true]);
    }

    public function update(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $data = [
            'image'  => (int)($_POST['image'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
        ];

        $res = ApiClient::put("/banners/{$id}/edit", $data, true);

        if (!empty($res['success'])) {
            setFlash('success', 'Banner updated successfully.');
            $this->redirect('banners');
        }

        setOld($data);
        $errors = $this->extractErrors($res);
        $banner = array_merge($data, ['id' => $id]);
        $this->view('banners.form', compact('banner', 'errors') + ['isEdit' => true]);
    }

    public function destroy(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $res = ApiClient::delete("/banners/{$id}/delete", true);

        if (!empty($res['success'])) {
            setFlash('success', 'Banner deleted.');
        } else {
            setFlash('danger', $res['message'] ?? 'Delete failed.');
        }
        $this->redirect('banners');
    }

    private function extractErrors(array $res): array {
        if (!empty($res['data']) && is_array($res['data'])) {
            $msgs = [];
            foreach ($res['data'] as $errs) {
                if (is_array($errs)) {
                    foreach ($errs as $e) { $msgs[] = $e; }
                } else {
                    $msgs[] = $errs;
                }
            }
            return $msgs;
        }
        return [$res['message'] ?? 'Something went wrong.'];
    }
}
