@forelse($orders as $order)
    <div x-data="{ expanded: false }"
        class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow"
        data-order-id="{{ $order->id }}">
        <div class="px-5 py-4 cursor-pointer" @click="expanded = !expanded">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php
                        $statusStyles = [
                            'pending' => 'bg-yellow-400',
                            'payment_success' => 'bg-emerald-400',
                            'processing' => 'bg-blue-400',
                            'ready' => 'bg-emerald-500',
                            'completed' => 'bg-slate-400',
                            'cancelled' => 'bg-red-400',
                            'pending_payment' => 'bg-amber-400',
                            'pending_confirmation' => 'bg-orange-400',
                        ];
                        $statusBar = $statusStyles[$order->status] ?? 'bg-slate-300';
                    @endphp
                    <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center relative overflow-hidden">
                        <span class="text-lg font-black text-white relative z-10">{{ $order->table->number }}</span>
                        <div class="absolute bottom-0 left-0 right-0 h-1 {{ $statusBar }}"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-800">{{ $order->order_number }}</h3>
                            @php $sc = ['pending' => 'bg-yellow-100 text-yellow-700', 'payment_success' => 'bg-emerald-100 text-emerald-700', 'processing' => 'bg-blue-100 text-blue-700', 'ready' => 'bg-emerald-100 text-emerald-700', 'completed' => 'bg-slate-100 text-slate-600', 'cancelled' => 'bg-red-100 text-red-700']; @endphp
                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full {{ $sc[$order->status] ?? '' }} uppercase tracking-wider">{{ $order->status_label }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                            <span>Meja {{ $order->table->number }}</span>
                            <span class="text-slate-300">·</span>
                            <span>{{ $order->items->count() }} item</span>
                            <span class="text-slate-300">·</span>
                            <span>{{ $order->created_at->format('H:i') }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-base font-black text-slate-800 leading-none">{{ $order->formatted_total }}</p>
                        <span class="text-[10px] uppercase font-bold mt-0.5 inline-block {{ $order->payment_method === 'online' ? 'text-blue-500' : 'text-emerald-500' }}">{{ $order->payment_method === 'online' ? 'Online' : 'Kasir' }}</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 transition-transform" :class="expanded ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>

        <div x-show="expanded" x-transition class="border-t border-slate-100 px-6 py-4">
            <div class="space-y-3 mb-4">
                @foreach($order->items as $oi)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-sm font-bold text-amber-600">{{ $oi->quantity }}x</span>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $oi->menuItem->name }}</p>
                                @if($oi->notes)
                                <p class="text-xs text-slate-400">📝 {{ $oi->notes }}</p>@endif
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">{{ $oi->formatted_subtotal }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-slate-100 pt-3">
                <div class="flex justify-between text-sm mb-1"><span class="text-slate-500">Subtotal</span><span>Rp
                        {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-sm mb-1"><span class="text-slate-500">PPN 11%</span><span>Rp
                        {{ number_format($order->tax, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-base font-bold"><span>Total</span><span
                        class="text-amber-600">{{ $order->formatted_total }}</span></div>
            </div>
            @if($order->notes)
            <p class="mt-3 text-sm text-slate-500 bg-slate-50 p-3 rounded-xl">📝 {{ $order->notes }}</p>@endif

            @if(!in_array($order->status, ['completed', 'cancelled', 'pending_payment', 'pending_confirmation']))
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex flex-wrap gap-2">
                        @if(in_array($order->status, ['pending', 'payment_success']))
                            <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block"
                                onsubmit="return confirmStatusUpdate(event, 'Terima pesanan ini dan mulai masak?')">
                                @csrf
                                <input type="hidden" name="status" value="processing">
                                <button type="submit"
                                    class="px-4 py-2 bg-amber-500 text-white text-[11px] font-bold rounded-xl hover:bg-amber-600 transition-all uppercase tracking-wider shadow-sm shadow-amber-500/20 active:scale-95 flex items-center gap-1.5">
                                    <span>🍳</span> Terima & Masak
                                </button>
                            </form>
                        @endif
            
                        @if(in_array($order->status, ['pending', 'payment_success', 'processing']))
                            <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block"
                                onsubmit="return confirmStatusUpdate(event, 'Pesanan sudah siap saji?')">
                                @csrf
                                <input type="hidden" name="status" value="ready">
                                <button type="submit"
                                    class="px-4 py-2 bg-emerald-500 text-white text-[11px] font-bold rounded-xl hover:bg-emerald-600 transition-all uppercase tracking-wider shadow-sm shadow-emerald-500/20 active:scale-95 flex items-center gap-1.5">
                                    <span>✅</span> Siap
                                </button>
                            </form>
                        @endif
            
                        @if(in_array($order->status, ['pending', 'processing', 'ready']))
                            <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block"
                                onsubmit="return confirmStatusUpdate(event, 'Selesaikan pesanan ini?')">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                    class="px-4 py-2 bg-slate-800 text-white text-[11px] font-bold rounded-xl hover:bg-slate-900 transition-all uppercase tracking-wider shadow-sm active:scale-95 flex items-center gap-1.5">
                                    <span>🏁</span> Selesai
                                </button>
                            </form>
                        @endif
            
                        @if(!in_array($order->status, ['completed', 'cancelled']))
                            <form method="POST" action="{{ role_route('admin.orders.update_status', $order) }}" class="inline-block"
                                onsubmit="return confirmStatusUpdate(event, 'Tolak pesanan ini?', 'warning')">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                    class="px-4 py-2 bg-white text-red-500 border border-red-100 text-[11px] font-bold rounded-xl hover:bg-red-50 transition-all uppercase tracking-wider active:scale-95 flex items-center gap-1.5">
                                    <span>❌</span> Tolak
                                </button>
                            </form>
                        @endif
                        <button type="button" onclick="openOrderDetail('{{ $order->id }}', '{{ role_route('admin.orders.data', $order) }}')" class="px-4 py-2 bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-bold rounded-xl hover:bg-slate-200 transition-all uppercase tracking-wider active:scale-95 flex items-center gap-1.5">
                            <span>📋</span> Detail
                        </button>
                    </div>
                </div>
            @elseif(in_array($order->status, ['pending_payment', 'pending_confirmation']))
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2 text-xs text-amber-600 bg-amber-50 px-3 py-2 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold">
                            @if($order->status === 'pending_payment')
                                Menunggu customer menyelesaikan pembayaran online
                            @else
                                Menunggu konfirmasi kasir - scan barcode untuk memproses
                            @endif
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-12 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-slate-400">Tidak ada pesanan</p>
    </div>
@endforelse