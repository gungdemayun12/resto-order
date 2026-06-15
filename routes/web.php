<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\QrScanController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('customer.welcome'))->name('home');

Route::get('/qr/{qrToken}', [QrScanController::class, 'scan'])->name('qr.scan');
Route::get('/customer/form', [QrScanController::class, 'showForm'])->name('customer.form');
Route::post('/customer/form', [QrScanController::class, 'storeCustomerData'])->name('customer.form.store');
Route::post('/session/validate', [QrScanController::class, 'validateSession'])->name('session.validate');
Route::get('/customer/menu', [CustomerMenuController::class, 'index'])->name('customer.menu');
Route::post('/customer/cart/add', [CustomerMenuController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/customer/cart/update', [CustomerMenuController::class, 'updateCart'])->name('customer.cart.update');
Route::post('/customer/cart/remove', [CustomerMenuController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::get('/customer/cart', [CustomerMenuController::class, 'getCart'])->name('customer.cart.get');
Route::get('/customer/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');
Route::post('/customer/checkout/process', [CheckoutController::class, 'process'])->name('customer.checkout.process');
Route::get('/customer/order/{order}/barcode', [CheckoutController::class, 'barcode'])->name('customer.order.barcode');
Route::get('/customer/order/{order}/check-confirmation', [CheckoutController::class, 'checkConfirmation'])->name('customer.order.check-confirmation');
Route::get('/customer/order/{order}/success', [CheckoutController::class, 'success'])->name('customer.order.success');
Route::get('/customer/order/{order}/receipt', [CheckoutController::class, 'receipt'])->name('customer.order.receipt');
Route::get('/customer/order/{order}/payment', [CheckoutController::class, 'payment'])->name('customer.payment');
Route::get('/customer/order/{order}/verify-payment', [CheckoutController::class, 'verifyPayment'])->name('customer.order.verify-payment');
Route::get('/customer/order/{order}/switch-cashier', [CheckoutController::class, 'switchToCashier'])->name('customer.order.switch-cashier');
Route::post('/payment/callback', [CheckoutController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/csrf-token', fn() => response()->json(['token' => csrf_token()]))->name('csrf.token');
Route::get('/customer/order/{order}/tracking', [CustomerController::class, 'tracking'])->name('order.tracking');
Route::get('/order/{order}/status', [CustomerController::class, 'orderStatus'])->name('order.status');
Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservasi.index');
Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');
Route::get('/reservasi/check', [ReservationController::class, 'checkStatus'])->name('reservasi.check');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

Route::prefix('admin')->middleware(['auth', 'role.prefix:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:view_dashboard');
    Route::get('/dashboard/latest', [DashboardController::class, 'latest'])->name('dashboard.latest')->middleware('permission:view_dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index')->middleware('permission:view_orders');
    Route::get('/orders/history', [AdminOrderController::class, 'history'])->name('orders.history')->middleware('permission:view_orders');
    Route::get('/orders/scan', [AdminOrderController::class, 'scanPage'])->name('orders.scan')->middleware('permission:scan_orders');
    Route::post('/orders/scan-barcode', [AdminOrderController::class, 'scanBarcode'])->name('orders.scan-barcode')->middleware('permission:scan_orders');
    Route::get('/orders/confirm/{orderNumber}', [AdminOrderController::class, 'confirmOrder'])->name('orders.confirm')->middleware('permission:view_orders');
    Route::get('/orders/get-latest', [AdminOrderController::class, 'getLatest'])->name('orders.get-latest')->middleware('permission:view_orders');
    Route::get('/orders/feed', [AdminOrderController::class, 'feed'])->name('orders.feed')->middleware('permission:view_orders');
    Route::get('/orders/{order}/data', [AdminOrderController::class, 'apiData'])->name('orders.data')->middleware('permission:view_orders');
    Route::match(['post', 'patch'], '/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status')->middleware('permission:view_orders');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show')->middleware('permission:view_orders');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index')->middleware('permission:manage_menu');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store')->middleware('permission:manage_menu');
    Route::put('/menu/{menuItem}', [MenuController::class, 'update'])->name('menu.update')->middleware('permission:manage_menu');
    Route::delete('/menu/{menuItem}', [MenuController::class, 'destroy'])->name('menu.destroy')->middleware('permission:manage_menu');
    Route::patch('/menu/{menuItem}/toggle', [MenuController::class, 'toggleAvailability'])->name('menu.toggle')->middleware('permission:manage_menu');

    Route::get('/tables', [TableController::class, 'index'])->name('tables.index')->middleware('permission:view_tables');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store')->middleware('permission:view_tables');
    Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus')->middleware('permission:view_tables');
    Route::match(['PATCH', 'POST'], '/tables/{table}', [TableController::class, 'update'])->name('tables.update')->middleware('permission:view_tables');
    Route::get('/tables/{table}/qr', [TableController::class, 'generateQR'])->name('tables.qr')->middleware('permission:view_tables');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy')->middleware('permission:view_tables');
    Route::post('/tables/{table}/regenerate-qr', [SessionController::class, 'regenerateQr'])->name('tables.regenerateQr')->middleware('permission:view_tables');

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('permission:view_sessions');
    Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show')->middleware('permission:view_sessions');
    Route::post('/sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close')->middleware('permission:view_sessions');
    Route::post('/sessions/expire', [SessionController::class, 'expireOldSessions'])->name('sessions.expire')->middleware('permission:view_sessions');

    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index')->middleware('permission:view_reservations');
    Route::patch('/reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.updateStatus');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:view_reports');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf')->middleware('permission:view_reports');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel')->middleware('permission:view_reports');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware('permission:manage_users');
    Route::patch('/settings/users/{user}', [SettingsController::class, 'update'])->name('settings.update')->middleware('permission:manage_users');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('permission:manage_users');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('permission:manage_users');
    Route::match(['put', 'patch'], '/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update')->middleware('permission:manage_users');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('permission:manage_users');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    Route::get('/search', [DashboardController::class, 'globalSearch'])->name('search');
});

Route::prefix('kasir')->middleware(['auth', 'role.prefix:kasir'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:view_dashboard');
    Route::get('/dashboard/latest', [DashboardController::class, 'latest'])->name('dashboard.latest')->middleware('permission:view_dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index')->middleware('permission:view_orders');
    Route::get('/orders/history', [AdminOrderController::class, 'history'])->name('orders.history')->middleware('permission:view_orders');
    Route::get('/orders/scan', [AdminOrderController::class, 'scanPage'])->name('orders.scan')->middleware('permission:scan_orders');
    Route::post('/orders/scan-barcode', [AdminOrderController::class, 'scanBarcode'])->name('orders.scan-barcode')->middleware('permission:scan_orders');
    Route::get('/orders/confirm/{orderNumber}', [AdminOrderController::class, 'confirmOrder'])->name('orders.confirm')->middleware('permission:view_orders');
    Route::get('/orders/get-latest', [AdminOrderController::class, 'getLatest'])->name('orders.get-latest')->middleware('permission:view_orders');
    Route::get('/orders/feed', [AdminOrderController::class, 'feed'])->name('orders.feed')->middleware('permission:view_orders');
    Route::get('/orders/{order}/data', [AdminOrderController::class, 'apiData'])->name('orders.data')->middleware('permission:view_orders');
    Route::match(['post', 'patch'], '/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status')->middleware('permission:view_orders');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show')->middleware('permission:view_orders');

    Route::get('/tables', [TableController::class, 'index'])->name('tables.index')->middleware('permission:view_tables');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store')->middleware('permission:view_tables');
    Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus')->middleware('permission:view_tables');
    Route::match(['PATCH', 'POST'], '/tables/{table}', [TableController::class, 'update'])->name('tables.update')->middleware('permission:view_tables');
    Route::get('/tables/{table}/qr', [TableController::class, 'generateQR'])->name('tables.qr')->middleware('permission:view_tables');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy')->middleware('permission:view_tables');
    Route::post('/tables/{table}/regenerate-qr', [SessionController::class, 'regenerateQr'])->name('tables.regenerateQr')->middleware('permission:view_tables');

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('permission:view_sessions');
    Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show')->middleware('permission:view_sessions');
    Route::post('/sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close')->middleware('permission:view_sessions');
    Route::post('/sessions/expire', [SessionController::class, 'expireOldSessions'])->name('sessions.expire')->middleware('permission:view_sessions');

    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index')->middleware('permission:view_reservations');
    Route::patch('/reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.updateStatus');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    Route::get('/search', [DashboardController::class, 'globalSearch'])->name('search');
});

Route::prefix('dapur')->middleware(['auth', 'role.prefix:dapur'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kitchenDashboard'])->name('dashboard')->middleware('permission:view_orders');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index')->middleware('permission:view_orders');
    Route::get('/orders/history', [AdminOrderController::class, 'history'])->name('orders.history')->middleware('permission:view_orders');
    Route::get('/orders/get-latest', [AdminOrderController::class, 'getLatest'])->name('orders.get-latest')->middleware('permission:view_orders');
    Route::get('/orders/feed', [AdminOrderController::class, 'feed'])->name('orders.feed')->middleware('permission:view_orders');
    Route::get('/orders/{order}/data', [AdminOrderController::class, 'apiData'])->name('orders.data')->middleware('permission:view_orders');
    Route::match(['post', 'patch'], '/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status')->middleware('permission:view_orders');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show')->middleware('permission:view_orders');

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('permission:view_sessions');
    Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show')->middleware('permission:view_sessions');
    Route::post('/sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close')->middleware('permission:view_sessions');
    Route::post('/sessions/expire', [SessionController::class, 'expireOldSessions'])->name('sessions.expire')->middleware('permission:view_sessions');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});
