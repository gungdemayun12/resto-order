@extends('layouts.customer')

@section('title', 'Pesanan Berhasil')

@section('content')
    @php
        $useOrangeTheme = in_array($order->status, ['processing', 'payment_success']);
        $isProcessing = $order->status === 'processing';
        $isPaymentSuccess = $order->payment_method === 'online' && $order->status === 'payment_success';
        $isWaitingCashier = $order->status === 'pending_confirmation';
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">

        <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="absolute -top-40 -right-40 w-96 h-96 {{ $useOrangeTheme ? 'bg-orange-200/30' : 'bg-amber-200/30' }} rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 -left-40 w-80 h-80 {{ $useOrangeTheme ? 'bg-orange-100/40' : 'bg-amber-100/40' }} rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 right-1/4 w-72 h-72 {{ $useOrangeTheme ? 'bg-orange-100/30' : 'bg-amber-100/30' }} rounded-full blur-3xl"></div>
        </div>

        <canvas id="confetti-canvas" class="fixed inset-0 pointer-events-none z-50"></canvas>

        <div class="relative z-10 px-4 py-6 sm:py-10 md:py-14 max-w-5xl mx-auto">

            <div class="text-center mb-8 sm:mb-10">
                <div class="relative inline-block mb-5">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full flex items-center justify-center mx-auto {{ $useOrangeTheme ? 'bg-orange-50 ring-4 ring-orange-100' : 'bg-amber-50 ring-4 ring-amber-100' }} success-icon-bounce">
                        @if($isProcessing)
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 text-orange-500 processing-flame" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                            </svg>
                        @else
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 text-amber-500 success-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="absolute -top-1 -right-1 w-7 h-7 {{ $useOrangeTheme ? 'bg-orange-400' : 'bg-amber-400' }} rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">
                    @if($isProcessing)
                        Pesanan Sedang Diproses!
                    @elseif($isPaymentSuccess)
                        Pembayaran Berhasil!
                    @else
                        Pesanan Diterima!
                    @endif
                </h1>
                <p class="text-slate-500 font-medium text-sm sm:text-base max-w-md mx-auto">
                    @if($isProcessing)
                        Pembayaran dikonfirmasi. Pesanan sedang diproses oleh dapur.
                    @elseif($isPaymentSuccess)
                        Pembayaran berhasil. Pesanan Anda sedang menunggu proses.
                    @else
                        Silakan tunjukkan barcode ke kasir untuk validasi pesanan.
                    @endif
                </p>

                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold {{ ($isProcessing || $isPaymentSuccess) ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700' }}">
                    @if($isProcessing)
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                        PESANAN SEDANG DIPROSES
                    @elseif($isPaymentSuccess)
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                        PEMBAYARAN DITERIMA
                    @else
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        MENUNGGU VALIDASI KASIR
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8 items-start">

                <div class="lg:col-span-2 order-1">
                    @if($isProcessing)
                        <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 overflow-hidden">
                            <div class="p-6 sm:p-8 text-center">
                                <div class="relative w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                                    <span class="absolute inset-0 rounded-full bg-orange-200/50 animate-ping opacity-40"></span>
                                    <span class="absolute inset-1 rounded-full border-2 border-orange-300/60 border-dashed processing-orbit"></span>
                                    <div class="relative w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center shadow-inner ring-6 ring-orange-100/80 processing-icon-pulse">
                                        <svg class="w-11 h-11 text-orange-500 processing-flame" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                                        </svg>
                                        <div class="absolute -top-2 left-1/2 -translate-x-1/2 flex gap-1">
                                            <span class="processing-steam w-1.5 h-1.5 bg-orange-400 rounded-full" style="animation-delay: 0s"></span>
                                            <span class="processing-steam w-1.5 h-1.5 bg-orange-500 rounded-full" style="animation-delay: 0.35s"></span>
                                            <span class="processing-steam w-1.5 h-1.5 bg-orange-400 rounded-full" style="animation-delay: 0.7s"></span>
                                        </div>
                                    </div>
                                </div>

                                <h2 class="text-xl font-black text-slate-800 mb-2">Sedang Diproses</h2>
                                <p class="text-sm text-orange-600 font-medium mb-4">Pembayaran Anda telah diterima.<br>Pesanan sedang diproses oleh dapur.</p>

                                <div class="flex items-center justify-center gap-2">
                                    <span class="processing-dot w-2 h-2 bg-orange-500 rounded-full" style="animation-delay: 0ms"></span>
                                    <span class="processing-dot w-2 h-2 bg-orange-500 rounded-full" style="animation-delay: 160ms"></span>
                                    <span class="processing-dot w-2 h-2 bg-orange-500 rounded-full" style="animation-delay: 320ms"></span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 overflow-hidden">
                            <div class="p-6 sm:p-8 text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] mb-5">Barcode Validasi</p>

                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-5 inline-block transition-transform hover:scale-[1.02] duration-300">
                                    {!! QrCode::size(180)->margin(1)->color(30, 41, 59)->generate($order->order_number) !!}
                                </div>

                                <div class="bg-slate-50 rounded-2xl p-4">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Order</p>
                                    <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-[0.15em] mb-2">{{ $order->order_number }}</h2>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-ping"></span>
                                        MENUNGGU VALIDASI KASIR
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5 sm:p-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Pesanan</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 7a4 4 0 00-8 0 4 4 0 008 0zm0 14l-4-4h8l-4 4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m-3-3h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">Pemesan</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">Meja</p>
                                        <p class="text-sm font-bold text-slate-800">Meja {{ $order->table->number }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $order->payment_method === 'online' ? 'bg-blue-50' : 'bg-emerald-50' }} flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 {{ $order->payment_method === 'online' ? 'text-blue-600' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">Pembayaran</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $order->payment_method === 'online' ? 'Online (QRIS/e-Wallet)' : 'Bayar di Kasir' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">Waktu Pesan</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $order->created_at->format('H:i') }} &middot; {{ $order->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 order-2">
                    <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 overflow-hidden">

                        <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-base sm:text-lg flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    Ringkasan Pesanan
                                </h3>
                                <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg">#{{ $order->table->number }}</span>
                            </div>
                        </div>

                        <div class="px-6 sm:px-8">
                            <div class="space-y-3">
                                @foreach($order->items as $item)
                                    <div class="flex items-center gap-3 sm:gap-4 py-3 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100/50 flex items-center justify-center text-xs font-black text-amber-700 shrink-0">
                                            {{ $item->quantity }}x
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ $item->menuItem->name }}</p>
                                            @if($item->notes)
                                                <p class="text-[11px] text-slate-400 truncate">{{ $item->notes }}</p>
                                            @else
                                                <p class="text-[11px] text-slate-300">Rp {{ number_format($item->price, 0, ',', '.') }}/pcs</p>
                                            @endif
                                        </div>
                                        <span class="font-bold text-slate-800 text-sm whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="px-6 sm:px-8 pt-4 pb-6 sm:pb-8">
                            <div class="bg-slate-50 rounded-2xl p-5">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-slate-500">Subtotal</span>
                                    <span class="font-semibold text-slate-700">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm mb-3">
                                    <span class="text-slate-500">Pajak (11%)</span>
                                    <span class="font-semibold text-slate-700">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-dashed border-slate-200 pt-3 flex justify-between items-center">
                                    <span class="font-bold text-slate-800">Total Tagihan</span>
                                    <span class="text-2xl sm:text-3xl font-black text-amber-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5 sm:p-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-5">Langkah Selanjutnya</h3>
                        <div class="space-y-0">
                            @if($isWaitingCashier)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-amber-500/30 shrink-0">
                                            1
                                        </div>
                                        <div class="w-0.5 h-full bg-slate-200 my-1"></div>
                                    </div>
                                    <div class="pb-6">
                                        <p class="font-bold text-slate-800 text-sm">Tunjukkan Barcode ke Kasir</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Bawa nomor order atau barcode ke kasir untuk validasi</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold shrink-0">
                                            2
                                        </div>
                                        <div class="w-0.5 h-full bg-slate-200 my-1"></div>
                                    </div>
                                    <div class="pb-6">
                                        <p class="font-semibold text-slate-400 text-sm">Pembayaran di Kasir</p>
                                        <p class="text-xs text-slate-300 mt-0.5">Bayar di kasir dengan tunai atau kartu</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold shrink-0">
                                            3
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-400 text-sm">Pesanan Diproses</p>
                                        <p class="text-xs text-slate-300 mt-0.5">Pesanan akan diproses oleh dapur</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="w-0.5 h-full bg-emerald-300 my-1"></div>
                                    </div>
                                    <div class="pb-6">
                                        <p class="font-bold text-emerald-700 text-sm">
                                            @if($order->payment_method === 'online')
                                                Pembayaran Berhasil
                                            @else
                                                Validasi Kasir Berhasil
                                            @endif
                                        </p>
                                        <p class="text-xs text-emerald-500 mt-0.5">
                                            @if($order->payment_method === 'online')
                                                Pembayaran telah diterima dan dikonfirmasi
                                            @else
                                                Kasir telah memvalidasi dan menerima pembayaran
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-amber-500/30 shrink-0">
                                            2
                                        </div>
                                        <div class="w-0.5 h-full bg-slate-200 my-1"></div>
                                    </div>
                                    <div class="pb-6">
                                        <p class="font-bold text-slate-800 text-sm">Pesanan Sedang Diproses</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Dapur sedang menyiapkan pesanan Anda</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold shrink-0">
                                            3
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-400 text-sm">Pesanan Siap Disajikan</p>
                                        <p class="text-xs text-slate-300 mt-0.5">Kami akan mengantarkan ke meja Anda</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 sm:mt-10 max-w-2xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <a href="{{ route('order.tracking', ['order' => $order->id]) }}"
                        class="flex items-center justify-center gap-2.5 w-full py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/15 active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Pantau Status Pesanan
                    </a>

                    <a href="{{ route('customer.order.receipt', ['order' => $order->order_number]) }}" target="_blank"
                        class="flex items-center justify-center gap-2.5 w-full py-4 bg-amber-500 text-white font-bold rounded-2xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25 active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh Receipt
                    </a>
                </div>

                <div class="mt-5 text-center">
                    @if($order->payment_method === 'online' && in_array($order->status, ['payment_success', 'processing']))
                        <p class="text-xs text-slate-400 font-medium px-4 leading-relaxed max-w-md mx-auto">
                            Pembayaran telah berhasil diterima. Pesanan Anda sekarang akan diproses oleh dapur. Terima kasih telah menggunakan pembayaran online.
                        </p>
                    @elseif($order->status === 'processing')
                        <p class="text-xs text-slate-400 font-medium px-4 leading-relaxed max-w-md mx-auto">
                            Pembayaran telah dikonfirmasi oleh kasir. Pesanan Anda sedang diproses oleh dapur. Silakan tunggu hingga pesanan siap.
                        </p>
                    @else
                        <p class="text-xs text-slate-400 font-medium px-4 leading-relaxed max-w-md mx-auto">
                            Pesanan Anda akan masuk ke dapur setelah kasir memvalidasi barcode. Silakan menuju kasir untuk melakukan konfirmasi.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ===== Success icon bounce ===== */
        @keyframes success-bounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.08); opacity: 1; }
            70% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
        .success-icon-bounce {
            animation: success-bounce 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }

        /* ===== Checkmark draw ===== */
        @keyframes check-draw {
            0% { stroke-dashoffset: 60; }
            100% { stroke-dashoffset: 0; }
        }
        .success-check {
            stroke-dasharray: 60;
            stroke-dashoffset: 60;
            animation: check-draw 0.6s ease-out 0.6s both;
        }

        /* ===== Processing animations ===== */
        @keyframes processing-steam-rise {
            0%, 100% {
                opacity: 0.25;
                transform: translateY(0) scale(0.85);
            }
            50% {
                opacity: 1;
                transform: translateY(-10px) scale(1.1);
            }
        }

        @keyframes processing-icon-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        @keyframes processing-orbit-spin {
            to { transform: rotate(360deg); }
        }

        @keyframes processing-flame-flicker {
            0%, 100% { transform: scale(1) rotate(-2deg); }
            33% { transform: scale(1.08) rotate(2deg); }
            66% { transform: scale(0.96) rotate(-1deg); }
        }

        @keyframes processing-dot-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
            40% { transform: translateY(-6px); opacity: 1; }
        }

        .processing-steam { animation: processing-steam-rise 1.4s ease-in-out infinite; }
        .processing-icon-pulse { animation: processing-icon-pulse 2s ease-in-out infinite; }
        .processing-orbit { animation: processing-orbit-spin 8s linear infinite; }
        .processing-flame {
            animation: processing-flame-flicker 1.6s ease-in-out infinite;
            transform-origin: center bottom;
        }
        .processing-dot { animation: processing-dot-bounce 1.2s ease-in-out infinite; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = "{{ $order->payment_method }}";
            const status = "{{ $order->status }}";
            const orderId = "{{ $order->id }}";

            // Redirect for payment_success online orders
            if (paymentMethod === 'online' && status === 'payment_success') {
                const receiptUrl = "{{ route('customer.order.receipt', ['order' => $order->order_number]) }}";
                window.location.replace(receiptUrl);
                return;
            }

            // Auto-poll: check order status every 3s, reload page if changed
            // This ensures the page updates when cashier scans the barcode
            const currentStatus = status;
            const statusCheckInterval = setInterval(function() {
                fetch('/order/' + orderId + '/status', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status && data.status !== currentStatus) {
                        clearInterval(statusCheckInterval);
                        window.location.reload();
                    }
                })
                .catch(() => {}); // Silently ignore network errors
            }, 3000);

            // Stop polling when user leaves the page
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    clearInterval(statusCheckInterval);
                }
            });

            // Confetti effect
            const canvas = document.getElementById('confetti-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                const confettiColors = ['#f59e0b', '#fb923c', '#f97316', '#eab308', '#fbbf24', '#d97706', '#10b981', '#3b82f6'];
                const confettiPieces = [];

                for (let i = 0; i < 80; i++) {
                    confettiPieces.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height - canvas.height,
                        w: Math.random() * 8 + 4,
                        h: Math.random() * 6 + 3,
                        color: confettiColors[Math.floor(Math.random() * confettiColors.length)],
                        rotation: Math.random() * 360,
                        rotationSpeed: (Math.random() - 0.5) * 8,
                        speedX: (Math.random() - 0.5) * 3,
                        speedY: Math.random() * 3 + 2,
                        opacity: 1
                    });
                }

                let frame = 0;
                const maxFrames = 180;

                function animateConfetti() {
                    frame++;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    const fadeStart = maxFrames * 0.6;
                    const globalOpacity = frame > fadeStart ? 1 - ((frame - fadeStart) / (maxFrames - fadeStart)) : 1;

                    confettiPieces.forEach(p => {
                        p.x += p.speedX;
                        p.y += p.speedY;
                        p.rotation += p.rotationSpeed;
                        p.speedY += 0.04;

                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate((p.rotation * Math.PI) / 180);
                        ctx.globalAlpha = globalOpacity;
                        ctx.fillStyle = p.color;
                        ctx.beginPath();
                        ctx.roundRect(-p.w / 2, -p.h / 2, p.w, p.h, 1.5);
                        ctx.fill();
                        ctx.restore();
                    });

                    if (frame < maxFrames) {
                        requestAnimationFrame(animateConfetti);
                    } else {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }

                animateConfetti();
            }

            // Handle resize
            window.addEventListener('resize', function() {
                if (canvas) {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                }
            });
        });
    </script>
@endsection