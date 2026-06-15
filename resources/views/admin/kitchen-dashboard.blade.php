@extends('layouts.admin')
@section('title', 'Dapur Dashboard')
@section('greeting')
    @php
        $hour = now()->format('H');
        $greeting = 'Siap Masak,';
        if ($hour >= 12 && $hour < 15) $greeting = 'Semangat Siang,';
        elseif ($hour >= 15 && $hour < 18) $greeting = 'Tetap Semangat,';
        elseif ($hour >= 18) $greeting = 'Selamat Malam,';
    @endphp
    {{ $greeting }}
@endsection
@section('subtitle', 'Monitor antrian masak & status pesanan real-time')

@section('content')
<div x-data="kitchenApp()" class="space-y-6 pb-10">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-amber-500 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-amber-500/30">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-400/40 rounded-full"></div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-amber-100 mb-1">Antrian Masuk</p>
            <p class="text-4xl font-black leading-none" id="stat-queue">{{ $queueCount }}</p>
            <p class="text-xs text-amber-100 mt-2 font-medium">pesanan menunggu</p>
            <div class="absolute top-4 right-4 w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-blue-600 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-blue-600/30">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/40 rounded-full"></div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-blue-100 mb-1">Sedang Dimasak</p>
            <p class="text-4xl font-black leading-none" id="stat-cooking">{{ $cookingOrders->count() }}</p>
            <p class="text-xs text-blue-100 mt-2 font-medium">di dapur sekarang</p>
            <div class="absolute top-4 right-4 w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
            </div>
        </div>

        <div class="bg-emerald-500 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-emerald-500/30">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-400/40 rounded-full"></div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-100 mb-1">Siap Diantar</p>
            <p class="text-4xl font-black leading-none" id="stat-ready">{{ $readyOrders->count() }}</p>
            <p class="text-xs text-emerald-100 mt-2 font-medium">menunggu diantar</p>
            <div class="absolute top-4 right-4 w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <div class="bg-slate-800 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-slate-800/30">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-slate-700/40 rounded-full"></div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Selesai Hari Ini</p>
            <p class="text-4xl font-black leading-none text-white" id="stat-done">{{ $completedToday }}</p>
            <p class="text-xs text-slate-400 mt-2 font-medium">{{ $totalItemsToday }} item total</p>
            <div class="absolute top-4 right-4 w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
        </div>
    </div>

    <div id="realtime-status" class="hidden items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-bold w-fit">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        Real-time aktif — pesanan baru masuk otomatis
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-blue-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Sedang Dimasak</h3>
                        <p class="text-xs text-slate-500">Pesanan dalam proses dapur</p>
                    </div>
                </div>
                <a href="{{ role_route('admin.orders.index', ['status' => 'processing']) }}" class="text-xs text-blue-600 font-bold hover:underline">Lihat semua →</a>
            </div>
            <div class="divide-y divide-slate-50" id="cooking-list">
                @forelse($cookingOrders as $order)
                    @include('admin._kitchen_order_card', ['order' => $order, 'action' => 'ready', 'actionLabel' => '✅ Siap Diantar', 'actionColor' => 'bg-emerald-500 hover:bg-emerald-600'])
                @empty
                    <div class="py-14 text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <p class="text-slate-400 text-sm font-medium">Tidak ada pesanan yang sedang dimasak</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-emerald-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Siap Diantar</h3>
                        <p class="text-xs text-slate-500">Menunggu dibawa ke meja</p>
                    </div>
                </div>
                <a href="{{ role_route('admin.orders.index', ['status' => 'ready']) }}" class="text-xs text-emerald-600 font-bold hover:underline">Lihat semua →</a>
            </div>
            <div class="divide-y divide-slate-50" id="ready-list">
                @forelse($readyOrders as $order)
                    @include('admin._kitchen_order_card', ['order' => $order, 'action' => 'completed', 'actionLabel' => '🏁 Selesaikan', 'actionColor' => 'bg-slate-800 hover:bg-slate-900'])
                @empty
                    <div class="py-14 text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138z"/></svg>
                        </div>
                        <p class="text-slate-400 text-sm font-medium">Tidak ada pesanan siap diantar</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-black text-slate-800 mb-4">Menu Paling Banyak Hari Ini</h3>
            <div class="space-y-3">
                @forelse($topMenusToday as $i => $menu)
                    @php $pct = $topMenusToday->first()?->total_qty > 0 ? round(($menu->total_qty / $topMenusToday->first()->total_qty) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-700 truncate max-w-[160px]">{{ $menu->menuItem?->name ?? '—' }}</span>
                            <span class="text-xs font-black text-amber-600">{{ $menu->total_qty }}x</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada data hari ini</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-800">Tren Pesanan Masuk Hari Ini</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Jumlah pesanan per jam</p>
                </div>
                @php $avgTime = $avgCookTime ? round($avgCookTime) . ' menit' : 'N/A'; @endphp
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-medium">Rata-rata masak</p>
                    <p class="text-lg font-black text-amber-600">{{ $avgTime }}</p>
                </div>
            </div>
            <div class="h-48"><canvas id="hourlyChart"></canvas></div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    const hourlyData = @json($hourlyTrend);
    const ctx = document.getElementById('hourlyChart');
    if (ctx && hourlyData.length) {
        // Buat array 24 jam, isi dengan data
        const hours = Array.from({length: 24}, (_, i) => i);
        const counts = hours.map(h => {
            const found = hourlyData.find(d => d.hour == h);
            return found ? found.count : 0;
        });

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hours.map(h => h + ':00'),
                datasets: [{
                    label: 'Pesanan',
                    data: counts,
                    backgroundColor: counts.map(c => c > 0 ? '#f59e0b' : '#f1f5f9'),
                    borderRadius: 6,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' }, maxRotation: 0 } },
                    y: { beginAtZero: true, grid: { color: '#f8fafc' }, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Realtime via Echo
    const statusEl = document.getElementById('realtime-status');
    if (window.Echo) {
        statusEl.classList.remove('hidden');
        statusEl.classList.add('flex');
        window.Echo.channel('admin-orders').listen('.OrderUpdated', () => {
            // Refresh halaman untuk update antrian
            setTimeout(() => window.location.reload(), 800);
        });
    } else {
        // Polling fallback tiap 10 detik
        setInterval(() => window.location.reload(), 10000);
    }
});

function kitchenApp() {
    return {};
}
</script>
@endpush
@endsection
