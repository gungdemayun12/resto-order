@extends('layouts.customer')

@section('title', 'Pembayaran Online - Resto Nusantara')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-6 text-center">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-8 md:p-10 border border-slate-100">
            <div class="flex justify-center mb-6">
                <x-resto-logo size="md" />
            </div>
            <p class="text-sm text-slate-500 mb-8">Pembayaran Virtual Account untuk pesanan <span class="font-bold text-slate-700">#{{ $order->order_number }}</span></p>

            <div class="bg-slate-50 rounded-2xl p-6 mb-8 text-left space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Total Tagihan</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Metode</span>
                    <span class="font-semibold text-amber-600">Pembayaran Online</span>
                </div>
            </div>

            @if($order->status === 'pending_payment' && $order->snap_token)
                <button id="pay-button" class="group w-full py-4 md:py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black text-base md:text-lg uppercase tracking-widest rounded-2xl shadow-xl shadow-amber-600/30 flex items-center justify-center gap-3 active:scale-[0.98] transform hover:scale-[1.02] transition-all hover:from-amber-600 hover:to-amber-700">
                    Lanjutkan Pembayaran
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            @else
                <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-6 mb-6 text-left">
                    <p class="text-lg font-bold text-emerald-700 mb-2">Pembayaran Sudah Berhasil</p>
                    <p class="text-sm text-emerald-600">Pembayaran berhasil. Pesanan langsung diproses dapur.</p>
                    <a href="{{ route('order.tracking', ['order' => $order->id]) }}" class="mt-4 inline-block text-sm font-bold text-amber-600 hover:text-amber-700">Lihat status pesanan</a>
                </div>
            @endif

            <a href="{{ route('customer.order.switch-cashier', ['order' => $order->order_number]) }}" class="block mt-4 text-xs font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">
                Ganti ke Pembayaran di Kasir
            </a>
        </div>

        <p class="mt-8 text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Terverifikasi • Aman • Cepat</p>
    </div>
</div>

@push('scripts')
@if(config('services.midtrans.is_production'))
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<script>
    async function verifyPaymentAndRedirect() {
        try {
            const response = await fetch('{{ route('customer.order.verify-payment', ['order' => $order->order_number]) }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                if (data.status && data.status !== 'pending_payment') {
                    window.location.href = "{{ route('customer.order.success', ['order' => $order->order_number]) }}";
                    return;
                }
            }
        } catch (error) {
            console.error('Verifikasi pembayaran gagal:', error);
        }

        window.location.href = "{{ route('order.tracking', ['order' => $order->id]) }}";
    }

    document.getElementById('pay-button').onclick = function() {
        window.snap.pay('{{ $order->snap_token }}', {
            onSuccess: function(result){
                verifyPaymentAndRedirect();
            },
            onPending: function(result){
                verifyPaymentAndRedirect();
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                window.location.href = "{{ route('order.tracking', ['order' => $order->id]) }}";
            }
        });
    };
</script>
@endpush
@endsection