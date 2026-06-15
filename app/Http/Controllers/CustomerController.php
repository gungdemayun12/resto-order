<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;

class CustomerController extends Controller
{
    public function start(Request $request)
    {
        $meja = $request->input('meja');

        if (session('customer_table') && session('customer_name') && session('customer_phone')) {
            return redirect()->route('menu.list');
        }

        $table = null;
        if ($meja) {
            $table = RestaurantTable::where('number', $meja)->first();
        }

        return view('customer.menu.start', compact('meja', 'table'));
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|exists:restaurant_tables,number',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:13',
        ], [
            'customer_name.required' => 'Nama harus diisi.',
            'customer_phone.required' => 'Nomor telepon harus diisi.',
            'customer_phone.min' => 'Nomor telepon minimal 10 digit.',
            'customer_phone.max' => 'Nomor telepon maksimal 13 digit.',
            'table_number.exists' => 'Meja tidak terdaftar.',
        ]);

        session([
            'customer_table' => $validated['table_number'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
        ]);

        return redirect()->route('menu.list');
    }

    public function list(Request $request)
    {
        $table = $request->_table; 
        $session = TableSession::where('table_id', $table->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            $session = TableSession::create([
                'table_id' => $table->id,
                'session_token' => 'SES-' . strtoupper(substr(md5(uniqid()), 0, 12)),
                'status' => 'active',
                'expires_at' => now()->addHours(2),
                'admin_id' => 1, // Default admin
            ]);
        }

        $sessionToken = $session->session_token;

        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('customer.menu.list', compact('table', 'categories', 'menuItems', 'sessionToken'));
    }

    public function checkout(Request $request)
    {
        $table = $request->_table;
        return view('customer.menu.checkout', compact('table'));
    }

    public function submitOrder(Request $request)
    {
        $table = $request->_table;

        $validated = $request->validate([
            'payment_method' => 'required|in:online,cashier',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $itemSubtotal = $menuItem->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $item['quantity'],
                'price' => $menuItem->price,
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
            ];
        }

        $tax = round($subtotal * 0.11, 2); 
        $totalAmount = $subtotal + $tax;

        $order = Order::create([
            'table_id' => $table->id,
            'session_token' => Str::uuid()->toString(),
            'customer_name' => session('customer_name'),
            'customer_phone' => session('customer_phone'),
            'order_number' => Order::generateOrderNumber(),
            'status' => $validated['payment_method'] === 'online' ? 'pending_payment' : 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $totalAmount,
        ]);

        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        $table->update(['status' => 'occupied']);

        if ($order->payment_method === 'online') {
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
            Config::$is3ds = env('MIDTRANS_IS_3DS', true);

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number . '-' . time(),
                    'gross_amount' => $totalAmount,
                ],
                'customer_details' => [
                    'first_name' => session('customer_name'),
                    'phone' => session('customer_phone'),
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $order->update([
                    'snap_token' => $snapToken,
                    'payment_reference' => $order->order_number . '-' . time(),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'redirect' => route('order.tracking', $order->id),
            'message' => 'Pesanan berhasil dikirim!',
        ]);
    }

    public function tracking(Order $order, Request $request)
    {
        $sessionPhone = session('customer_phone');

        $order->load(['items.menuItem', 'table']);
        return view('customer.order.tracking', compact('order'));
    }

    public function orderStatus(Order $order)
    {
        if ($order->payment_method === 'online' && $order->status === 'pending_payment' && $order->payment_reference) {
            $cacheKey = 'midtrans_verify_' . $order->id;

            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, 30); // throttle 30 seconds

                try {
                    Config::$serverKey = config('services.midtrans.server_key');
                    Config::$isProduction = config('services.midtrans.is_production');
                    Config::$isSanitized = config('services.midtrans.is_sanitized');
                    Config::$is3ds = config('services.midtrans.is_3ds');

                    $transaction = \Midtrans\Transaction::status($order->payment_reference);
                    $transactionStatus = null;

                    if (is_array($transaction)) {
                        $transactionStatus = $transaction['transaction_status'] ?? null;
                    } elseif (is_object($transaction)) {
                        $transactionStatus = $transaction->transaction_status ?? null;
                    }

                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'paid_at' => now(),
                        ]);
                        $order->refresh();
                    } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
                        $order->update(['payment_status' => 'failed']);
                        $order->refresh();
                    }
                } catch (\Exception $e) {
                                        Log::error('orderStatus Midtrans verify error: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'status' => $order->status,
            'status_label' => $order->status_label,
            'updated_at' => $order->updated_at->diffForHumans()
        ]);
    }
}
