@extends('layouts.customer')
@section('title', 'Checkout Pesanan')

@section('content')
<div x-data="checkoutApp()" class="min-h-screen bg-slate-50 flex flex-col pb-24">

    
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
            <a href="{{ route('customer.menu', ['session' => $session->session_token]) }}" class="p-2 -ml-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="font-bold text-slate-800 text-lg">Keranjang Anda</h1>
        </div>
    </header>

    <div class="flex-1 max-w-lg mx-auto w-full p-4">
        
        <template x-if="cart.length === 0">
            <div class="flex flex-col items-center justify-center h-64 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-2">Keranjang Kosong</h2>
                <p class="text-sm text-slate-500 mb-6">Belum ada makanan yang dipilih.</p>
                <a href="{{ route('customer.menu', ['session' => $session->session_token]) }}" class="px-6 py-3 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700">Lihat Menu</a>
            </div>
        </template>

        
        <template x-if="cart.length > 0">
            <div class="space-y-6">
                
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-slate-500">Pemesan</p>
                        <p class="font-bold text-slate-800">{{ session('customer_name') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Meja</p>
                        <div class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 text-amber-700 font-bold rounded-lg">{{ $table->number }}</div>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-4 border-b border-slate-100">
                        <h2 class="font-bold text-slate-800">Daftar Pesanan</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="p-4 flex gap-4">
                                <img :src="item.image" class="w-20 h-20 rounded-xl object-cover bg-slate-100">
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800 text-sm mb-1" x-text="item.name"></h3>
                                    <p class="text-amber-600 font-semibold text-sm mb-2" x-text="formatRp(item.price)"></p>
                                    
                                    
                                    <div class="mb-3">
                                        <input type="text" x-model="item.notes" @change="saveCart()" placeholder="Tambah catatan (opsional)..." 
                                               class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-500 focus:bg-white transition-colors">
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <button @click="removeItem(idx)" class="text-xs font-semibold text-red-500 hover:text-red-700">Hapus</button>
                                        <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-1 border border-slate-100">
                                            <button @click="updateQty(idx, -1)" class="w-6 h-6 bg-white text-slate-600 rounded shadow-sm flex items-center justify-center font-bold">-</button>
                                            <span class="text-sm font-bold text-slate-800 w-4 text-center" x-text="item.qty"></span>
                                            <button @click="updateQty(idx, 1)" class="w-6 h-6 bg-white text-slate-600 rounded shadow-sm flex items-center justify-center font-bold">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                
                <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                    <label class="block text-sm font-bold text-slate-800 mb-2">Catatan Pesanan</label>
                    <textarea x-model="orderNotes" placeholder="Cth: Jangan terlalu manis, alat makan disiapkan..." rows="2" 
                              class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none"></textarea>
                </div>

                
                <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 space-y-4">
                    <h2 class="font-bold text-slate-800 text-base">Metode Pembayaran</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <label class="relative flex items-center p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 group"
                               :class="paymentMethod === 'online' ? 'border-amber-500 bg-amber-50/30 shadow-lg shadow-amber-500/10' : 'border-slate-200 bg-white hover:border-amber-200 hover:bg-amber-50/10'">
                            <input type="radio" x-model="paymentMethod" value="online" class="sr-only">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white mr-4 shadow-md group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div class="flex-1">
                                <span class="block font-bold text-slate-800 text-sm">QRIS / e-Wallet / Transfer</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">GoPay, OVO, Dana, ShopeePay, Virtual Account</span>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ml-2 transition-colors" :class="paymentMethod === 'online' ? 'border-amber-500' : 'border-slate-300'">
                                <div x-show="paymentMethod === 'online'" class="w-2.5 h-2.5 bg-amber-500 rounded-full" x-transition.scale></div>
                            </div>
                        </label>
                        
                        
                        <label class="relative flex items-center p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 group"
                               :class="paymentMethod === 'cashier' ? 'border-amber-500 bg-amber-50/30 shadow-lg shadow-amber-500/10' : 'border-slate-200 bg-white hover:border-amber-200 hover:bg-amber-50/10'">
                            <input type="radio" x-model="paymentMethod" value="cashier" class="sr-only">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center text-white mr-4 shadow-md group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <span class="block font-bold text-slate-800 text-sm">Bayar di Kasir</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Tunai atau Debit/Kredit Card di kasir</span>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ml-2 transition-colors" :class="paymentMethod === 'cashier' ? 'border-amber-500' : 'border-slate-300'">
                                <div x-show="paymentMethod === 'cashier'" class="w-2.5 h-2.5 bg-amber-500 rounded-full" x-transition.scale></div>
                            </div>
                        </label>
                    </div>
                </div>

                
                <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 space-y-3 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full blur-2xl"></div>
                    <h2 class="font-bold text-slate-800 mb-3 relative">Rincian Pembayaran</h2>
                    <div class="flex justify-between text-sm relative">
                        <span class="text-slate-500" x-text="'Subtotal (' + totalItems + ' item)'"></span>
                        <span class="font-medium text-slate-800" x-text="formatRp(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm relative">
                        <span class="text-slate-500">Pajak (11%)</span>
                        <span class="font-medium text-slate-800" x-text="formatRp(tax)"></span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-4 flex justify-between items-center mt-2 relative">
                        <span class="font-bold text-slate-800">Total Harga</span>
                        <span class="text-2xl font-bold text-amber-600 drop-shadow-sm" x-text="formatRp(totalAmount)"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    
    <div x-show="cart.length > 0" class="fixed bottom-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md border-t border-slate-100 px-4 py-4 safe-area-bottom shadow-[0_-10px_30px_rgba(0,0,0,0.05)]" x-cloak>
        <div class="max-w-lg mx-auto">
            <button @click="submitOrder()" :disabled="submitting" 
                    class="group w-full py-4 md:py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black text-base md:text-lg uppercase tracking-widest rounded-2xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-500/30 disabled:opacity-50 disabled:cursor-wait flex items-center justify-center gap-3 transform active:scale-[0.98] hover:scale-[1.02]">
                <span x-show="!submitting" class="flex items-center gap-3">
                    Konfirmasi Pesanan
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> 
                    Memproses...
                </span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkoutApp() {
    return {
        cart: JSON.parse(localStorage.getItem('cart_{{ $session->session_token }}') || '[]'),
        sessionToken: '{{ $session->session_token }}',
        orderNotes: '',
        paymentMethod: 'online',
        submitting: false,

        get totalItems() { return this.cart.reduce((s, i) => s + i.qty, 0); },
        get subtotal() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },
        get tax() { return Math.round(this.subtotal * 0.11); },
        get totalAmount() { return this.subtotal + this.tax; },

        formatRp(n) { return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },

        updateQty(idx, delta) {
            this.cart[idx].qty += delta;
            if (this.cart[idx].qty <= 0) {
                this.cart.splice(idx, 1);
            }
            this.saveCart();
        },

        removeItem(idx) {
            this.cart.splice(idx, 1);
            this.saveCart();
        },

        saveCart() { 
            localStorage.setItem('cart_{{ $session->session_token }}', JSON.stringify(this.cart)); 
        },

        async submitOrder() {
            if (this.cart.length === 0 || this.submitting) return;
            this.submitting = true;
            try {
                const response = await fetch('{{ route('customer.checkout.process') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({
                        session_token: this.sessionToken,
                        items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty, notes: i.notes || null })),
                        notes: this.orderNotes || null,
                        payment_method: this.paymentMethod
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    localStorage.removeItem('cart_{{ $session->session_token }}');
                    window.location.href = data.redirect;
                } else { 
                    showToast(data.message || 'Gagal mengirim pesanan', 'error'); 
                    this.submitting = false;
                }
            } catch (e) { 
                showToast('Terjadi kesalahan koneksi. Coba lagi.', 'error'); 
                this.submitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection

