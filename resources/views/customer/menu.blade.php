@extends('layouts.customer')
@section('title', 'Menu Utama')

@section('content')
    <div x-data="menuListApp()" class="min-h-screen bg-slate-50 pb-32">

            <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-slate-100 safe-area-top shadow-sm">
                <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <x-resto-logo size="menu-header" class="flex-shrink-0" />
                        <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                            <span
                                class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                                <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                                {{ $customerData['name'] }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                Meja {{ $table->number }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout', ['session' => $session->session_token]) }}" id="cart-button-top"
                        class="relative flex items-center justify-center w-12 h-12 bg-white border border-slate-200 shadow-lg shadow-slate-200/50 rounded-2xl text-amber-600 hover:bg-amber-50 hover:border-amber-300 transition-all active:scale-90 group z-50">
                        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="totalItems > 0"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-lg border-2 border-white animate-bounce"
                            x-text="totalItems" x-cloak></span>
                    </a>
                </div>
            </header>

            <div class="sticky top-[72px] z-30 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm">

                <div class="max-w-lg mx-auto px-4 pt-3 pb-1.5">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" x-model="search" @input.debounce.300ms="startLoading()"
                            placeholder="Cari menu kesukaanmu..."
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-100 border-none rounded-xl text-sm font-semibold focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all placeholder-slate-400 text-slate-700 shadow-inner">

                        <button x-show="search.length > 0" @click="search = ''; startLoading()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center bg-slate-200 text-slate-500 rounded-full hover:bg-slate-300 transition-colors"
                            x-cloak>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="max-w-lg mx-auto px-4 py-2 flex gap-2 overflow-x-auto no-scrollbar">
                    <button type="button" @click="selectCategory('all')"
                        :class="activeCategory === 'all' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/25 ring-2 ring-amber-500/10' : 'bg-white text-slate-500 border border-slate-200 hover:border-amber-300 hover:text-amber-600 shadow-sm'"
                        class="min-w-fit px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.14em] whitespace-nowrap transition-all duration-200">Semua</button>
                    @foreach($categories as $cat)
                        <button type="button" @click="selectCategory('{{ $cat->id }}')"
                            :class="activeCategory === '{{ $cat->id }}' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/25 ring-2 ring-amber-500/10' : 'bg-white text-slate-500 border border-slate-200 hover:border-amber-300 hover:text-amber-600 shadow-sm'"
                            class="min-w-fit px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.14em] whitespace-nowrap transition-all duration-200">
                            {{ $cat->icon }} {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if($activeOrder)
                @php
                    $activeOrderRoute = match ($activeOrder->status) {
                        'pending_confirmation' => route('customer.order.barcode', ['order' => $activeOrder->order_number]),
                        'pending_payment' => route('customer.payment', ['order' => $activeOrder->order_number]),
                        default => route('order.tracking', ['order' => $activeOrder->id]),
                    };
                    $orderTheme = $activeOrder->status_theme;
                @endphp
                <div class="max-w-lg mx-auto px-5 mt-4">
                    <a href="{{ $activeOrderRoute }}"
                        class="flex items-center gap-4 bg-white border {{ $orderTheme['landing_border'] }} p-4 rounded-3xl shadow-xl {{ $orderTheme['landing_shadow'] }} {{ $orderTheme['landing_hover_border'] }} transition-all group overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-2 opacity-5">
                            <x-order-status-icon :icon="$orderTheme['icon']" class="w-20 h-20 {{ $orderTheme['landing_accent'] }}" />
                        </div>
                        <div
                            class="w-12 h-12 {{ $orderTheme['landing_icon_bg'] }} rounded-2xl flex items-center justify-center text-white shadow-lg {{ $orderTheme['landing_icon_shadow'] }} group-hover:rotate-12 transition-transform">
                            <x-order-status-icon :icon="$orderTheme['icon']" class="w-7 h-7 text-white animate-pulse" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] font-black {{ $orderTheme['landing_accent'] }} uppercase tracking-[0.2em]">
                                    Pesanan Berlangsung</p>
                                <span class="text-[10px] font-bold text-slate-400">#{{ $activeOrder->order_number }}</span>
                            </div>
                            <p class="text-sm font-black text-slate-800 mt-0.5">
                                Status: <span
                                    class="{{ $orderTheme['landing_status'] }} uppercase">{{ $activeOrder->status_label }}</span>
                            </p>
                        </div>
                        <div
                            class="p-2 text-slate-300 {{ $orderTheme['landing_hover_arrow'] }} group-hover:translate-x-1 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                </div>
            @endif

            <div class="max-w-lg mx-auto px-5 py-5">

                <template x-if="isLoading">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        @for($i = 0; $i < 6; $i++)
                            <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-100">
                                <div class="aspect-[4/3] rounded-xl skeleton mb-3"></div>
                                <div class="h-4 w-3/4 skeleton rounded-full mb-2"></div>
                                <div class="h-3 w-1/2 skeleton rounded-full mb-3"></div>
                                <div class="flex justify-between items-center">
                                    <div class="h-5 w-1/3 skeleton rounded-full"></div>
                                    <div class="h-9 w-9 skeleton rounded-xl"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </template>

                <div x-show="!isLoading" x-cloak>
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 menu-grid">
                        @foreach($menuItems as $item)
                            <div x-show="'{{ strtolower(addslashes($item->name)) }}'.includes(search.toLowerCase())"
                                x-transition:enter="transition ease-out duration-400"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="menu-item bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition-all duration-300 group relative {{ !$item->is_available ? 'grayscale opacity-60' : 'hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1 hover:border-amber-200 active:scale-[0.98]' }}">

                                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy"
                                        class="w-full h-full object-cover {{ $item->is_available ? 'group-hover:scale-105' : '' }} transition-transform duration-500">

                                    @if($item->is_available && $item->labels && count($item->labels) > 0)
                                        <div class="absolute top-2 left-2 flex gap-1">
                                            @foreach(array_slice($item->labels, 0, 2) as $label)
                                                @php $lc = ['best_seller' => 'bg-amber-500', 'recommended' => 'bg-blue-500', 'vegetarian' => 'bg-green-500', 'spicy' => 'bg-red-500', 'halal' => 'bg-emerald-600']; @endphp
                                                <span class="px-2 py-0.5 text-[9px] font-bold text-white rounded-lg shadow-sm {{ $lc[$label] ?? 'bg-slate-500' }} uppercase tracking-wider">{{ str_replace('_', ' ', $label) }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!$item->is_available)
                                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center">
                                            <div style="transform:rotate(-12deg);">
                                                <div class="border-2 border-red-500 rounded px-3 py-1" style="box-shadow:0 0 0 1px #ef4444 inset,0 4px 12px rgba(239,68,68,0.4);">
                                                    <span class="text-red-500 font-black text-xs uppercase tracking-[0.25em]" style="text-shadow:0 1px 0 rgba(0,0,0,0.3);">Habis</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/60 backdrop-blur-sm text-white text-[9px] font-bold rounded-lg">
                                            ⏱ {{ $item->estimated_time }}m
                                        </span>
                                    @endif
                                </div>

                                <div class="p-3">
                                    <h3 class="text-[13px] font-bold {{ $item->is_available ? 'text-slate-800' : 'text-slate-400' }} leading-snug line-clamp-1 mb-0.5">{{ $item->name }}</h3>
                                    <p class="text-[11px] text-slate-400 line-clamp-1 mb-3 leading-relaxed">{{ $item->description }}</p>

                                    <div class="flex items-center justify-between">
                                        <p class="text-[13px] font-black {{ $item->is_available ? 'text-amber-600' : 'text-slate-400' }}">{{ $item->formatted_price }}</p>

                                        @if(!$item->is_available)
                                            <div class="w-8 h-8 bg-slate-100 text-slate-300 rounded-xl flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <button x-show="getItemQty({{ $item->id }}) === 0"
                                                    @click="addToCart({{ json_encode(['id' => $item->id, 'name' => $item->name, 'price' => $item->price, 'image' => $item->image_url]) }})"
                                                    class="w-8 h-8 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-amber-500 hover:shadow-lg hover:shadow-amber-500/30 transition-all active:scale-75">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12m6-6H6"/></svg>
                                                </button>

                                                <div x-show="getItemQty({{ $item->id }}) > 0"
                                                    class="flex items-center bg-slate-100 rounded-xl overflow-hidden shadow-inner" x-cloak>
                                                    <button @click="updateQty({{ $item->id }}, -1)"
                                                        class="w-7 h-7 bg-white text-slate-700 flex items-center justify-center font-black text-sm active:scale-90 transition-all">−</button>
                                                    <span class="text-[11px] font-black text-slate-800 w-5 text-center"
                                                        x-text="getItemQty({{ $item->id }})"></span>
                                                    <button @click="updateQty({{ $item->id }}, 1)"
                                                        class="w-7 h-7 bg-amber-500 text-white flex items-center justify-center font-black text-sm active:scale-90 transition-all shadow-amber-500/20">+</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($menuItems->hasPages())
                    <div class="mt-8 flex justify-center" x-show="search.length === 0" x-cloak>
                        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-4 inline-flex items-center gap-2">
                            @if ($menuItems->onFirstPage())
                                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">
                                    ←
                                </span>
                            @else
                                <a href="{{ $menuItems->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                                    ←
                                </a>
                            @endif

                            <div class="flex items-center gap-1">
                                @foreach ($menuItems->getUrlRange(1, $menuItems->lastPage()) as $page => $url)
                                    @if ($page == $menuItems->currentPage())
                                        <span class="px-4 py-2 bg-amber-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-amber-600/20">
                                            {{ $page }}
                                        </span>
                                    @elseif ($page == 1 || $page == $menuItems->lastPage() || abs($page - $menuItems->currentPage()) <= 1)
                                        <a href="{{ $url }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                                            {{ $page }}
                                        </a>
                                    @elseif (abs($page - $menuItems->currentPage()) == 2)
                                        <span class="px-2 text-slate-400">...</span>
                                    @endif
                                @endforeach
                            </div>

                            @if ($menuItems->hasMorePages())
                                <a href="{{ $menuItems->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">
                                    →
                                </a>
                            @else
                                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">
                                    →
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div x-show="nothingFound()" class="py-20 text-center" x-cloak>
                        <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-black text-slate-800"
                            x-text="search.length > 0 ? 'Menu tidak ditemukan' : 'Menu tidak tersedia'"></h4>
                        <p class="text-sm text-slate-500 mt-2"
                            x-text="search.length > 0 ? 'Coba kata kunci lain atau pilih kategori berbeda' : 'Menu belum tersedia di kategori ini'">
                        </p>
                    </div>
                </div>
            </div>

            <div id="floating-cart-bar" x-show="totalItems > 0" x-cloak
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                class="fixed bottom-0 left-0 right-0 z-[100] px-4 pb-6 pt-2">
                <div class="max-w-lg mx-auto">
                    <a href="{{ route('customer.checkout', ['session' => $session->session_token]) }}"
                        class="group block w-full bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-xl transition-all active:scale-[0.98] border border-slate-700">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <span
                                    class="bg-amber-500 text-slate-900 text-[10px] font-black px-2 py-0.5 rounded-lg flex-shrink-0"
                                    x-text="totalItems"></span>
                                <span class="text-xs font-bold uppercase tracking-wide truncate">Item Terpilih</span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-xs font-black text-amber-400" x-text="formatRp(totalPrice)"></span>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="text-xs font-bold uppercase tracking-tight opacity-80 whitespace-nowrap">Lanjutkan →</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function menuListApp() {
                    return {
                        activeCategory: @json((string) $activeCategory),
                        search: '',
                        isLoading: false,
                        cart: JSON.parse(localStorage.getItem('cart_{{ $session->session_token }}') || '[]'),

                        init() {

                        },

                        selectCategory(categoryId) {
                            const url = new URL(window.location.href);
                            url.searchParams.set('session', @json($session->session_token));
                            url.searchParams.delete('page');

                            if (categoryId === 'all') {
                                url.searchParams.delete('category');
                            } else {
                                url.searchParams.set('category', categoryId);
                            }

                            window.location.href = url.toString();
                        },

                        startLoading() {
                            this.isLoading = true;
                            setTimeout(() => {
                                this.isLoading = false;
                            }, 800);
                        },

                        get totalItems() { return this.cart.reduce((s, i) => s + i.qty, 0); },
                        get totalPrice() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },

                        formatRp(n) { return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },

                        nothingFound() {
                            return !document.querySelectorAll('.menu-grid > .menu-item:not([style*="display: none"])').length;
                        },

                        getItemQty(id) {
                            const item = this.cart.find(i => i.id === id);
                            return item ? item.qty : 0;
                        },

                        async addToCart(item) {
                            try {
                                const response = await fetch('{{ route('customer.cart.add') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        session_token: '{{ $session->session_token }}',
                                        menu_item_id: item.id,
                                        quantity: 1,
                                        notes: ''
                                    })
                                });

                                const result = await response.json();
                                if (result.success) {
                                    this.cart.push({ ...item, qty: 1, notes: '' });
                                    this.saveCart();
                                    showToast(item.name + ' ditambahkan', 'cart');
                                }
                            } catch (e) {
                                showToast('Gagal menambah ke keranjang', 'error');
                            }
                        },

                        async updateQty(id, delta) {
                            const idx = this.cart.findIndex(i => i.id === id);
                            if (idx > -1) {
                                const newQty = this.cart[idx].qty + delta;

                                if (newQty <= 0) {
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

                                        if (!result.isConfirmed) {
                                            return;
                                        }
                                    }
                                }

                                try {
                                    const response = await fetch('{{ route('customer.cart.update') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            session_token: '{{ $session->session_token }}',
                                            menu_item_id: id,
                                            quantity: newQty
                                        })
                                    });

                                    this.cart[idx].qty = newQty;
                                    if (this.cart[idx].qty <= 0) this.cart.splice(idx, 1);
                                    this.saveCart();
                                } catch (e) {
                                    showToast('Gagal update keranjang', 'error');
                                }
                            }
                        },

                        saveCart() {
                            localStorage.setItem('cart_{{ $session->session_token }}', JSON.stringify(this.cart));
                        },
                    };
                }
            </script>
        @endpush
@endsection