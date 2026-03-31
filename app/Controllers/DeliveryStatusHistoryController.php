<?php

class DeliveryStatusHistoryController extends Controller {

    public function index(): void {
        requireAuth();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $orderId = (int)($_GET['order_id'] ?? 0);
        $histories = [];
        $total     = 0;
        $totalPages = 0;

        if ($orderId) {
            $res       = ApiClient::get('/delivery-status-histories/list', ['page' => $page, 'limit' => $limit, 'order_id' => $orderId], true);
            $histories = $res['data']['data']  ?? [];
            $total     = $res['data']['total'] ?? 0;
            $totalPages = (int)ceil($total / $limit);
        }

        // Always fetch orders for the dropdown
        $ordersRes = ApiClient::get('/orders/list', ['limit' => 1000], true);
        $orders = $ordersRes['data']['data'] ?? [];

        $this->view('delivery-status-histories.index', compact('histories', 'total', 'page', 'totalPages', 'limit', 'orderId', 'orders'));
    }
}
