<?php

class DashboardController extends Controller {

    public function index(): void {
        requireAuth();

        $endpoints = [
            'categories'         => '/categories/list',
            'products'           => '/products/list',
            'branches'           => '/branches/list',
            'branch_admins'      => '/branch-admins/list',
            'branch_roles'       => '/branch-roles/list',
            'branch_staff'       => '/branch-staff/list',
            'customers'          => '/customers/list',
            'customer_addresses' => '/customer-addresses/list',
            'orders'             => '/orders/list',
            'payments'           => '/payments/list',
            'coupons'            => '/coupons/list',
            'reviews'            => '/reviews/list',
        ];

        $counts = [];
        foreach ($endpoints as $key => $ep) {
            $res = ApiClient::get($ep, ['limit' => 1, 'page' => 1], true);
            $counts[$key] = $res['data']['total'] ?? 0;
        }

        // Revenue & payment stats
        $payRes   = ApiClient::get('/payments/list', ['limit' => 1000], true);
        $payments = $payRes['data']['data'] ?? [];

        $revenue        = array_sum(array_column(array_filter($payments, fn($p) => ($p['status'] ?? '') === 'success'), 'amount'));
        $pendingPayments = count(array_filter($payments, fn($p) => ($p['status'] ?? '') === 'pending'));
        $failedPayments  = count(array_filter($payments, fn($p) => ($p['status'] ?? '') === 'failed'));

        // Order stats
        $orderRes = ApiClient::get('/orders/list', ['limit' => 1000], true);
        $orders   = $orderRes['data']['data'] ?? [];

        $orderStats = array_count_values(array_column($orders, 'order_status'));
        $recentOrders = array_slice(array_reverse($orders), 0, 5);

        // Review stats
        $reviewRes = ApiClient::get('/reviews/list', ['limit' => 1000], true);
        $reviews   = $reviewRes['data']['data'] ?? [];
        $avgRating = count($reviews) ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : 0;

        $this->view('dashboard.index', compact(
            'counts', 'revenue', 'pendingPayments', 'failedPayments',
            'orderStats', 'recentOrders', 'avgRating'
        ));
    }
}
