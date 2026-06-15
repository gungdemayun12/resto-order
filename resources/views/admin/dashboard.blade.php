@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan performa Anda hari ini')

@section('content')
    <div x-data="{ tab: 'overview', dateFilter: 'today', ...dashboardManager() }" class="space-y-8 pb-10">

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p>Anda masuk sebagai <span class="font-bold">{{ auth()->user()->role_label }}</span>.</p>
                @if(auth()->user()->isKitchen())
                    <p class="mt-1 text-blue-600/80">Dashboard ini memuat pesanan dapur secara real-time, tanpa perlu refresh.</p>
                @elseif(auth()->user()->isCashier())
                    <p class="mt-1 text-blue-600/80">Dashboard kasir akan menampilkan pesanan dan meja aktif sesuai hak akses Anda.</p>
                @else
                    <p class="mt-1 text-blue-600/80">Pemilik dapat mengelola role, akses, dan melihat data dashboard secara penuh.</p>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4 mt-6">
            <div class="flex items-center gap-6 overflow-x-auto no-scrollbar">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'text-amber-600 border-b-[3px] border-amber-600 font-bold pb-4 -mb-[17px]' : 'text-slate-500 font-medium pb-4 -mb-[17px] hover:text-slate-800 transition-colors'">Overview</button>
                <button @click="tab = 'audiences'" :class="tab === 'audiences' ? 'text-amber-600 border-b-[3px] border-amber-600 font-bold pb-4 -mb-[17px]' : 'text-slate-500 font-medium pb-4 -mb-[17px] hover:text-slate-800 transition-colors'">Penjualan</button>
                <button @click="tab = 'demographics'" :class="tab === 'demographics' ? 'text-amber-600 border-b-[3px] border-amber-600 font-bold pb-4 -mb-[17px]' : 'text-slate-500 font-medium pb-4 -mb-[17px] hover:text-slate-800 transition-colors'">Operasional</button>
                <button @click="tab = 'more'" :class="tab === 'more' ? 'text-amber-600 border-b-[3px] border-amber-600 font-bold pb-4 -mb-[17px]' : 'text-slate-500 font-medium pb-4 -mb-[17px] hover:text-slate-800 transition-colors'">Lainnya</button>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <select name="date_range" onchange="this.form.submit()" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-all shadow-sm hover:shadow">
                        <option value="today" {{ ($dateRange ?? 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ ($dateRange ?? 'today') == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="month" {{ ($dateRange ?? 'today') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                </form>

            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 border-b border-slate-200 pb-8 pt-4" x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="group cursor-pointer">
                <p class="text-[13px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">Pesanan Masuk</p>
                <h3 class="text-[34px] font-black text-slate-800 leading-none tracking-tight mb-2 group-hover:text-amber-600 transition-colors" id="orders-today-count">{{ $stats['orders_today'] }}</h3>
                <p class="text-[13px] {{ $stats['order_growth'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $stats['order_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/></svg>
                    {{ $stats['order_growth'] >= 0 ? '+' : '' }}{{ $stats['order_growth'] }}% <span class="text-slate-400 font-medium ml-1">dari kemarin</span>
                </p>
            </div>
            <div class="group cursor-pointer">
                <p class="text-[13px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">Pendapatan</p>
                @php
                    $revFormatted = number_format($stats['revenue_today'] / 1000, 1, ',', '.') . 'K';
                @endphp
                <h3 class="text-[34px] font-black text-slate-800 leading-none tracking-tight mb-2 group-hover:text-amber-600 transition-colors" id="revenue-today-count">{{ $revFormatted }}</h3>
                <p class="text-[13px] {{ $stats['revenue_growth'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $stats['revenue_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/></svg>
                    {{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ $stats['revenue_growth'] }}% <span class="text-slate-400 font-medium ml-1">dari kemarin</span>
                </p>
            </div>
            <div class="group cursor-pointer">
                <p class="text-[13px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">Meja Aktif</p>
                <h3 class="text-[34px] font-black text-slate-800 leading-none tracking-tight mb-2 group-hover:text-amber-600 transition-colors"><span id="active-tables-count">{{ $stats['active_tables'] }}</span><span class="text-2xl text-slate-300 font-bold">/{{ $stats['total_tables'] }}</span></h3>
                @php $prevActive = $stats['active_tables']; $tableGrowth = 0; @endphp
                <p class="text-[13px] text-slate-400 font-bold flex items-center gap-1">
                    <span class="text-slate-400 font-medium ml-1">{{ $stats['active_tables'] }} meja saat ini</span>
                </p>
            </div>
            <div class="group cursor-pointer">
                <p class="text-[13px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">Reservasi Baru</p>
                <h3 class="text-[34px] font-black text-slate-800 leading-none tracking-tight mb-2 group-hover:text-amber-600 transition-colors" id="reservations-today-count">{{ $stats['reservations_today'] }}</h3>
                <p class="text-[13px] text-slate-400 font-bold flex items-center gap-1">
                    <span class="text-slate-400 font-medium ml-1">{{ $stats['pending_reservations'] }} menunggu konfirmasi</span>
                </p>
            </div>
            <div class="hidden lg:block group cursor-pointer">
                <p class="text-[13px] text-slate-500 font-semibold mb-1 uppercase tracking-wider">Selesai Hari Ini</p>
                <h3 class="text-[34px] font-black text-slate-800 leading-none tracking-tight mb-2 group-hover:text-amber-600 transition-colors">{{ $stats['completed_today'] ?? 0 }}</h3>
                <p class="text-[13px] text-slate-400 font-bold flex items-center gap-1">
                    <span class="text-slate-400 font-medium ml-1">{{ $stats['avg_prep_time'] ?? 'N/A' }} rata-rata waktu</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-500 delay-100" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 p-6 relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Performance Line Chart</h3>
                        <p class="text-sm text-slate-500 mt-1">Analisa pendapatan berdasarkan periode waktu</p>
                    </div>
                    <div class="flex items-center gap-4 text-sm font-semibold">
                        <span class="flex items-center gap-2 text-slate-700 cursor-pointer"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></span> This week</span>
                        <span class="flex items-center gap-2 text-slate-400 cursor-pointer"><span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span> Last week</span>
                    </div>
                </div>
                <div class="h-[320px] w-full relative">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-blue-600 rounded-2xl shadow-[0_8px_24px_rgba(37,99,235,0.25)] p-6 text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute -right-4 -bottom-4 w-40 h-40 bg-blue-500 rounded-full blur-3xl opacity-60 group-hover:bg-blue-400 transition-colors"></div>
                    <div class="absolute -left-10 -top-10 w-32 h-32 bg-blue-400 rounded-full blur-2xl opacity-40"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-4 tracking-wide text-blue-50">Status Summary</h3>
                        @php
                            $totalToday = $orderStatusDistribution->sum('count');
                            $completedCount = $orderStatusDistribution->firstWhere('status', 'completed')?->count ?? 0;
                            $closedPercent = $totalToday > 0 ? round(($completedCount / $totalToday) * 100, 1) : 0;
                        @endphp
                        <p class="text-blue-200 text-sm mb-1 font-medium">Pesanan Selesai Hari Ini</p>
                        <h2 class="text-5xl font-black mb-6 tracking-tight drop-shadow-sm">{{ $completedCount }}</h2>
                        <div class="h-[100px] -mx-2">
                            <canvas id="miniStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 p-6 flex flex-col justify-center h-[180px]">
                    <div class="flex items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 relative shrink-0">
                                    <canvas id="statusChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        @php $totalStatusCount = $orderStatusDistribution->sum('count'); $completedStatus = $orderStatusDistribution->firstWhere('status', 'completed')?->count ?? 0; $closedStatusPercent = $totalStatusCount > 0 ? round(($completedStatus / $totalStatusCount) * 100, 1) : 0; @endphp
                                        <span class="text-xs font-bold text-slate-700">{{ $closedStatusPercent }}%</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-semibold mb-0.5">Total Pesanan</p>
                                    <h4 class="text-xl font-bold text-slate-800">{{ $totalStatusCount }}</h4>
                                </div>
                            </div>
                            <div class="w-px h-16 bg-slate-100 shrink-0"></div>
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 relative shrink-0">
                                    <canvas id="tableChart"></canvas>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-semibold mb-0.5">Total Meja</p>
                                    @php $totalTables = $tableUtilizationArray['available'] + ($tableUtilizationArray['occupied'] ?? 0) + ($tableUtilizationArray['reserved'] ?? 0); @endphp
                                    <h4 class="text-xl font-bold text-slate-800">{{ $totalTables }}</h4>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-500 delay-200" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Market Overview</h3>
                    <select class="bg-slate-50 border border-slate-200 text-slate-700 rounded-lg text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium cursor-pointer">
                        <option>This month</option>
                        <option>Last month</option>
                    </select>
                </div>
                <div class="h-[280px]">
                    <canvas id="weeklyOrdersChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Menu Terlaris (30 Hari)</h3>
                    <button class="text-amber-600 hover:text-amber-700 text-sm font-semibold">View All</button>
                </div>
                <div class="h-[280px]">
                    <canvas id="topMenuChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-500 delay-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Recent Orders</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Manage and track active orders</p>
                        </div>
                    </div>
                    <a href="{{ role_route('admin.orders.index') }}"
                        class="text-sm bg-white border border-slate-200 shadow-sm px-4 py-2 rounded-lg text-slate-700 hover:text-amber-600 hover:border-amber-300 font-semibold transition-all">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-slate-100" id="recent-orders">
                    @forelse($recentOrders as $order)
                        <div class="px-6 py-4 hover:bg-slate-50/80 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-900 text-white rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform shadow-sm">
                                        <span class="text-base font-black">{{ $order->table->number }}</span>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-800">{{ $order->order_number }}</p>
                                        <p class="text-sm text-slate-500 font-medium mt-0.5">Meja {{ $order->table->number }} ·
                                            <span class="text-slate-400">{{ $order->items->count() }} item</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <span class="text-base font-black text-slate-800 block">{{ $order->formatted_total }}</span>
                                        <span class="text-[11px] uppercase font-bold text-slate-400 mt-0.5 block">{{ $order->payment_method === 'online' ? '💳 Online' : '💵 Kasir' }}</span>
                                    </div>
                                    @php $sc = ['pending' => 'bg-yellow-100 text-yellow-700', 'processing' => 'bg-blue-100 text-blue-700', 'ready' => 'bg-emerald-100 text-emerald-700', 'completed' => 'bg-slate-100 text-slate-600', 'cancelled' => 'bg-red-100 text-red-700']; @endphp
                                    <span class="px-3.5 py-1.5 text-xs font-bold rounded-lg border border-slate-50 shadow-sm {{ $sc[$order->status] ?? '' }} min-w-[100px] text-center">{{ $order->status_label }}</span>
                                    <button type="button" onclick='openOrderDetail("{{ $order->id }}", "{{ role_route("admin.orders.data", $order) }}")' class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold hover:bg-slate-200 transition-all uppercase tracking-wider">Detail</button>
                                </div>
                            </div>

                            @if(!in_array($order->status, ['completed', 'cancelled', 'pending_payment']))
                                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if(in_array($order->status, ['pending', 'payment_success', 'processing']))
                                        <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block" onsubmit="return confirmStatusUpdate(event, 'Tolak pesanan ini?', 'warning')">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="px-4 py-2 text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors text-sm font-bold rounded-lg">Tolak</button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['pending', 'payment_success']))
                                        <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block" onsubmit="return confirmStatusUpdate(event, 'Terima pesanan ini dan mulai masak?')">
                                            @csrf
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="px-5 py-2 bg-amber-500 text-white hover:bg-amber-600 transition-colors text-sm font-bold rounded-lg shadow-sm shadow-amber-500/20">🍳 Terima & Masak</button>
                                        </form>
                                    @elseif($order->status === 'processing')
                                        <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block" onsubmit="return confirmStatusUpdate(event, 'Pesanan sudah siap saji?')">
                                            @csrf
                                            <input type="hidden" name="status" value="ready">
                                            <button type="submit" class="px-5 py-2 bg-emerald-500 text-white hover:bg-emerald-600 transition-colors text-sm font-bold rounded-lg shadow-sm shadow-emerald-500/20">✅ Makanan Siap</button>
                                        </form>
                                    @elseif($order->status === 'ready')
                                        <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block" onsubmit="return confirmStatusUpdate(event, 'Selesaikan pesanan ini?')">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-5 py-2 bg-slate-800 text-white hover:bg-slate-900 transition-colors text-sm font-bold rounded-lg shadow-sm shadow-slate-800/20">🏁 Selesaikan</button>
                                        </form>
                                    @endif
                                </div>
                            @elseif($order->status === 'pending_payment')
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="flex items-center gap-2 text-sm text-amber-600 bg-amber-50 border border-amber-100 px-4 py-2.5 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="font-bold">Menunggu customer menyelesaikan pembayaran online</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                     @empty
                         <div class="px-6 py-16 text-center">
                             <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                             </div>
                             <p class="text-slate-500 font-medium">Belum ada pesanan hari ini</p>
                         </div>
                     @endforelse
                </div>
                
                @if($recentOrders->hasPages())
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500">
                            Menampilkan <span class="text-slate-800">{{ $recentOrders->firstItem() }}-{{ $recentOrders->lastItem() }}</span> dari <span class="text-slate-800">{{ $recentOrders->total() }}</span> pesanan
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if ($recentOrders->onFirstPage())
                                <span class="p-2 bg-slate-100 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                            @else
                                <a href="{{ $recentOrders->previousPageUrl() }}" class="p-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-all shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                            @endif
                            
                            <span class="px-4 py-1.5 text-sm font-black text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm">
                                {{ $recentOrders->currentPage() }} <span class="text-slate-400 font-medium mx-1">/</span> {{ $recentOrders->lastPage() }}
                            </span>
                            
                            @if ($recentOrders->hasMorePages())
                                <a href="{{ $recentOrders->nextPageUrl() }}" class="p-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-all shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                            @else
                                <span class="p-2 bg-slate-100 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Reservasi Hari Ini</h3>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $todayReservations->count() }} reservasi</p>
                        </div>
                    </div>
                    <a href="{{ role_route('admin.reservations.index') }}" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-slate-500 hover:text-amber-600 hover:border-amber-300 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($todayReservations as $res)
                        <div class="px-6 py-4 hover:bg-slate-50 transition-colors flex items-center gap-4 group cursor-pointer">
                            <input type="checkbox" class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer transition-colors" {{ $res->status === 'confirmed' ? 'checked' : '' }}>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-base font-bold text-slate-800 group-hover:text-amber-600 transition-colors">{{ $res->name }}</p>
                                    @php $rs = ['pending' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700']; @endphp
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider {{ $rs[$res->status] ?? '' }}">{{ $res->status_label }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm font-medium text-slate-500">
                                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $res->formatted_time }}</span>
                                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> {{ $res->guests }} tamu</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-slate-400 text-sm font-medium">Belum ada reservasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="tab === 'audiences'" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center" style="display: none;">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Laporan Penjualan (Audiences)</h3>
            <p class="text-slate-500">Fitur laporan detail sedang dikembangkan.</p>
        </div>
        <div x-show="tab === 'demographics'" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center" style="display: none;">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Operasional & Demografik</h3>
            <p class="text-slate-500">Analisa demografik operasional tersedia segera.</p>
        </div>
        <div x-show="tab === 'more'" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center" style="display: none;">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Lebih Banyak Fitur</h3>
            <p class="text-slate-500">Konfigurasi lanjutan dan integrasi lainnya.</p>
        </div>

    </div>

    @push('scripts')
        <script>
        // ─── Alpine.js: Dashboard Manager ───────────────────────────────────
        function dashboardManager() {
            return {
                statsFilters: {
                    menu: { search: '', category: '', status: 'all' },
                    tables: { search: '', location: '', status: 'all' },
                    orders: { search: '', status: 'all', dateRange: 'today' },
                    reservations: { search: '', status: 'all', dateRange: 'upcoming' }
                },
                init() {
                    this.$watch('statsFilters', () => {
                        this.applyFilters();
                    }, { deep: true });
                    
                    if(typeof flatpickr !== 'undefined') {
                        flatpickr('.flatpickr', {
                            dateFormat: 'd/m/Y',
                            defaultDate: 'today'
                        });
                    }
                },
                applyFilters() {
                    const filters = this.statsFilters.orders || {};
                    const dateRange = filters.dateRange || 'today';
                    const params = new URLSearchParams({ date_range: dateRange });
                    const url = `{{ route('admin.dashboard') }}?${params.toString()}`;
                    
                    window.location.href = url;
                },
                resetFilters() {
                    this.statsFilters = {
                        menu: { search: '', category: '', status: 'all' },
                        tables: { search: '', location: '', status: 'all' },
                        orders: { search: '', status: 'all', dateRange: 'today' },
                        reservations: { search: '', status: 'all', dateRange: 'upcoming' }
                    };
                }
            };
        }

        (function() {
            function showToast(message, type = 'info') {
                const icons = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icons[type] || 'info',
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            window.confirmStatusUpdate = function(event, message, icon = 'question') {
                event.preventDefault();
                const form = event.target;
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#0f172a',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'rounded-2xl' }
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(async data => {
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Status berhasil diperbarui!',
                                timer: 1800,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-2xl' }
                            });
                            window.location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal memperbarui status',
                                customClass: { popup: 'rounded-2xl' }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi Bermasalah',
                            text: 'Terjadi kesalahan saat memperbarui status',
                            customClass: { popup: 'rounded-2xl' }
                        });
                    });
                });
                return false;
            };

            const ORDER_STATUS_LABELS = {
                pending_payment: 'Menunggu Pembayaran', payment_success: 'Pembayaran Berhasil',
                pending: 'Menunggu', processing: 'Diproses', ready: 'Siap Disajikan',
                completed: 'Selesai', cancelled: 'Dibatalkan'
            };
            const STATUS_COLOR_MAP = {
                pending: '#eab308', processing: '#3b82f6', ready: '#10b981',
                completed: '#6b7280', cancelled: '#ef4444',
                pending_payment: '#f59e0b', payment_success: '#10b981'
            };

            document.addEventListener('DOMContentLoaded', function () {
                
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#94a3b8';

                const revenueTrendData = @json($revenueTrend);
                const lastWeekData = @json($lastWeekRevenueData);
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    const ctx = revenueCtx.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
                    gradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');
                    
                    const gradientBlue = ctx.createLinearGradient(0, 0, 0, 300);
                    gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                    gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                    const labels30Days = revenueTrendData.map(d => new Date(d.date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
                    const thisWeekRevenue = revenueTrendData.map(d => d.revenue);
                    const lastWeekRevenue = labels30Days.map((label, idx) => {
                        const dateKey = new Date(revenueTrendData[idx].date);
                        const prevDate = new Date(dateKey);
                        prevDate.setDate(prevDate.getDate() - 7);
                        const prevKey = prevDate.toISOString().split('T')[0];
                        const found = lastWeekData[prevKey];
                        return found ? found.revenue : null;
                    });

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels30Days,
                            datasets: [
                                {
                                    label: 'Minggu Ini',
                                    data: thisWeekRevenue,
                                    borderColor: '#f59e0b',
                                    backgroundColor: gradient,
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#f59e0b',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: 'Minggu Lalu',
                                    data: lastWeekRevenue,
                                    borderColor: '#cbd5e1',
                                    backgroundColor: gradientBlue,
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 0
                                }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 12,
                                    titleFont: { size: 13 },
                                    bodyFont: { size: 14, weight: 'bold' },
                                    callbacks: { label: (ctx) => 'Rp ' + (ctx.raw ? ctx.raw.toLocaleString('id-ID') : '0') }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9', drawBorder: false },
                                    ticks: { callback: v => (v/1000) + 'k', font: { size: 11, weight: '600' } }
                                },
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { font: { size: 11, weight: '600' } }
                                }
                            },
                            interaction: { intersect: false, mode: 'index' }
                        }
                    });
                }

                const miniStatusCtx = document.getElementById('miniStatusChart');
                const miniChartData = @json($miniChartData);
                if(miniStatusCtx && miniChartData.length > 0) {
                    const hourLabels = miniChartData.map((_, i) => i + 'h');
                    const maxVal = Math.max(...miniChartData);
                    const normalizedData = miniChartData.map(v => maxVal > 0 ? (v / maxVal) * 30 : 0);
                    new Chart(miniStatusCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: hourLabels,
                            datasets: [{
                                data: normalizedData,
                                borderColor: 'rgba(255, 255, 255, 0.8)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointBackgroundColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { enabled: false } },
                            scales: { 
                                x: { display: false }, 
                                y: { display: false, min: 0 } 
                            },
                            layout: { padding: 0 }
                        }
                    });
                }

                const statusData = @json($orderStatusDistribution);
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    new Chart(statusCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: statusData.map(s => ORDER_STATUS_LABELS[s.status] ?? s.status),
                            datasets: [{
                                data: statusData.length ? statusData.map(s => s.count) : [1],
                                backgroundColor: statusData.length ? statusData.map(s => STATUS_COLOR_MAP[s.status] ?? '#94a3b8') : ['#e2e8f0'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: { legend: { display: false }, tooltip: { enabled: statusData.length > 0 } }
                        }
                    });
                }

                const tableData = @json($tableUtilization);
                const tableColors = { available: '#10b981', occupied: '#ef4444', reserved: '#f59e0b' };
                const tableCtx = document.getElementById('tableChart');
                if (tableCtx) {
                    new Chart(tableCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: tableData.map(t => t.status),
                            datasets: [{
                                data: tableData.length ? tableData.map(t => t.count) : [1],
                                backgroundColor: tableData.length ? tableData.map(t => tableColors[t.status] ?? '#94a3b8') : ['#e2e8f0'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: { legend: { display: false }, tooltip: { enabled: tableData.length > 0 } }
                        }
                    });
                }

                const weeklyData = @json($weeklyRevenue);
                const weeklyOrdersCtx = document.getElementById('weeklyOrdersChart');
                if (weeklyOrdersCtx) {
                    new Chart(weeklyOrdersCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: weeklyData.map(d => new Date(d.date).toLocaleDateString('id-ID', { weekday: 'short' })),
                            datasets: [{
                                label: 'Orders',
                                data: weeklyData.map(d => d.count),
                                backgroundColor: '#f59e0b', 
                                borderRadius: 4,
                                barPercentage: 0.5
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false, drawBorder: false }, ticks: { font: { weight: '600' } } },
                                y: { grid: { color: '#f1f5f9', drawBorder: false }, beginAtZero: true }
                            }
                        }
                    });
                }

                const topMenuData = @json($topMenuItems);
                const topMenuCtx = document.getElementById('topMenuChart');
                if (topMenuCtx) {
                    new Chart(topMenuCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: topMenuData.map(m => m.name),
                            datasets: [{
                                label: 'Terjual',
                                data: topMenuData.map(m => m.total_sold),
                                backgroundColor: '#3b82f6', 
                                borderRadius: 4,
                                barPercentage: 0.6
                            }]
                        },
                        options: {
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { display: false, beginAtZero: true },
                                y: { grid: { display: false, drawBorder: false }, ticks: { font: { weight: '600' } } }
                            }
                        }
                    });
                }

                if (window.Echo) {
                    const onDashboardRefresh = (msg) => {
                        if (msg) showToast(msg, 'info');
                        window.location.reload();
                    };
                    window.Echo.channel('admin-dashboard').listen('.DashboardUpdated', () => onDashboardRefresh('Data dashboard diperbarui'));
                    window.Echo.channel('admin-orders').listen('.OrderUpdated', (e) => {
                        const text = e.action === 'created' ? 'Pesanan baru: ' + (e.order?.order_number || '') : 'Pesanan diperbarui';
                        onDashboardRefresh(text);
                    });
                }
            });
        })();
        </script>
    @endpush
@endsection
