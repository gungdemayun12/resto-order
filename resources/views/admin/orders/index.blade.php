@extends('layouts.admin')
@section('title', 'Pesanan Masuk')
@section('subtitle', 'Kelola pesanan pelanggan — pembaruan otomatis real-time')

@section('content')
    <div class="space-y-6">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $scColors = ['pending' => 'bg-yellow-50 border-yellow-200 text-yellow-700', 'processing' => 'bg-blue-50 border-blue-200 text-blue-700', 'ready' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'completed' => 'bg-slate-50 border-slate-200 text-slate-600']; @endphp
            <a href="{{ role_route('admin.orders.index', ['status' => 'pending', 'date' => request('date')]) }}" class="{{ $scColors['pending'] }} border rounded-2xl p-4 hover:shadow-md transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Menunggu</p>
                    <span class="text-lg">⏳</span>
                </div>
                <p class="text-3xl font-black mt-1 group-hover:scale-105 transition-transform origin-left">{{ $statusCounts['pending'] ?? 0 }}</p>
            </a>
            <a href="{{ role_route('admin.orders.index', ['status' => 'processing', 'date' => request('date')]) }}" class="{{ $scColors['processing'] }} border rounded-2xl p-4 hover:shadow-md transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Diproses</p>
                    <span class="text-lg">🍳</span>
                </div>
                <p class="text-3xl font-black mt-1 group-hover:scale-105 transition-transform origin-left">{{ $statusCounts['processing'] ?? 0 }}</p>
            </a>
            <a href="{{ role_route('admin.orders.index', ['status' => 'ready', 'date' => request('date')]) }}" class="{{ $scColors['ready'] }} border rounded-2xl p-4 hover:shadow-md transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Siap Saji</p>
                    <span class="text-lg">✅</span>
                </div>
                <p class="text-3xl font-black mt-1 group-hover:scale-105 transition-transform origin-left">{{ $statusCounts['ready'] ?? 0 }}</p>
            </a>
            <a href="{{ role_route('admin.orders.index', ['status' => 'completed', 'date' => request('date')]) }}" class="{{ $scColors['completed'] }} border rounded-2xl p-4 hover:shadow-md transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Selesai</p>
                    <span class="text-lg">🏁</span>
                </div>
                <p class="text-3xl font-black mt-1 group-hover:scale-105 transition-transform origin-left">{{ $statusCounts['completed'] ?? 0 }}</p>
            </a>
        </div>

        <div id="realtime-status"
            class="hidden items-center gap-2 px-5 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-emerald-700 font-bold shadow-sm">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            Real-time aktif — pesanan masuk otomatis tanpa refresh
        </div>

        <form method="GET" action="{{ role_route('admin.orders.index') }}"
            class="admin-live-search bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between"
            data-live-search="orders"
            data-feed-url="{{ role_route('admin.orders.feed') }}"
            data-live-target="#orders-list">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="relative w-full md:w-1/2 lg:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari No. Order atau Nama..."
                    autocomplete="off"
                    class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all shadow-inner outline-none">
            </div>
            <div class="flex flex-wrap md:flex-nowrap gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-48">
                    <input type="text" name="date" id="date-picker" value="{{ request('date', now()->format('Y-m-d')) }}"
                        placeholder="Pilih Tanggal"
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all shadow-inner outline-none cursor-pointer">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                </div>
                <button type="submit"
                    class="px-6 py-3.5 bg-slate-900 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 active:scale-95 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'date']))
                    <a href="{{ role_route('admin.orders.index') }}"
                        class="px-5 py-3.5 bg-slate-100 text-slate-400 hover:text-red-500 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-red-50 transition-all flex items-center gap-2 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div id="orders-status-tabs" class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-2">
            @php $tabs = ['all' => 'Semua', 'pending' => 'Menunggu', 'payment_success' => 'Pembayaran Berhasil', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan']; @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ role_route('admin.orders.index', ['status' => $key, 'date' => request('date'), 'search' => request('search')]) }}"
                    class="px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ (request('status', 'all') == $key) ? 'bg-amber-500 text-white shadow-xl shadow-amber-500/20 ring-4 ring-amber-500/10' : 'bg-white text-slate-500 border border-slate-200 hover:border-amber-300 hover:text-amber-600 shadow-sm' }}">
                    {{ $label }}
                    @if(isset($statusCounts[$key]) && $statusCounts[$key] > 0)
                        <span data-status-count="{{ $key }}"
                            class="ml-2 px-2 py-0.5 text-[9px] rounded-full {{ (request('status', 'all') == $key) ? 'bg-white/20' : 'bg-slate-100 text-slate-400' }}">{{ $statusCounts[$key] }}</span>
                    @else
                        <span data-status-count="{{ $key }}"
                            class="ml-2 px-2 py-0.5 text-[9px] rounded-full hidden {{ (request('status', 'all') == $key) ? 'bg-white/20' : 'bg-slate-100 text-slate-400' }}">0</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div id="orders-list" class="grid gap-6">
            @include('admin.orders._cards', ['orders' => $orders])
        </div>

        @if($orders->hasPages())
            <div id="orders-pagination" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mt-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm font-medium text-slate-500">
                        Menampilkan <span class="font-black text-slate-800">{{ $orders->firstItem() }}</span>
                        sampai <span class="font-black text-slate-800">{{ $orders->lastItem() }}</span>
                        dari <span id="orders-total-count" class="font-black text-slate-800">{{ $orders->total() }}</span>
                        pesanan
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest">
                            ← Geser untuk navigasi →
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            flatpickr("#date-picker", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                allowInput: true,
                disableMobile: "true"
            });

            (function () {
                const feedUrl = @json(role_route('admin.orders.feed'));
                const feedParams = new URLSearchParams(window.location.search);
                let lastPendingCount = {{ $statusCounts['pending'] ?? 0 }};
                const notifySound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

                function playNewOrderSound() {
                    notifySound.play().catch(() => { });
                }

                function updateStatusTabCounts(counts) {
                    if (!counts) return;
                    Object.keys(counts).forEach(function (key) {
                        const el = document.querySelector('[data-status-count="' + key + '"]');
                        if (!el) return;
                        const n = counts[key];
                        el.textContent = n;
                        if (n > 0) {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    });
                }

                function updateSidebarBadge(pendingCount) {
                    const badge = document.getElementById('order-badge');
                    if (!badge) return;
                    if (pendingCount > 0) {
                        badge.textContent = pendingCount > 99 ? '99+' : pendingCount;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                window.refreshOrdersList = function (showNotification) {
                    if (showNotification === undefined) showNotification = true;

                    const url = feedUrl + (feedParams.toString() ? '?' + feedParams.toString() : '');

                    return fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            const list = document.getElementById('orders-list');
                            if (list && data.html) {
                                list.innerHTML = data.html;
                            }
                            updateStatusTabCounts(data.status_counts);
                            updateSidebarBadge(data.pending_count);

                            const totalEl = document.getElementById('orders-total-count');
                            if (totalEl && data.total !== undefined) {
                                totalEl.textContent = data.total;
                            }

                            if (showNotification && data.pending_count > lastPendingCount) {
                                playNewOrderSound();
                                if (typeof showToast === 'function') {
                                    showToast('Pesanan baru masuk!', 'info');
                                }
                                Swal.fire({
                                    title: 'Pesanan Baru!',
                                    text: 'Daftar pesanan telah diperbarui otomatis.',
                                    icon: 'info',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                });
                            }
                            lastPendingCount = data.pending_count;
                        })
                        .catch(function (e) {
                            console.error('Gagal memuat daftar pesanan:', e);
                        });
                };

                function onOrderBroadcast(payload) {
                    if (payload && payload.pending_count !== undefined) {
                        updateSidebarBadge(payload.pending_count);
                    }
                    window.refreshOrdersList(payload && payload.action === 'created');
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const statusEl = document.getElementById('realtime-status');

                    window.addEventListener('order-updated', function (ev) {
                        onOrderBroadcast(ev.detail);
                    });

                    if (window.Echo) {
                        if (statusEl) {
                            statusEl.classList.remove('hidden');
                            statusEl.classList.add('flex');
                        }
                        console.log('✅ Real-time pesanan: terhubung ke Reverb');
                    } else {
                        console.warn('Echo tidak tersedia — fallback polling 5 detik.');
                        if (statusEl) {
                            statusEl.classList.remove('hidden');
                            statusEl.classList.add('flex');
                            statusEl.classList.remove('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                            statusEl.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-700');
                            statusEl.innerHTML = '⚠️ WebSocket tidak aktif — polling aktif (jalankan: php artisan reverb:start)';
                        }
                        setInterval(function () { window.refreshOrdersList(false); }, 5000);
                    }

                    updateSidebarBadge(lastPendingCount);
                });
            })();
        </script>
    @endpush
@endsection