<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\TableSession;
use App\Models\Order;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    public function index(Request $request)
    {
        $sessionToken = $request->query('session');

        $session = TableSession::where('session_token', $sessionToken)
            ->with('table')
            ->first();

        if (!$session) {
            return view('customer.error', [
                'title' => 'Session Tidak Valid',
                'message' => 'Session Anda tidak ditemukan. Silakan scan QR code lagi.',
            ]);
        }

        if (!$session->isActive()) {
            return view('customer.session-ended');
        }

        $customerData = session()->get('customer_data_' . $sessionToken);
        if (!$customerData) {

            return redirect()->route('customer.form', ['session' => $sessionToken]);
        }

        $categories = Category::with([
            'menuItems' => function ($query) {
                $query->orderBy('name');
            }
        ])->orderBy('name')->get();

        $perPage = 12;
        $activeCategory = $request->query('category', 'all');

        $menuQuery = MenuItem::query();

        if ($activeCategory !== 'all') {
            $categoryExists = Category::whereKey($activeCategory)->exists();
            if (!$categoryExists) {
                $activeCategory = 'all';
            } else {
                $menuQuery->where('category_id', $activeCategory);
            }
        }

        $paginationAppends = ['session' => $sessionToken];
        if ($activeCategory !== 'all') {
            $paginationAppends['category'] = $activeCategory;
        }

        $menuItems = $menuQuery
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($paginationAppends);

        $cart = session()->get('cart_' . $sessionToken, []);

        $activeOrder = Order::where('session_token', $sessionToken)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->first();

        return view('customer.menu', [
            'session' => $session,
            'categories' => $categories,
            'menuItems' => $menuItems,
            'cart' => $cart,
            'table' => $session->table,
            'customerData' => $customerData,
            'activeOrder' => $activeOrder,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'session_token' => 'required|string',
                'menu_item_id' => 'required|exists:menu_items,id',
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500',
            ]);

            $sessionToken = $request->session_token;

            $session = TableSession::where('session_token', $sessionToken)->first();
            if (!$session || !$session->isActive()) {
                \Log::error('Cart add failed: Invalid session', ['session_token' => $sessionToken, 'session' => $session]);
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak valid atau sudah expired',
                ], 403);
            }

            $menuItem = MenuItem::findOrFail($request->menu_item_id);

            if (!$menuItem->is_available) {
                \Log::error('Cart add failed: Menu not available', ['menu_item_id' => $request->menu_item_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak tersedia',
                ], 400);
            }

            $cart = session()->get('cart_' . $sessionToken, []);

            $cartKey = $menuItem->id . '_' . md5($request->notes ?? '');

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $request->quantity;
            } else {

                $cart[$cartKey] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'price' => $menuItem->price,
                    'quantity' => $request->quantity,
                    'notes' => $request->notes,
                    'subtotal' => $menuItem->price * $request->quantity,
                ];
            }

            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['price'] * $cart[$cartKey]['quantity'];

            session()->put('cart_' . $sessionToken, $cart);

            $total = collect($cart)->sum('subtotal');
            $itemCount = collect($cart)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang',
                'cart_count' => $itemCount,
                'cart_total' => $total,
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart add exception', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambah ke keranjang',
            ], 500);
        }
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
            'cart_key' => 'nullable|string',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $sessionToken = $request->session_token;
        $cart = session()->get('cart_' . $sessionToken, []);

        $cartKey = $request->cart_key;

        if (!$cartKey && $request->menu_item_id) {
            $cartKey = $request->menu_item_id . '_' . md5('');
        }

        if (!$cartKey || !isset($cart[$cartKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang',
            ], 404);
        }

        if ($request->quantity == 0) {
            unset($cart[$cartKey]);
        } else {

            $cart[$cartKey]['quantity'] = $request->quantity;
            $cart[$cartKey]['subtotal'] =
                $cart[$cartKey]['price'] * $request->quantity;
        }

        session()->put('cart_' . $sessionToken, $cart);

        $total = collect($cart)->sum('subtotal');
        $itemCount = collect($cart)->sum('quantity');

        return response()->json([
            'success' => true,
            'cart_count' => $itemCount,
            'cart_total' => $total,
            'item_subtotal' => $cart[$cartKey]['subtotal'] ?? 0,
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
            'cart_key' => 'required|string',
        ]);

        $sessionToken = $request->session_token;
        $cart = session()->get('cart_' . $sessionToken, []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart_' . $sessionToken, $cart);
        }

        $total = collect($cart)->sum('subtotal');
        $itemCount = collect($cart)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus',
            'cart_count' => $itemCount,
            'cart_total' => $total,
        ]);
    }

    public function getCart(Request $request)
    {
        $sessionToken = $request->query('session_token');
        $cart = session()->get('cart_' . $sessionToken, []);

        $total = collect($cart)->sum('subtotal');
        $itemCount = collect($cart)->sum('quantity');

        return response()->json([
            'success' => true,
            'cart' => array_values($cart),
            'cart_count' => $itemCount,
            'cart_total' => $total,
        ]);
    }
}