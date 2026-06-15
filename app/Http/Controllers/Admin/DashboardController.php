<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastMonth = Carbon::today()->subMonth();
        $user = auth()->user();

        $dateRange = $request->get('date_range', 'today');

        switch ($dateRange) {
            case 'week':
                $filterStart = Carbon::now()->subDays(6)->startOfDay();
                $filterEnd   = Carbon::now()->endOfDay();
                $prevStart   = Carbon::now()->subDays(13)->startOfDay();
                $prevEnd     = Carbon::now()->subDays(7)->endOfDay();
                break;
            case 'month':
                $filterStart = Carbon::now()->startOfMonth();
                $filterEnd   = Carbon::now()->endOfDay();
                $prevStart   = Carbon::now()->subMonth()->startOfMonth();
                $prevEnd     = Carbon::now()->subMonth()->endOfMonth();
                break;
            default: // today
                $filterStart = Carbon::today()->startOfDay();
                $filterEnd   = Carbon::today()->endOfDay();
                $prevStart   = Carbon::yesterday()->startOfDay();
                $prevEnd     = Carbon::yesterday()->endOfDay();
                break;
        }

        $baseQuery = $this->orderQueryForUser()
            ->whereBetween('created_at', [$filterStart, $filterEnd]);

        $prevQuery = $this->orderQueryForUser()
            ->whereBetween('created_at', [$prevStart, $prevEnd]);

        $currentRevenue = $baseQuery->clone()
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->sum('total_amount');
        $prevRevenue = $prevQuery->clone()
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->sum('total_amount');
        $currentCount = $baseQuery->clone()->count();
        $prevCount = $prevQuery->clone()->count();

        $orderGrowth = $prevCount > 0 ? round((($currentCount - $prevCount) / $prevCount) * 100, 1) : 0;
        $revenueGrowth = $prevRevenue > 0 ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

        $activeTablesCount = $user->hasPermission('view_tables') ? RestaurantTable::where('status', 'occupied')->whereBetween('updated_at', [$filterStart, $filterEnd])->count() : 0;
        $totalTablesCount = $user->hasPermission('view_tables') ? RestaurantTable::count() : 0;

        $reservationsTodayCount = $user->hasPermission('view_reservations') ? Reservation::whereDate('date', '>=', $filterStart->toDateString())->whereDate('date', '<=', $filterEnd->toDateString())->count() : 0;
        $pendingReservationsCount = $user->hasPermission('view_reservations') ? Reservation::where('status', 'pending')->count() : 0;

        $allCompletedFiltered = Order::whereBetween('created_at', [$filterStart, $filterEnd])->where('status', 'completed')->count();
        $avgPrepTime = Order::whereBetween('created_at', [$filterStart, $filterEnd])
            ->whereNotNull('paid_at')
            ->where('status', 'completed')
            ->get()
            ->avg(function ($order) {
                if ($order->paid_at) {
                    return $order->created_at->diffInMinutes($order->paid_at);
                }
                return null;
            });
        $avgPrepTimeFormatted = $avgPrepTime ? floor($avgPrepTime) . 'm ' . round(($avgPrepTime - floor($avgPrepTime)) * 60) . 's' : 'N/A';

        $stats = [
            'orders_today' => $currentCount,
            'revenue_today' => $currentRevenue,
            'active_tables' => $activeTablesCount,
            'total_tables' => $totalTablesCount,
            'reservations_today' => $reservationsTodayCount,
            'pending_reservations' => $pendingReservationsCount,
            'order_growth' => $orderGrowth,
            'revenue_growth' => $revenueGrowth,
            'completed_today' => $allCompletedFiltered,
            'avg_prep_time' => $avgPrepTimeFormatted,
        ];

        $weeklyRevenue = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$filterStart, $filterEnd])
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $lastWeekStart = match($dateRange) {
            'week' => Carbon::now()->subDays(13)->startOfDay(),
            'month' => Carbon::now()->subMonth()->startOfMonth(),
            default => Carbon::yesterday()->startOfDay(),
        };
        $lastWeekEnd = match($dateRange) {
            'week' => Carbon::now()->subDays(7)->endOfDay(),
            'month' => Carbon::now()->subMonth()->endOfMonth(),
            default => Carbon::yesterday()->endOfDay(),
        };
        $lastWeekRevenueData = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $topMenuItems = OrderItem::selectRaw('menu_items.name, COUNT(*) as total_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereDate('order_items.created_at', '>=', $lastMonth)
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $orderStatusDistribution = $baseQuery->clone()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $peakHours = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', $dateRange === 'today' ? $today : $today)
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(6)
            ->get();

        $miniChartData = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count');

        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $tableUtilization = RestaurantTable::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        $tableUtilizationArray = $tableUtilization->mapWithKeys(fn($item) => [$item->status => $item->count])->toArray();

        $recentOrders = $baseQuery->clone()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $todayReservations = $user->hasPermission('view_reservations')
            ? Reservation::whereDate('date', $today)->orderBy('time', 'asc')->get()
            : collect();

        $revenueTrend = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$filterStart, $filterEnd])
            ->whereIn('status', ['processing', 'ready', 'completed', 'payment_success', 'pending_confirmation'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'weeklyRevenue',
            'lastWeekRevenueData',
            'topMenuItems',
            'orderStatusDistribution',
            'peakHours',
            'monthlyRevenue',
            'tableUtilization',
            'tableUtilizationArray',
            'recentOrders',
            'todayReservations',
            'revenueTrend',
            'miniChartData',
            'dateRange',
            'filterStart',
            'filterEnd',
        ));
    }

    public function latest(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasPermission('view_dashboard')) {
            return response()->json([
                'stats' => [],
                'recentOrders' => [],
                'orderStatusDistribution' => [],
                'todayReservations' => [],
            ]);
        }

        $dateRange = $request->get('date_range', 'today');
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $dateFilter = match($dateRange) {
            'week' => $today->subDays(6),
            'month' => $today->startOfMonth(),
            default => $today,
        };

        $orders = $this->orderQueryForUser()
            ->whereDate('created_at', '>=', $dateFilter)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $orderStatusDistribution = $this->orderQueryForUser()
            ->whereDate('created_at', '>=', $dateFilter)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $totalOrders = $this->orderQueryForUser()->whereDate('created_at', '>=', $dateFilter)->count();

        return response()->json([
            'stats' => [
                'orders_today' => $totalOrders,
                'revenue_today' => $this->orderQueryForUser()
                    ->whereBetween('created_at', [$dateFilter->startOfDay(), $dateFilter->endOfDay()])
                    ->whereIn('status', ['processing', 'ready', 'completed'])
                    ->sum('total_amount'),
                'active_tables' => $user->hasPermission('view_tables') ? RestaurantTable::where('status', 'occupied')->count() : 0,
                'total_tables' => $user->hasPermission('view_tables') ? RestaurantTable::count() : 0,
                'reservations_today' => $user->hasPermission('view_reservations') ? Reservation::whereDate('date', $today)->count() : 0,
                'pending_reservations' => $user->hasPermission('view_reservations') ? Reservation::where('status', 'pending')->count() : 0,
            ],
            'recentOrders' => $orders,
            'orderStatusDistribution' => $orderStatusDistribution,
            'todayReservations' => $user->hasPermission('view_reservations') ? Reservation::whereDate('date', $dateFilter)->orderBy('time', 'asc')->get() : collect(),
        ]);
    }

    private function orderQueryForUser()
    {
        $user = auth()->user();
        $query = Order::with(['table', 'items.menuItem']);

        if ($user->isKitchen()) {
            $query->whereIn('status', ['processing', 'ready', 'completed']);
        } elseif ($user->isCashier()) {
            $query->whereNotIn('status', ['pending_payment']);
        }

        return $query;
    }

    public function kitchenDashboard()
    {
        $today = Carbon::today();

        $cookingOrders = Order::with(['table', 'items.menuItem'])
            ->whereDate('created_at', $today)
            ->where('status', 'processing')
            ->orderBy('created_at', 'asc')
            ->get();

        $readyOrders = Order::with(['table', 'items.menuItem'])
            ->whereDate('created_at', $today)
            ->where('status', 'ready')
            ->orderBy('created_at', 'asc')
            ->get();

        $completedToday = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $totalItemsToday = OrderItem::whereHas('order', function ($q) use ($today) {
            $q->whereDate('created_at', $today)
              ->whereIn('status', ['processing', 'ready', 'completed']);
        })->sum('quantity');

        $avgCookTime = Order::whereDate('created_at', $today)
            ->whereIn('status', ['ready', 'completed'])
            ->get()
            ->avg(function ($order) {
                return $order->items->sum(fn($i) => $i->menuItem->estimated_time ?? 0) / max($order->items->count(), 1);
            });

        $queueCount = Order::whereDate('created_at', $today)
            ->whereIn('status', ['pending', 'payment_success'])
            ->count();

        $topMenusToday = OrderItem::selectRaw('menu_item_id, SUM(quantity) as total_qty')
            ->whereHas('order', function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->whereIn('status', ['processing', 'ready', 'completed']);
            })
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('menuItem')
            ->get();

        $hourlyTrend = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['processing', 'ready', 'completed'])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.kitchen-dashboard', compact(
            'cookingOrders',
            'readyOrders',
            'completedToday',
            'totalItemsToday',
            'avgCookTime',
            'queueCount',
            'topMenusToday',
            'hourlyTrend',
        ));
    }

    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            if ($request->user()->hasPermission('view_orders')) {
                $orders = Order::with(['table'])
                    ->where(function ($q) use ($query) {
                        $q->where('order_number', 'LIKE', "%{$query}%")
                            ->orWhere('customer_name', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($orders as $order) {
                    $results[] = [
                        'type' => 'order',
                        'title' => '#' . $order->order_number,
                        'subtitle' => 'Meja ' . ($order->table->number ?? '-') . ' · ' . $order->status_label,
                        'url' => role_route('admin.orders.index', ['search' => $order->order_number]),
                        'icon' => 'receipt',
                        'color' => 'amber',
                    ];
                }
            }

            if ($request->user()->hasPermission('manage_menu')) {
                $menuItems = MenuItem::where('name', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($menuItems as $item) {
                    $results[] = [
                        'type' => 'menu',
                        'title' => $item->name,
                        'subtitle' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                        'url' => role_route('admin.menu.index'),
                        'icon' => 'menu',
                        'color' => 'blue',
                    ];
                }
            }

            if ($request->user()->hasPermission('view_tables')) {
                $tables = RestaurantTable::where('number', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->get();

                foreach ($tables as $table) {
                    $results[] = [
                        'type' => 'table',
                        'title' => 'Meja ' . $table->number,
                        'subtitle' => ucfirst($table->status),
                        'url' => role_route('admin.tables.index'),
                        'icon' => 'table',
                        'color' => 'emerald',
                    ];
                }
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['results' => $results, 'total' => count($results)]);
        }

        return view('admin.search', compact('results', 'query'));
    }
}
