@extends('layouts.admin')
@section('title', 'Detail Pesanan #' . $order->order_number)
@section('subtitle', 'Informasi lengkap rincian pesanan pelanggan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="{{ url()->previous() }}" class="p-2 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-slate-800 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-black text-slate-800">Pesanan {{ $order->order_number }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Daftar Menu yang Dipesan</h3>
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $order->items->count() }} Items</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                    <div class="p-8 flex items-center gap-6 group hover:bg-slate-50/50 transition-all">
                        <div class="w-20 h-20 bg-slate-100 rounded-2xl overflow-hidden shadow-inner flex-shrink-0">
                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <h4 class="font-black text-slate-800 group-hover:text-amber-600 transition-colors">{{ $item->menuItem->name }}</h4>
                                <span class="text-sm font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-slate-400 mb-2">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            @if($item->notes)
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold">
                                📝 {{ $item->notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="p-8 bg-slate-900 text-white rounded-b-2xl">
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Subtotal</span>
                            <span class="font-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400">Pajak (11%)</span>
                            <span class="font-bold">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-white/10 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Pembayaran</p>
                            <p class="text-xs text-slate-400">Lunas via {{ strtoupper($order->payment_method) }}</p>
                        </div>
                        <p class="text-3xl font-black text-amber-500">{{ $order->formatted_total }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Informasi Pelanggan</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center font-black text-xl">
                            {{ substr($order->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</p>
                            <p class="text-xs text-slate-500">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-50 space-y-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Nomor Meja</span>
                            <span class="font-black text-slate-800">MEJA {{ $order->table->number }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Waktu Order</span>
                            <span class="font-bold text-slate-800">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Metode Bayar</span>
                            <span class="inline-flex px-2 py-0.5 bg-slate-100 rounded-full font-bold text-[10px]">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Status Saat Ini</h3>
                @php $sc = ['pending_confirmation'=>'bg-orange-100 text-orange-700', 'pending'=>'bg-yellow-100 text-yellow-700', 'payment_success'=>'bg-emerald-100 text-emerald-700', 'processing'=>'bg-blue-100 text-blue-700', 'ready'=>'bg-emerald-100 text-emerald-700', 'completed'=>'bg-slate-100 text-slate-600', 'cancelled'=>'bg-red-100 text-red-700']; @endphp
                <div class="inline-block px-6 py-3 {{ $sc[$order->status] ?? '' }} rounded-2xl font-black text-sm uppercase tracking-widest mb-6 shadow-sm">
                    {{ $order->status_label }}
                </div>
                
                @if($order->status === 'pending_confirmation')
                    <div class="pt-6 border-t border-slate-50">
                        <p class="text-xs font-bold text-slate-600 mb-4">Scan QR Code untuk Konfirmasi</p>
                        <div class="flex justify-center mb-4">
                            {!! QrCode::size(150)->generate($order->order_number) !!}
                        </div>
                        <p class="text-[10px] text-slate-400 mb-4">{{ $order->order_number }}</p>
                        <a href="{{ role_route('admin.orders.scan') }}" class="inline-block w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-colors">
                            📷 Buka Scanner
                        </a>
                    </div>
                @elseif($order->status !== 'completed' && $order->status !== 'cancelled')
                <div class="pt-6 border-t border-slate-50 space-y-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aksi Cepat</p>
                    <div class="flex flex-col gap-2">
                        @if(in_array($order->status, ['pending', 'payment_success']))
                            <button type="button" onclick="submitOrderStatus('processing')" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20">🍳 Terima & Masak</button>
                        @elseif($order->status === 'processing')
                            <button type="button" onclick="submitOrderStatus('ready')" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20">✅ Makanan Siap</button>
                        @elseif($order->status === 'ready')
                            <button type="button" onclick="submitOrderStatus('completed')" class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl">🏁 Selesaikan</button>
                        @endif
                    </div>
                    <form id="statusForm" method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="hidden">
                        @csrf
                        <input type="hidden" name="status" id="statusInput">
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
