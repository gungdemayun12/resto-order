@extends('layouts.customer')
@section('title', 'Lacak Pesanan')

@section('content')
@php
    $steps = [
        'pending_payment'     => ['label' => 'Menunggu Pembayaran',   'desc' => 'Selesaikan pembayaran terlebih dahulu.',          'icon' => 'wallet',   'time' => '~2 menit'],
        'payment_success'     => ['label' => 'Pembayaran Berhasil',   'desc' => 'Pembayaran diterima, menunggu proses.',           'icon' => 'currency', 'time' => '~1 menit'],
        'pending_confirmation'=> ['label' => 'Menunggu Konfirmasi',   'desc' => 'Tunjukkan barcode ke kasir untuk validasi.',       'icon' => 'qr',       'time' => 'Segera'],
        'pending'             => ['label' => 'Pesanan Diterima',      'desc' => 'Pesanan diterima, menunggu proses dapur.',        'icon' => 'clock',    'time' => '~5 menit'],
        'processing'          => ['label' => 'Sedang Dimasak',        'desc' => 'Chef sedang menyiapkan pesanan Anda.',            'icon' => 'fire',     'time' => '~15 menit'],
        'ready'               => ['label' => 'Siap Disajikan',        'desc' => 'Pesanan segera diantarkan ke meja Anda.',         'icon' => 'bell',     'time' => '~3 menit'],
        'completed'           => ['label' => 'Selesai',               'desc' => 'Selamat menikmati hidangan Anda!',                'icon' => 'sparkles', 'time' => 'Selesai'],
    ];

    // Map order status to step index for the timeline
    $statusFlow = ['pending_payment', 'payment_success', 'pending_confirmation', 'pending', 'processing', 'ready', 'completed'];

    // Build the list of visible steps for this order (skip irrelevant ones)
    $isCashierPayment = $order->payment_method === 'cashier';
    $visibleSteps = [];
    foreach ($statusFlow as $idx => $key) {
        if ($isCashierPayment && $key === 'payment_success') continue;
        if ($order->payment_method === 'online' && $key === 'pending_confirmation') continue;
        $visibleSteps[] = ['key' => $key, 'idx' => $idx, 'step' => $steps[$key]];
    }

    // Build the status-to-flowIdx map for Alpine (what index in statusFlow each status maps to)
    $statusFlowMap = [];
    foreach ($statusFlow as $i => $k) {
        $statusFlowMap[$k] = $i;
    }

    $statusTheme = \App\Models\Order::statusTheme($order->status);
@endphp

<div x-data="trackingApp()" x-init="init()" class="min-h-screen bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">

    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-amber-200/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -left-40 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 right-1/4 w-72 h-72 bg-amber-100/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10">

        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm">
            <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.menu', ['session' => $order->session_token]) }}" class="p-2 -ml-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <h1 class="font-bold text-slate-800 text-sm sm:text-base">Lacak Pesanan</h1>
                        <p class="text-[10px] sm:text-xs text-slate-400 font-mono font-bold">{{ $order->order_number }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 rounded-full">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-black text-emerald-700 tracking-wider">LIVE</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium hidden sm:block" x-text="lastUpdate"></span>
                </div>
            </div>
        </header>

        <div class="max-w-5xl mx-auto px-4 py-6 sm:py-8">

            <div class="relative rounded-3xl overflow-hidden mb-6 sm:mb-8 transition-all duration-700 shadow-xl bg-gradient-to-r"
                :class="activeTheme.gradient.split(' ')">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-20 -right-20 w-60 h-60 bg-white rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-white rounded-full blur-2xl"></div>
                </div>
                <div class="relative px-5 sm:px-8 py-6 sm:py-8 flex flex-col sm:flex-row items-center gap-5 sm:gap-8">
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center ring-2 ring-white/30">
                            <template x-if="statusKey === 'processing'">
                                <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
                                    <span class="absolute inset-0 rounded-full bg-white/20 animate-ping"></span>
                                    <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white processing-flame" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                                    </svg>
                                    <div class="absolute -top-1 left-1/2 -translate-x-1/2 flex gap-1">
                                        <span class="processing-steam w-1 h-1 bg-white/80 rounded-full" style="animation-delay: 0s"></span>
                                        <span class="processing-steam w-1 h-1 bg-white/90 rounded-full" style="animation-delay: 0.35s"></span>
                                        <span class="processing-steam w-1 h-1 bg-white/80 rounded-full" style="animation-delay: 0.7s"></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="statusKey === 'ready'">
                                <div class="relative flex items-center justify-center">
                                    <span class="absolute inset-0 rounded-full bg-white/20 animate-ping"></span>
                                    <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white bell-ring" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="statusKey === 'completed'">
                                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </template>
                            <template x-if="statusKey === 'pending_confirmation' || statusKey === 'unconfirmed'">
                                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white qr-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </template>
                            <template x-if="statusKey === 'pending_payment'">
                                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white wallet-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </template>
                            <template x-if="statusKey === 'payment_success'">
                                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="statusKey === 'pending'">
                                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white clock-tick" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>
                    </div>

                    <div class="text-center sm:text-left flex-1">
                        <p class="text-white/70 text-xs font-bold uppercase tracking-wider mb-1">Status Saat Ini</p>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-1.5" x-text="activeTheme.label"></h2>
                        <p class="text-white/80 text-sm font-medium max-w-sm" x-text="statusDesc"></p>
                        <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-[10px] font-bold text-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="lastUpdate"></span>
                            </span>
                            <template x-if="statusKey === 'processing'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-[10px] font-bold text-white">
                                    <span class="processing-dot w-1.5 h-1.5 bg-white rounded-full" style="animation-delay: 0ms"></span>
                                    <span class="processing-dot w-1.5 h-1.5 bg-white rounded-full" style="animation-delay: 160ms"></span>
                                    <span class="processing-dot w-1.5 h-1.5 bg-white rounded-full" style="animation-delay: 320ms"></span>
                                    Sedang Dimasak
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <template x-if="statusKey === 'completed'">
                <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-5 py-5 sm:py-6 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-xl font-black text-white">Pesanan Selesai!</h3>
                            <p class="text-white/80 text-sm">Terima kasih telah berkunjung. Selamat menikmati!</p>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-center gap-5">
                        <div class="shrink-0 p-3 bg-white rounded-xl shadow-sm border border-slate-100">
                            {!! QrCode::size(100)->margin(0)->color(30, 41, 59)->generate(route('order.tracking', $order->id)) !!}
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Struk Digital</p>
                            <p class="text-sm text-slate-500 mb-4">Unduh struk pembayaran sebagai bukti pesanan Anda.</p>
                            <a href="{{ route('customer.order.receipt', ['order' => $order->order_number]) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 text-white font-bold rounded-2xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25 active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Unduh Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 sm:gap-6 items-start">

                <div class="lg:col-span-2 space-y-5">

                    <template x-if="['pending_confirmation', 'unconfirmed'].includes(statusKey)">
                        <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-orange-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                <span class="text-white text-xs font-black uppercase tracking-wider">Perlu Tindakan</span>
                            </div>
                            <div class="p-5 text-center">
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 mb-4 inline-block">
                                    {!! QrCode::size(140)->margin(1)->color(30, 41, 59)->generate($order->order_number) !!}
                                </div>
                                <p class="text-sm font-bold text-slate-800 mb-1">Tunjukkan ke Kasir</p>
                                <p class="text-xs text-slate-400">Tunjukkan barcode di atas ke kasir untuk validasi dan pembayaran pesanan Anda.</p>
                                <div class="mt-3 bg-orange-50 rounded-xl px-3 py-2 inline-flex items-center gap-1.5 text-[10px] font-bold text-orange-700">
                                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-ping"></span>
                                    MENUNGGU VALIDASI KASIR
                                </div>
                            </div>
                        </div>
                    </template>

                    @if($order->payment_method === 'online' && $order->status === 'pending_payment' && $order->snap_token && $order->payment_status === 'unpaid')
                    <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-yellow-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-500 to-amber-500 px-5 py-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-white text-xs font-black uppercase tracking-wider">Pembayaran Belum Selesai</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-slate-500 mb-4">Selesaikan pembayaran agar pesanan Anda segera diproses.</p>
                            <button id="pay-button" class="w-full flex items-center justify-center gap-2 py-3.5 bg-gradient-to-r from-yellow-500 to-amber-600 text-white font-bold rounded-2xl hover:from-yellow-600 hover:to-amber-700 transition-all shadow-lg shadow-yellow-500/30 active:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Progress Pesanan</h3>
                            <span class="text-[10px] font-bold text-slate-300" x-text="progressText"></span>
                        </div>

                        <div class="space-y-0">
                            @foreach($visibleSteps as $i => $vs)
                                @php $key = $vs['key']; $step = $vs['step']; $flowIdx = $vs['idx']; @endphp

                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 transition-all duration-500 border-2"
                                            :class="
                                                flowIdxToStatus({{ $flowIdx }}) === 'completed'
                                                    ? 'bg-emerald-500 text-white border-emerald-500'
                                                    : flowIdxToStatus({{ $flowIdx }}) === 'active'
                                                        ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-500/30 step-active-ring'
                                                        : 'bg-white text-slate-300 border-slate-200'
                                            ">
                                            <template x-if="flowIdxToStatus({{ $flowIdx }}) === 'completed'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                            <template x-if="flowIdxToStatus({{ $flowIdx }}) === 'active'">
                                                <span class="w-2.5 h-2.5 bg-white rounded-full step-dot-pulse"></span>
                                            </template>
                                            <template x-if="flowIdxToStatus({{ $flowIdx }}) === 'future'">
                                                <span class="w-2 h-2 bg-slate-200 rounded-full"></span>
                                            </template>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-0.5 transition-all duration-700"
                                                :class="
                                                    flowIdxToStatus({{ $flowIdx }}) === 'completed'
                                                        ? 'bg-emerald-400'
                                                        : flowIdxToStatus({{ $flowIdx }}) === 'active'
                                                            ? 'bg-amber-300 step-connector-active'
                                                            : 'bg-slate-100'
                                                "
                                                style="min-height: 2.5rem;"></div>
                                        @endif
                                    </div>

                                    <div class="pb-5 pt-1 flex-1 min-w-0">
                                        <p class="text-sm font-bold transition-colors"
                                            :class="
                                                flowIdxToStatus({{ $flowIdx }}) === 'completed'
                                                    ? 'text-emerald-700'
                                                    : flowIdxToStatus({{ $flowIdx }}) === 'active'
                                                        ? 'text-slate-800'
                                                        : 'text-slate-300'
                                            ">
                                            {{ $step['label'] }}
                                        </p>
                                        <p class="text-xs mt-0.5 transition-colors"
                                            :class="
                                                flowIdxToStatus({{ $flowIdx }}) === 'completed'
                                                    ? 'text-emerald-500'
                                                    : flowIdxToStatus({{ $flowIdx }}) === 'active'
                                                        ? 'text-slate-500'
                                                        : 'text-slate-200'
                                            ">
                                            {{ $step['desc'] }}
                                        </p>
                                        <template x-if="flowIdxToStatus({{ $flowIdx }}) === 'active'">
                                            <div class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Est. {{ $step['time'] }}
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 space-y-5">

                    <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5 sm:p-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Detail Pesanan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Pemesan</p>
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $order->customer_name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Meja</p>
                                    <p class="text-sm font-bold text-slate-800">Meja {{ $order->table->number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $order->payment_method === 'online' ? 'bg-blue-50' : 'bg-emerald-50' }} flex items-center justify-center shrink-0">
                                    @if($order->payment_method === 'online')
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Pembayaran</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $order->payment_method === 'online' ? 'Online' : 'Bayar di Kasir' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Waktu</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $order->created_at->format('H:i') }} &middot; {{ $order->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($order->payment_method === 'online' && $order->status === 'payment_success')
                            <div class="mt-4 bg-emerald-50 border border-emerald-100 rounded-2xl px-4 py-3 flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-emerald-700">Pembayaran Berhasil</p>
                                    <p class="text-[11px] text-emerald-600">Pesanan telah dibayar dan menunggu proses.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 overflow-hidden">
                        <div class="px-5 sm:px-6 pt-5 sm:pt-6 pb-3">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-sm sm:text-base flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    Pesanan Anda
                                </h3>
                                <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $order->items->count() }} item</span>
                            </div>
                        </div>

                        <div class="px-5 sm:px-6">
                            <div class="divide-y divide-slate-50">
                                @foreach($order->items as $item)
                                    <div class="flex items-center gap-3 sm:gap-4 py-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100/50 flex items-center justify-center text-xs font-black text-amber-700 shrink-0">
                                            {{ $item->quantity }}x
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ $item->menuItem->name }}</p>
                                            @if($item->notes)
                                                <p class="text-[11px] text-amber-600 truncate italic">{{ $item->notes }}</p>
                                            @else
                                                <p class="text-[11px] text-slate-300">Rp {{ number_format($item->price, 0, ',', '.') }}/pcs</p>
                                            @endif
                                        </div>
                                        <span class="font-bold text-slate-800 text-sm whitespace-nowrap">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($order->notes)
                            <div class="mx-5 sm:mx-6 mt-3 p-3 bg-amber-50 border border-amber-100 rounded-2xl text-xs text-amber-800">
                                <span class="font-bold">Catatan:</span> {{ $order->notes }}
                            </div>
                        @endif

                        <div class="px-5 sm:px-6 pt-4 pb-5 sm:pb-6">
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
                                    <span class="text-xl sm:text-2xl font-black text-amber-600">{{ $order->formatted_total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-8 sm:mt-10 max-w-2xl mx-auto">
                <a href="{{ route('customer.menu', ['session' => $order->session_token]) }}"
                    class="flex items-center justify-center gap-2.5 w-full py-4 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-[0.98]">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Pesan Menu Lain
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== Active step pulsing dot ===== */
    @keyframes step-dot-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.4); opacity: 0.6; }
    }
    .step-dot-pulse { animation: step-dot-pulse 2s ease-in-out infinite; }

    /* ===== Active step ring ===== */
    @keyframes step-ring-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
    }
    .step-active-ring { animation: step-ring-pulse 2s ease-in-out infinite; }

    /* ===== Active connector glow ===== */
    @keyframes connector-flow {
        0% { background-position: 0 0; }
        100% { background-position: 0 20px; }
    }
    .step-connector-active {
        background: repeating-linear-gradient(
            to bottom,
            #fbbf24 0px,
            #fbbf24 4px,
            #fde68a 4px,
            #fde68a 8px
        );
        background-size: 2px 20px;
        animation: connector-flow 1s linear infinite;
    }

    /* ===== Processing animations ===== */
    @keyframes processing-steam-rise {
        0%, 100% { opacity: 0.25; transform: translateY(0) scale(0.85); }
        50% { opacity: 1; transform: translateY(-10px) scale(1.1); }
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
    .processing-flame { animation: processing-flame-flicker 1.6s ease-in-out infinite; transform-origin: center bottom; }
    .processing-dot { animation: processing-dot-bounce 1.2s ease-in-out infinite; }

    /* ===== Bell ring ===== */
    @keyframes bell-ring-anim {
        0%, 100% { transform: rotate(0deg); }
        10% { transform: rotate(14deg); }
        20% { transform: rotate(-12deg); }
        30% { transform: rotate(10deg); }
        40% { transform: rotate(-8deg); }
        50% { transform: rotate(4deg); }
        60% { transform: rotate(0deg); }
    }
    .bell-ring { animation: bell-ring-anim 2s ease-in-out infinite; transform-origin: top center; }

    /* ===== QR pulse ===== */
    @keyframes qr-pulse-anim {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .qr-pulse { animation: qr-pulse-anim 2.5s ease-in-out infinite; }

    /* ===== Wallet pulse ===== */
    @keyframes wallet-pulse-anim {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.08); opacity: 0.9; }
    }
    .wallet-pulse { animation: wallet-pulse-anim 2s ease-in-out infinite; }

    /* ===== Clock tick ===== */
    @keyframes clock-tick-anim {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(5deg); }
        75% { transform: rotate(-5deg); }
    }
    .clock-tick { animation: clock-tick-anim 3s ease-in-out infinite; }
</style>

@push('scripts')
@if(config('services.midtrans.is_production'))
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<script>
function trackingApp() {
    return {
        currentFlowIdx: {{ $statusFlowMap[$order->status] ?? 0 }},
        statusKey: '{{ $order->status }}',
        statusThemes: @json(\App\Models\Order::statusThemes()),
        lastUpdate: 'Baru saja',
        statusDescMap: @json(collect($steps)->mapWithKeys(fn($s, $k) => [$k => $s['desc']])),
        statusFlowMap: @json($statusFlowMap),
        orderId: {{ $order->id }},

        // Determine if a step is completed, active, or future
        flowIdxToStatus(flowIdx) {
            if (flowIdx < this.currentFlowIdx) return 'completed';
            if (flowIdx === this.currentFlowIdx) return 'active';
            return 'future';
        },

        get activeTheme() {
            const key = ['pending_confirmation', 'unconfirmed'].includes(this.statusKey) ? 'pending_confirmation' : this.statusKey;
            return this.statusThemes[key] || this.statusThemes.pending || {
                label: 'Status Pesanan',
                gradient: 'from-slate-500 to-slate-700',
                card_border: 'border-slate-200',
                card_bg: 'bg-slate-50',
                icon_wrap_bg: 'bg-slate-100',
                icon_text: 'text-slate-600'
            };
        },

        get statusDesc() {
            return this.statusDescMap[this.statusKey] || 'Memantau pesanan Anda...';
        },

        get progressText() {
            const current = this.currentFlowIdx + 1;
            return current + '/6 langkah';
        },

        // Central handler for status changes (used by both Echo and polling)
        handleStatusChange(newStatusKey) {
            const newFlowIdx = this.statusFlowMap[newStatusKey];
            if (newFlowIdx === undefined || newFlowIdx === this.currentFlowIdx) return;

            this.currentFlowIdx = newFlowIdx;
            this.statusKey = newStatusKey;

            // Status change notifications
            if (this.statusKey === 'completed') {
                Swal.fire({
                    title: 'Pesanan Selesai!',
                    text: 'Terima kasih telah berkunjung. Selamat menikmati!',
                    icon: 'success',
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Terima Kasih!',
                    customClass: { popup: 'rounded-2xl', title: 'font-bold text-slate-800' }
                });
            } else if (this.statusKey === 'ready') {
                Swal.fire({
                    title: 'Pesanan Siap!',
                    text: 'Makanan Anda siap disajikan ke meja.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: { popup: 'rounded-2xl' }
                });
            } else if (this.statusKey === 'processing') {
                Swal.fire({
                    title: 'Sedang Dimasak!',
                    text: 'Chef sedang menyiapkan pesanan Anda.',
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'rounded-2xl' }
                });
            }
        },

        init() {
            // === REAL-TIME: Echo WebSocket listener (instant updates) ===
            if (window.Echo) {
                window.Echo.channel('admin-orders')
                    .listen('.OrderUpdated', (e) => {
                        // Only react if this update is for OUR order
                        if (e.order && e.order.id === this.orderId) {
                            const newStatus = e.order.status;
                            if (newStatus && newStatus !== this.statusKey) {
                                this.lastUpdate = 'Baru saja';
                                this.handleStatusChange(newStatus);
                            }
                        }
                    });
            }

            // === FALLBACK: Polling every 8 seconds ===
            setInterval(async () => {
                try {
                    const res = await fetch('{{ route("order.status", $order->id) }}', {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (data.updated_at) {
                        this.lastUpdate = data.updated_at;
                    }

                    if (data.status && data.status !== this.statusKey) {
                        this.handleStatusChange(data.status);
                    }
                } catch(e) {
                    console.error('Failed to fetch status', e);
                }
            }, 8000);

            // Midtrans payment handler
            @if($order->payment_method === 'online' && $order->status === 'pending_payment' && $order->snap_token)
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function () {
                    window.snap.pay('{{ $order->snap_token }}', {
                        onSuccess: async function(result){
                            showToast('Pembayaran berhasil! Memverifikasi...', 'success');
                            try {
                                const res = await fetch('{{ route('customer.order.verify-payment', ['order' => $order->order_number]) }}', {
                                    headers: { 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.status && data.status !== 'pending_payment') {
                                    window.location.href = "{{ route('customer.order.success', ['order' => $order->order_number]) }}";
                                    return;
                                }
                            } catch(e) {
                                console.error('Verify payment failed', e);
                            }
                            setTimeout(() => location.reload(), 2000);
                        },
                        onPending: function(result){
                            showToast('Menunggu pembayaran Anda!', 'info');
                        },
                        onError: function(result){
                            showToast('Pembayaran gagal!', 'error');
                        },
                        onClose: function(){
                            // Verify payment in case user completed payment in another tab
                            fetch('{{ route('customer.order.verify-payment', ['order' => $order->order_number]) }}', {
                                headers: { 'Accept': 'application/json' }
                            }).then(r => r.json()).then(data => {
                                if (data.status && data.status !== 'pending_payment') {
                                    location.reload();
                                }
                            }).catch(() => {});
                        }
                    });
                });
            }
            @endif

            // === PAYMENT VERIFICATION: Auto-verify Midtrans for pending_payment orders ===
            @if($order->payment_method === 'online' && $order->status === 'pending_payment')
            this._paymentVerifyInterval = setInterval(async () => {
                if (this.statusKey !== 'pending_payment') {
                    clearInterval(this._paymentVerifyInterval);
                    return;
                }
                try {
                    const res = await fetch('{{ route('customer.order.verify-payment', ['order' => $order->order_number]) }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.status && data.status !== 'pending_payment') {
                        clearInterval(this._paymentVerifyInterval);
                        this.handleStatusChange(data.status);
                        // Redirect to success page after a short delay
                        Swal.fire({
                            title: 'Pembayaran Berhasil!',
                            text: 'Pembayaran Anda telah dikonfirmasi.',
                            icon: 'success',
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'Lihat Pesanan',
                            customClass: { popup: 'rounded-2xl', title: 'font-bold text-slate-800' }
                        }).then(() => {
                            window.location.href = "{{ route('customer.order.success', ['order' => $order->order_number]) }}";
                        });
                    }
                } catch(e) {
                    console.error('Payment verify error', e);
                }
            }, 15000);
            @endif
        }
    };
}
</script>
@endpush
@endsection
