<?php

// Auth
$router->get('',                    'AuthController@loginForm');
$router->get('auth/login',          'AuthController@loginForm');
$router->post('auth/login',         'AuthController@login');
$router->get('auth/register',       'AuthController@registerForm');
$router->post('auth/register',      'AuthController@register');
$router->get('auth/logout',         'AuthController@logout');

// Dashboard
$router->get('dashboard',           'DashboardController@index');

// Categories
$router->get('categories',          'CategoryController@index');
$router->get('categories/create',   'CategoryController@create');
$router->post('categories/create',  'CategoryController@store');
$router->get('categories/{id}/edit','CategoryController@edit');
$router->post('categories/{id}/edit','CategoryController@update');
$router->post('categories/{id}/delete','CategoryController@destroy');

// Products
$router->get('products',            'ProductController@index');
$router->get('products/create',     'ProductController@create');
$router->post('products/create',    'ProductController@store');
$router->get('products/{id}/edit',  'ProductController@edit');
$router->post('products/{id}/edit', 'ProductController@update');
$router->post('products/{id}/delete','ProductController@destroy');

// Branches
$router->get('branches',                 'BranchController@index');
$router->get('branches/create',          'BranchController@create');
$router->post('branches/create',         'BranchController@store');
$router->get('branches/{id}/edit',       'BranchController@edit');
$router->post('branches/{id}/edit',      'BranchController@update');
$router->post('branches/{id}/delete',    'BranchController@destroy');

// Branch Admins
$router->get('branch-admins',              'BranchAdminController@index');
$router->get('branch-admins/create',       'BranchAdminController@create');
$router->post('branch-admins/create',      'BranchAdminController@store');
$router->get('branch-admins/{id}/edit',    'BranchAdminController@edit');
$router->post('branch-admins/{id}/edit',   'BranchAdminController@update');
$router->post('branch-admins/{id}/delete', 'BranchAdminController@destroy');

// Branch Roles
$router->get('branch-roles',              'BranchRoleController@index');
$router->get('branch-roles/create',       'BranchRoleController@create');
$router->post('branch-roles/create',      'BranchRoleController@store');
$router->get('branch-roles/{id}/edit',    'BranchRoleController@edit');
$router->post('branch-roles/{id}/edit',   'BranchRoleController@update');
$router->post('branch-roles/{id}/delete', 'BranchRoleController@destroy');

// Branch Staff Users
$router->get('branch-staff',              'BranchStaffController@index');
$router->get('branch-staff/create',       'BranchStaffController@create');
$router->post('branch-staff/create',      'BranchStaffController@store');
$router->get('branch-staff/{id}/edit',    'BranchStaffController@edit');
$router->post('branch-staff/{id}/edit',   'BranchStaffController@update');
$router->post('branch-staff/{id}/delete', 'BranchStaffController@destroy');

// Customers
$router->get('customers',                   'CustomerController@index');
$router->get('customers/create',            'CustomerController@create');
$router->post('customers/create',           'CustomerController@store');
$router->get('customers/{id}/edit',         'CustomerController@edit');
$router->post('customers/{id}/edit',        'CustomerController@update');
$router->post('customers/{id}/delete',      'CustomerController@destroy');

// Customer Addresses
$router->get('customer-addresses',              'CustomerAddressController@index');
$router->get('customer-addresses/create',       'CustomerAddressController@create');
$router->post('customer-addresses/create',      'CustomerAddressController@store');
$router->get('customer-addresses/{id}/edit',    'CustomerAddressController@edit');
$router->post('customer-addresses/{id}/edit',   'CustomerAddressController@update');
$router->post('customer-addresses/{id}/delete', 'CustomerAddressController@destroy');

// Orders
$router->get('orders',                  'OrderController@index');
$router->get('orders/create',           'OrderController@create');
$router->post('orders/create',          'OrderController@store');
$router->get('orders/{id}/edit',        'OrderController@edit');
$router->post('orders/{id}/edit',       'OrderController@update');
$router->post('orders/{id}/delete',     'OrderController@destroy');

// Payments
$router->get('payments',                'PaymentController@index');
$router->get('payments/create',         'PaymentController@create');
$router->post('payments/create',        'PaymentController@store');
$router->get('payments/{id}/edit',      'PaymentController@edit');
$router->post('payments/{id}/edit',     'PaymentController@update');
$router->post('payments/{id}/delete',   'PaymentController@destroy');

// Coupons
$router->get('coupons',                 'CouponController@index');
$router->get('coupons/create',          'CouponController@create');
$router->post('coupons/create',         'CouponController@store');
$router->get('coupons/{id}/edit',       'CouponController@edit');
$router->post('coupons/{id}/edit',      'CouponController@update');
$router->post('coupons/{id}/delete',    'CouponController@destroy');

// Reviews
$router->get('reviews',                 'ReviewController@index');
$router->get('reviews/create',          'ReviewController@create');
$router->post('reviews/create',         'ReviewController@store');
$router->get('reviews/{id}/edit',       'ReviewController@edit');
$router->post('reviews/{id}/edit',      'ReviewController@update');
$router->post('reviews/{id}/delete',    'ReviewController@destroy');

// Carts
$router->get('carts',                   'CartController@index');
$router->get('carts/create',            'CartController@create');
$router->post('carts/create',           'CartController@store');
$router->get('carts/{id}/edit',         'CartController@edit');
$router->post('carts/{id}/edit',        'CartController@update');
$router->post('carts/{id}/delete',      'CartController@destroy');

// Wishlists
$router->get('wishlists',               'WishlistController@index');
$router->get('wishlists/create',        'WishlistController@create');
$router->post('wishlists/create',       'WishlistController@store');
$router->get('wishlists/{id}/edit',     'WishlistController@edit');
$router->post('wishlists/{id}/edit',    'WishlistController@update');
$router->post('wishlists/{id}/delete',  'WishlistController@destroy');

// Notifications
$router->get('notifications',            'NotificationController@index');
$router->post('notifications/send',      'NotificationController@send');
