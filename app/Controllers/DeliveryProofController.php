<?php

class DeliveryProofController extends Controller {

    public function index(): void {
        requireAuth();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $orderId = (int)($_GET['order_id'] ?? 0);
        $proofs  = [];
        $total   = 0;
        $totalPages = 0;

        if ($orderId) {
            $res    = ApiClient::get('/delivery-proofs/list', ['page' => $page, 'limit' => $limit, 'order_id' => $orderId], true);
            $proofs = $res['data']['data']  ?? [];
            $total  = $res['data']['total'] ?? 0;
            $totalPages = (int)ceil($total / $limit);
        }

        // Always fetch orders for the dropdown
        $ordersRes = ApiClient::get('/orders/list', ['limit' => 1000], true);
        $orders = $ordersRes['data']['data'] ?? [];

        $this->view('delivery-proofs.index', compact('proofs', 'total', 'page', 'totalPages', 'limit', 'orderId', 'orders'));
    }
}
