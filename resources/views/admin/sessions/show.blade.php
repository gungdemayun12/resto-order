@extends('layouts.admin')
@section('title', 'Detail Sesi Meja #' . $session->table->number)
@section('subtitle', 'Detail aktivitas dan pesanan pada sesi ini')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="{{ role_route('admin.sessions.index') }}" class="p-2 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-slate-800 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-800">Sesi Meja {{ $session->table->number }}</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $session->session_token }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 space-y-6">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Status Sesi</h3>
                <div class="flex items-center gap-3">
                    @php $statusColors = ['active' => 'bg-emerald-100 text-emerald-700', 'closed' => 'bg-slate-100 text-slate-700', 'expired' => 'bg-amber-100 text-amber-700']; @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$session->status] ?? '' }}">
                        {{ strtoupper($session->status) }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">Mulai: {{ $session->started_at->format('H:i') }}</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Data Pelanggan</h3>
                @php $firstOrder = $session->orders->first(); @endphp
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Nama</span>
                        <span class="font-bold text-slate-800">{{ $firstOrder->customer_name ?? 'Guest' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">HP</span>
                        <span class="font-semibold text-slate-700">{{ $firstOrder->customer_phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Waktu Sesi</h3>
                <div class="space-y-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Durasi Sesi</span>
                        <span class="font-bold text-slate-800">3 Jam</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Sisa Waktu</span>
                        <span class="font-bold {{ $session->isActive() ? 'text-amber-600' : 'text-slate-400' }}">
                            {{ $session->isActive() ? now()->diffInMinutes($session->expires_at) . ' Menit' : 'Selesai' }}
                        </span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        @php 
                            $total = $session->started_at->diffInMinutes($session->expires_at);
                            $passed = $session->started_at->diffInMinutes(now());
                            $percent = min(100, max(0, ($passed / $total) * 100));
                        @endphp
                        <div class="h-full bg-slate-900" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Riwayat Pesanan Sesi Ini</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($session->orders as $order)
                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-bold text-slate-600">
                                    #{{ $loop->iteration }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $order->order_number }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $order->created_at->format('H:i') }} · {{ $order->payment_method }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-800">{{ $order->formatted_total }}</p>
                                @php $sc = ['pending' => 'text-yellow-600', 'processing' => 'text-blue-600', 'ready' => 'text-emerald-600', 'completed' => 'text-slate-500']; @endphp
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $sc[$order->status] ?? '' }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 rounded-2xl p-4 space-y-2">
                            @foreach($order->items as $item)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-600 font-medium">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                <span class="font-bold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <p class="text-slate-400 text-sm font-medium">Belum ada pesanan pada sesi ini</p>
                    </div>
                    @endforelse
                </div>
                @if($session->orders->isNotEmpty())
                <div class="bg-slate-900 p-6 flex justify-between items-center text-white">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Sesi Ini</p>
                        <p class="text-xs text-slate-500">Akumulasi semua pesanan</p>
                    </div>
                    <p class="text-2xl font-black text-amber-500">
                        Rp {{ number_format($session->orders->sum('total_amount'), 0, ',', '.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
