@extends('layouts.customer')
@section('title', 'Checkout Pesanan')

@section('content')
    <div x-data="checkoutApp()" class="min-h-screen bg-slate-50 flex flex-col pb-28">

        <header class="bg-white border-b border-slate-100 sticky top-0 z-30 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-3.5 flex items-center gap-3">
                <a href="{{ route('customer.menu', ['session' => $session->session_token]) }}"
                    class="p-2 -ml-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="font-bold text-slate-800 text-base">Keranjang</h1>
                    <p class="text-[10px] text-slate-400 font-medium" x-text="cart.length > 0 ? totalItems + ' item dipilih' : 'Kosong'"></p>
                </div>
            </div>
        </header>

        <div class="flex-1 max-w-2xl mx-auto w-full px-4 py-5">

            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center h-64 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-5">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 mb-2">Keranjang Kosong</h2>
                    <p class="text-sm text-slate-400 mb-6">Belum ada makanan yang dipilih.</p>
                    <a href="{{ route('customer.menu', ['session' => $session->session_token]) }}"
                        class="px-6 py-3 bg-amber-500 text-white font-bold rounded-2xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25">Lihat Menu</a>
                </div>
            </template>

            <template x-if="cart.length > 0">
                <div class="space-y-4">

                    <div class="bg-white rounded-2xl px-4 py-3 shadow-sm border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $customerData['name'] }}</p>
                            <p class="text-[11px] text-slate-400">{{ $customerData['phone'] }}</p>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5 bg-emerald-50 text-emerald-700 font-bold px-3 py-1.5 rounded-xl text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                            Meja {{ $session->table->number }}
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-50 flex items-center justify-between">
                            <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                Pesanan Kamu
                            </h2>
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md" x-text="totalItems + ' item'"></span>
                        </div>

                        <div class="divide-y divide-slate-50">
                            <template x-for="(item, idx) in cart" :key="item.id">
                                <div class="px-4 py-4 flex gap-3">
                                    <div class="relative shrink-0">
                                        <img :src="item.image" class="w-16 h-16 rounded-xl object-cover bg-slate-100">
                                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-sm" x-text="item.qty"></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="font-bold text-slate-800 text-sm leading-tight truncate" x-text="item.name"></h3>
                                            <span class="font-bold text-slate-800 text-sm whitespace-nowrap" x-text="formatRp(item.price * item.qty)"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="formatRp(item.price) + '/pcs'"></p>

                                        <input type="text" x-model="item.notes" @change="saveCart()"
                                                            placeholder="Tambah catatan..."
                                                            class="w-full mt-2 px-2.5 py-1.5 text-[11px] bg-slate-50 border border-slate-100 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-500 focus:bg-white transition-colors placeholder:text-slate-300">

                                        <div class="flex items-center justify-between mt-2.5">
                                            <button @click="removeItem(idx)"
                                                class="text-[11px] text-red-400 hover:text-red-600 hover:bg-red-50 font-semibold px-2 py-1 -ml-2 rounded-lg transition-all flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                            <div class="flex items-center gap-0.5 bg-slate-100 rounded-xl p-0.5">
                                                <button @click="updateQty(idx, -1)"
                                                    class="w-8 h-8 bg-white text-slate-600 rounded-lg flex items-center justify-center font-bold text-sm shadow-sm hover:bg-slate-50 active:scale-95 transition-all">−</button>
                                                <span class="w-8 text-center text-sm font-bold text-slate-800" x-text="item.qty"></span>
                                                <button @click="updateQty(idx, 1)"
                                                    class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-sm hover:bg-amber-600 active:scale-95 transition-all">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl px-4 py-3.5 shadow-sm border border-slate-100">
                        <label class="block text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Catatan Pesanan
                        </label>
                        <textarea x-model="orderNotes" placeholder="Cth: Jangan terlalu manis, alat makan disiapkan..."
                            rows="2"
                            class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none placeholder:text-slate-300"></textarea>
                    </div>

                    <div class="bg-white rounded-2xl px-4 py-3.5 shadow-sm border border-slate-100">
                        <h2 class="text-xs font-bold text-slate-800 mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Metode Pembayaran
                        </h2>
                        <div class="space-y-2.5">

                            <label class="relative flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all duration-300 group"
                                :class="paymentMethod === 'online' ? 'border-amber-500 bg-amber-50/40' : 'border-slate-100 hover:border-amber-200'">
                                <input type="radio" x-model="paymentMethod" value="online" class="sr-only">
                                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white mr-3 shadow-sm group-hover:scale-105 transition-transform shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="block font-bold text-slate-800 text-sm">Bayar Online</span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">QRIS, e-Wallet, Transfer</span>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ml-2 shrink-0 transition-colors"
                                    :class="paymentMethod === 'online' ? 'border-amber-500' : 'border-slate-200'">
                                    <div x-show="paymentMethod === 'online'" class="w-2.5 h-2.5 bg-amber-500 rounded-full" x-transition.scale></div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all duration-300 group"
                                :class="paymentMethod === 'cashier' ? 'border-amber-500 bg-amber-50/40' : 'border-slate-100 hover:border-amber-200'">
                                <input type="radio" x-model="paymentMethod" value="cashier" class="sr-only">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center text-white mr-3 shadow-sm group-hover:scale-105 transition-transform shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 2.24-4 5s1.79 5 4 5 4-2.24 4-5-1.79-5-4-5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2M12 19v2M5 12h2M17 12h2"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="block font-bold text-slate-800 text-sm">Bayar di Kasir</span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">Tunai atau Debit/Kredit</span>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ml-2 shrink-0 transition-colors"
                                    :class="paymentMethod === 'cashier' ? 'border-amber-500' : 'border-slate-200'">
                                    <div x-show="paymentMethod === 'cashier'" class="w-2.5 h-2.5 bg-amber-500 rounded-full" x-transition.scale></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl px-4 py-3.5 shadow-sm border border-slate-100 relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-20 h-20 bg-amber-50 rounded-full blur-2xl"></div>
                        <h2 class="text-xs font-bold text-slate-800 mb-3 flex items-center gap-1.5 relative">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Ringkasan Pembayaran
                        </h2>
                        <div class="space-y-2 relative">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400" x-text="'Subtotal (' + totalItems + ' item)'"></span>
                                <span class="font-semibold text-slate-700" x-text="formatRp(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400">Pajak (11%)</span>
                                <span class="font-semibold text-slate-700" x-text="formatRp(tax)"></span>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-slate-100 mt-3 pt-3 flex justify-between items-center relative">
                            <span class="font-bold text-slate-800">Total</span>
                            <span class="text-xl font-black text-amber-600" x-text="formatRp(totalAmount)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="cart.length > 0"
            class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-100 px-4 py-3.5 safe-area-bottom shadow-[0_-8px_30px_rgba(0,0,0,0.06)]"
            x-cloak>
            <div class="max-w-2xl mx-auto flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-slate-400 font-medium">Total Pembayaran</p>
                    <p class="text-lg font-black text-amber-600 leading-tight" x-text="formatRp(totalAmount)"></p>
                </div>
                <button @click="confirmOrder()" :disabled="submitting || cart.length === 0"
                    class="shrink-0 px-6 py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black rounded-2xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-500/30 disabled:opacity-50 disabled:cursor-wait flex items-center justify-center gap-2 transform active:scale-[0.98]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-show="!submitting">Pesan</span>
                    <span x-show="submitting">Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function checkoutApp() {
                return {
                    cart: JSON.parse(localStorage.getItem('cart_{{ $session->session_token }}') || '[]'),
                    orderNotes: '',
                    paymentMethod: 'online',
                    submitting: false,

                    get totalItems() { return this.cart.reduce((s, i) => s + i.qty, 0); },
                    get subtotal() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },
                    get tax() { return Math.round(this.subtotal * 0.11); },
                    get totalAmount() { return this.subtotal + this.tax; },

                    formatRp(n) { return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },

                    async updateQty(idx, delta) {
                        if (this.cart[idx].qty + delta <= 0) {
                            if (this.cart.length === 1) {
                                const result = await Swal.fire({
                                    title: 'Keranjang Bakal Kosong',
                                    text: 'Ini barang terakhirmu, kamu mau kosongin keranjangnya?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#64748b',
                                    confirmButtonText: 'Ya, kosongin',
                                    cancelButtonText: 'Batal',
                                    customClass: {
                                        popup: 'rounded-2xl',
                                        title: 'font-bold text-slate-800',
                                    }
                                });

                                if (result.isConfirmed) {
                                    this.cart.splice(idx, 1);
                                    this.saveCart();
                                }
                            } else {
                                this.cart.splice(idx, 1);
                                this.saveCart();
                            }
                        } else {
                            this.cart[idx].qty += delta;
                            this.saveCart();
                        }
                    },

                    async removeItem(idx) {
                        if (this.cart.length === 1) {
                            const result = await Swal.fire({
                                title: 'Keranjang Bakal Kosong',
                                text: 'Ini barang terakhirmu, kamu mau kosongin keranjangnya?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Ya, kosongin',
                                cancelButtonText: 'Batal',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    title: 'font-bold text-slate-800',
                                }
                            });

                            if (result.isConfirmed) {
                                this.cart.splice(idx, 1);
                                this.saveCart();
                            }
                        } else {
                            this.cart.splice(idx, 1);
                            this.saveCart();
                        }
                    },

                    saveCart() {
                        localStorage.setItem('cart_{{ $session->session_token }}', JSON.stringify(this.cart));
                    },

                    async confirmOrder() {
                        if (this.cart.length === 0 || this.submitting) return;

                        const result = await Swal.fire({
                            title: 'Konfirmasi Pesanan',
                            html: `Apakah pesanan Anda sudah <strong>sesuai</strong>?<br>Jika ragu, klik Batal untuk kembali ke keranjang.<div class="mt-4 p-4 rounded-2xl bg-slate-50 text-left text-sm text-slate-700"><span class="block text-[11px] uppercase tracking-[0.2em] text-slate-400 mb-2">Total Ringkasan</span><strong class="text-lg text-slate-900">${this.formatRp(this.totalAmount)}</strong></div>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Sudah Sesuai',
                            cancelButtonText: 'Ragu, Cek Lagi',
                            confirmButtonColor: '#f59e0b',
                            cancelButtonColor: '#64748b',
                            customClass: {
                                popup: 'rounded-2xl',
                                title: 'font-bold text-slate-800',
                                htmlContainer: 'text-slate-600'
                            }
                        });

                        if (result.isConfirmed) {
                            this.submitOrder();
                        }
                    },

                    async submitOrder() {
                        if (this.cart.length === 0 || this.submitting) return;
                        this.submitting = true;
                        try {
                            // Refresh CSRF token
                            const tokenResponse = await fetch('/csrf-token', {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            if (tokenResponse.ok) {
                                const tokenData = await tokenResponse.json();
                                document.querySelector('meta[name="csrf-token"]').content = tokenData.token;
                            }

                            const response = await fetch('{{ route('customer.checkout.process') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    session_token: '{{ $session->session_token }}',
                                    items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty, notes: i.notes || null })),
                                    notes: this.orderNotes || null,
                                    payment_method: this.paymentMethod
                                })
                            });

                            if (!response.ok) {
                                if (response.status === 419) {
                                    showToast('Session expired. Silakan refresh halaman dan coba lagi.', 'error');
                                    this.submitting = false;
                                    return;
                                }
                                const errorData = await response.json();
                                showToast(errorData.message || 'Gagal mengirim pesanan', 'error');
                                this.submitting = false;
                                return;
                            }

                            const data = await response.json();

                            if (data.success) {

                                localStorage.removeItem('cart_{{ $session->session_token }}');
                                this.cart = [];

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