@extends('layouts.admin')
@section('title', 'Arsip & Riwayat')
@section('subtitle', 'Pusat data transaksi dan performa operasional')

@section('content')
<div class="space-y-8">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-20 h-20 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2v-4h2v4zm0-6h-2V7h2v4z"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Transaksi</p>
            <p class="text-3xl font-black text-slate-900 leading-none">{{ $orders->total() }} <span class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Order</span></p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-20 h-20 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v4z"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Pendapatan</p>
            <p class="text-3xl font-black text-amber-600 leading-none">Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Filter Aktif</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @if(request('date'))
                <span class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black rounded-full uppercase tracking-widest">{{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}</span>
                @else
                <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[9px] font-black rounded-full uppercase tracking-widest">Semua Waktu</span>
                @endif
                @if(request('search'))
                <span class="px-3 py-1 bg-amber-500 text-white text-[9px] font-black rounded-full uppercase tracking-widest">"{{ request('search') }}"</span>
                @endif
            </div>
        </div>
    </div>

    <form method="GET" action="{{ role_route('admin.orders.history') }}" class="admin-live-search bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between" data-live-search="history" data-live-target="#orders-history-list">
        <div class="relative w-full md:w-[450px] group">
            <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Cari No. Order, Nama Pelanggan, atau Nomor Meja..." class="w-full px-4 pr-12 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all shadow-inner outline-none" />
        </div>

        <div class="flex gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-60 group">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <input type="text" name="date" id="date-picker" value="{{ request('date') }}" placeholder="Pilih Tanggal" class="w-full pl-12 pr-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all shadow-inner outline-none cursor-pointer">
            </div>
            
            <button type="submit" class="px-8 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 active:scale-95">Filter Data</button>
            
            @if(request()->anyFilled(['search', 'date']))
                <a href="{{ role_route('admin.orders.history') }}" class="w-12 h-12 flex items-center justify-center bg-slate-100 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all shadow-sm group" title="Reset Filter">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>

    <div id="orders-history-list" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Timestamp</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Order Detail</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Unit Meja</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nominal Akhir</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Metode</th>
                        <th class="pl-6 pr-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-all duration-300 group">
                        <td class="pl-10 pr-6 py-6">
                            <p class="text-xs font-black text-slate-800">{{ $order->created_at->format('d M, Y') }}</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $order->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 group-hover:text-amber-600 transition-colors">#{{ $order->order_number }}</span>
                                <span class="text-[11px] font-bold text-slate-500 mt-0.5">{{ $order->customer_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl text-xs font-black text-slate-800 shadow-sm group-hover:shadow-md group-hover:-translate-y-0.5 transition-all">
                                {{ $order->table->number }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-sm font-black text-amber-600">{{ $order->formatted_total }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5 tracking-tighter">{{ $order->items->count() }} Item Terjual</p>
                        </td>
                        <td class="px-6 py-6">
                            @php $pm = $order->payment_method === 'online' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black border uppercase tracking-widest {{ $pm }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_method === 'online' ? 'bg-blue-500' : 'bg-emerald-500' }}"></span>
                                {{ $order->payment_method }}
                            </span>
                        </td>
                        <td class="pl-6 pr-10 py-6 text-right">
                            <button type="button" onclick="openOrderDetail('{{ $order->id }}', '{{ role_route('admin.orders.data', $order) }}')" class="inline-flex items-center justify-center w-10 h-10 bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white rounded-xl transition-all group/btn shadow-inner">
                                <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-24 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 class="text-base font-black text-slate-800">Riwayat Masih Kosong</h4>
                            <p class="text-xs text-slate-400 mt-2">Belum ada transaksi yang tercatat untuk periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    Menampilkan <span class="font-bold text-slate-800">{{ $orders->firstItem() }}</span> 
                    sampai <span class="font-bold text-slate-800">{{ $orders->lastItem() }}</span> 
                    dari <span class="font-bold text-slate-800">{{ $orders->total() }}</span> transaksi
                </div>
                <div class="flex items-center gap-2">
                    @if ($orders->onFirstPage())
                        <span class="px-4 py-2 bg-slate-200 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">
                            ← Sebelumnya
                        </span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                            ← Sebelumnya
                        </a>
                    @endif

                    <div class="hidden sm:flex items-center gap-1">
                        @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                            @if ($page == $orders->currentPage())
                                <span class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold shadow-lg shadow-slate-900/20">
                                    {{ $page }}
                                </span>
                            @elseif ($page == 1 || $page == $orders->lastPage() || abs($page - $orders->currentPage()) <= 2)
                                <a href="{{ $url }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                                    {{ $page }}
                                </a>
                            @elseif (abs($page - $orders->currentPage()) == 3)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                        @endforeach
                    </div>

                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                            Selanjutnya →
                        </a>
                    @else
                        <span class="px-4 py-2 bg-slate-200 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">
                            Selanjutnya →
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    flatpickr("#date-picker", {
        locale: "id",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: true,
        disableMobile: "true"
    });
</script>
@endpush
@endsection
