<?php

class DeliveryTrackingController extends Controller {

    public function index(): void {
        requireAuth();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $orderId = (int)($_GET['order_id'] ?? 0);
        $tracking = [];
        $total    = 0;
        $totalPages = 0;

        if ($orderId) {
            $res      = ApiClient::get('/delivery-tracking/list', ['page' => $page, 'limit' => $limit, 'order_id' => $orderId], true);
            $tracking = $res['data']['data']  ?? [];
            $total    = $res['data']['total'] ?? 0;
            $totalPages = (int)ceil($total / $limit);
        }

        // Always fetch orders for the dropdown
        $ordersRes = ApiClient::get('/orders/list', ['limit' => 1000], true);
        $orders = $ordersRes['data']['data'] ?? [];

        $this->view('delivery-tracking.index', compact('tracking', 'total', 'page', 'totalPages', 'limit', 'orderId', 'orders'));
    }

    public function show(): void {
        requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $res = ApiClient::get("/delivery-tracking/{$id}/get", [], true);
        $tracking = $res['data']['data'] ?? [];
        $this->view('delivery-tracking.show', compact('tracking', 'id'));
    }
}
