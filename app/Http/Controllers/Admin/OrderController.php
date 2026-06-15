<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->buildOrdersQuery($request)->paginate(20);
        $statusCounts = $this->statusCounts($request);

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function feed(Request $request)
    {
        $orders = $this->buildOrdersQuery($request)->paginate(20);
        $statusCounts = $this->statusCounts($request);

        return response()->json([
            'html' => view('admin.orders._cards', compact('orders'))->render(),
            'status_counts' => $statusCounts,
            'pending_count' => $statusCounts['pending'] ?? 0,
            'total' => $orders->total(),
        ]);
    }

    private function buildOrdersQuery(Request $request)
    {
        $query = Order::with(['table', 'items.menuItem']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', ['completed', 'cancelled', 'pending_confirmation', 'pending_payment']);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(str_replace(' ', '', $request->search));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(REPLACE(order_number, ' ', '')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("LOWER(REPLACE(customer_name, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    private function statusCounts(Request $request): array
    {
        $date = $request->filled('date') ? $request->date : Carbon::today();

        return [
            'all' => Order::whereDate('created_at', $date)->count(),
            'pending' => Order::whereDate('created_at', $date)->where('status', 'pending')->count(),
            'payment_success' => Order::whereDate('created_at', $date)->where('status', 'payment_success')->count(),
            'processing' => Order::whereDate('created_at', $date)->where('status', 'processing')->count(),
            'ready' => Order::whereDate('created_at', $date)->where('status', 'ready')->count(),
            'completed' => Order::whereDate('created_at', $date)->where('status', 'completed')->count(),
            'cancelled' => Order::whereDate('created_at', $date)->where('status', 'cancelled')->count(),
        ];
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menuItem']);
        return view('admin.orders.show', compact('order'));
    }

    public function apiData(Order $order)
    {
        $order->load(['table', 'items.menuItem']);
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status ?? 'unpaid',
            'total_amount' => $order->total_amount,
            'formatted_total' => $order->formatted_total,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'table_number' => $order->table ? $order->table->number : '-',
            'created_at' => $order->created_at->format('d M Y, H:i'),
            'notes' => $order->notes,
            'items' => $order->items->map(fn($item) => [
                'name' => $item->menuItem->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'notes' => $item->notes,
                'image_url' => $item->menuItem->image_url ?? null,
            ])->toArray(),
        ];
        return response()->json($data);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,payment_success,processing,ready,completed,cancelled',
        ]);

        if ($validated['status'] === 'processing' && in_array($order->status, ['pending_payment', 'pending_confirmation'], true)) {
            if ($order->payment_method === 'online' && $order->payment_status === 'paid') {
                // Allow
            } elseif ($order->payment_method === 'cashier' && $order->status === 'pending_confirmation') {
                // Allow
            } else {
                $message = $order->payment_method === 'online'
                    ? 'Pesanan belum dibayar. Customer harus menyelesaikan pembayaran terlebih dahulu.'
                    : 'Pesanan belum dikonfirmasi kasir. Scan barcode untuk konfirmasi.';

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->with('error', $message);
            }
        }

        $payload = ['status' => $validated['status']];

        if ($validated['status'] === 'processing' && $order->payment_method === 'online' && $order->payment_status !== 'paid') {
            $payload['payment_status'] = 'paid';
        }

        $order->update($payload);

        if (in_array($validated['status'], ['completed', 'cancelled'])) {
            $activeOrders = Order::where('table_id', $order->table_id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();

            if ($activeOrders === 0) {
                optional($order->table)->update(['status' => 'available']);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'message' => 'Status pesanan berhasil diperbarui!',
            ]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function getLatest(Request $request)
    {
        $after = $request->input('after');

        $query = Order::with(['table', 'items.menuItem'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('status', ['pending_confirmation', 'pending_payment']);

        if ($after) {
            $query->where('updated_at', '>', $after);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $pendingCount = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'orders' => $orders,
            'pending_count' => $pendingCount,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function confirmOrder($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if ($order->status !== 'pending_confirmation') {
            return role_redirect('admin.orders.index')->with('error', 'Pesanan sudah dikonfirmasi atau tidak memerlukan konfirmasi kasir.');
        }

        $order->update([
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        return role_redirect('admin.orders.index')->with('success', 'Pesanan #' . $orderNumber . ' dikonfirmasi — mulai diproses dapur.');
    }

    public function history(Request $request)
    {
        $query = Order::with(['table', 'items.menuItem'])->where('status', 'completed');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(str_replace(' ', '', $request->search));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(REPLACE(order_number, ' ', '')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("LOWER(REPLACE(customer_name, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.history', compact('orders'));
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        if ($order->status !== 'pending_confirmation') {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah dikonfirmasi atau tidak memerlukan konfirmasi',
            ], 400);
        }

        $order->update([
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dikonfirmasi',
            'order' => $order->load(['table', 'items.menuItem']),
        ]);
    }

    public function scanPage()
    {
        return view('admin.orders.scan');
    }
}