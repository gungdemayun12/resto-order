@extends('layouts.customer')
@section('title', 'Menu Utama')

@section('content')
<div x-data="menuListApp()" class="min-h-screen bg-slate-50 pb-32">

    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-100 safe-area-top shadow-sm">
        <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between gap-3 relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-amber-400/10 rounded-full blur-2xl"></div>
            <div class="relative flex items-center gap-3 min-w-0 flex-1 z-10">
                <x-resto-logo size="nav" class="flex-shrink-0" />
                <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                        {{ session('customer_name') }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        Meja {{ $table->number }}
                    </span>
                </div>
            </div>
            <a href="{{ route('customer.checkout', ['session' => $sessionToken]) }}" class="relative p-3 bg-white border border-slate-100 shadow-sm rounded-2xl text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition-all active:scale-95 group">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <span x-show="totalItems > 0" x-text="totalItems" class="absolute -top-2.5 -right-2.5 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center badge-pulse shadow-md border-2 border-white" x-cloak></span>
            </a>
        </div>
    </header>

    <div class="sticky top-[73px] z-30 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
        <div class="max-w-lg mx-auto px-4 py-3 flex gap-2 overflow-x-auto no-scrollbar">
            <button @click="activeCategory = 'all'" :class="activeCategory === 'all' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-5 py-2.5 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">Semua Menu</button>
            @foreach($categories as $cat)
            <button @click="activeCategory = '{{ $cat->id }}'" :class="activeCategory === '{{ $cat->id }}' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-5 py-2.5 rounded-2xl text-sm font-semibold whitespace-nowrap transition-all duration-300">{{ $cat->icon }} {{ $cat->name }}</button>
            @endforeach
        </div>
        
        <div class="max-w-lg mx-auto px-4 pb-3">
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari menu kesukaanmu..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all placeholder-slate-400">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button x-show="search.length > 0" @click="search = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600" x-cloak>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-lg mx-auto px-4 py-6">
        <div class="grid grid-cols-2 gap-4">
            @foreach($menuItems as $item)
            <div x-show="(activeCategory === 'all' || activeCategory === '{{ $item->category_id }}') && '{{ strtolower(addslashes($item->name)) }}'.includes(search.toLowerCase())" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.06)] border border-slate-100 overflow-hidden hover:shadow-[0_10px_30px_rgba(245,158,11,0.15)] hover:border-amber-200 hover:-translate-y-1.5 transition-all duration-300 group flex flex-col relative z-10">
                <div class="relative aspect-square bg-slate-100 overflow-hidden">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    @if($item->labels && count($item->labels) > 0)
                    <div class="absolute top-2 left-2 flex flex-col gap-1 items-start">
                        @foreach(array_slice($item->labels, 0, 2) as $label)
                        @php $lc = ['best_seller'=>'bg-amber-500','recommended'=>'bg-blue-500','vegetarian'=>'bg-green-500','spicy'=>'bg-red-500','halal'=>'bg-emerald-600']; @endphp
                        <span class="px-2.5 py-1 text-xs font-semibold text-white rounded-xl shadow-lg {{ $lc[$label] ?? 'bg-slate-500' }} uppercase leading-none border border-white/30 ring-1 ring-white/20">{{ ucfirst(str_replace('_',' ',$label)) }}</span>
                        @endforeach
                    </div>
                    @endif
                    <span class="absolute bottom-2 right-2 px-1.5 py-0.5 bg-black/60 text-white text-[10px] rounded-full">⏱ {{ $item->estimated_time }}m</span>
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 leading-tight line-clamp-1">{{ $item->name }}</h3>
                    <p class="text-[11px] text-slate-500 line-clamp-2 mt-1.5 flex-1">{{ $item->description }}</p>
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-sm font-bold text-amber-600">{{ $item->formatted_price }}</p>
                        
                        <div class="flex items-center gap-2">
                            <button x-show="getItemQty({{ $item->id }}) === 0" @click="addToCart({{ json_encode(['id'=>$item->id,'name'=>$item->name,'price'=>$item->price,'image'=>$item->image_url]) }})" class="w-9 h-9 bg-amber-500 text-white rounded-xl flex items-center justify-center hover:bg-amber-600 transition-all shadow-md active:scale-90 shadow-amber-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                            </button>
                            
                            <div x-show="getItemQty({{ $item->id }}) > 0" class="flex items-center gap-2 bg-slate-100/80 rounded-xl p-1 shadow-inner border border-slate-200/50" x-cloak>
                                <button @click="updateQty({{ $item->id }}, -1)" class="w-7 h-7 bg-white text-slate-600 rounded-lg flex items-center justify-center font-bold shadow-sm active:scale-90 transition-transform">-</button>
                                <span class="text-xs font-bold text-slate-800 w-4 text-center" x-text="getItemQty({{ $item->id }})"></span>
                                <button @click="updateQty({{ $item->id }}, 1)" class="w-7 h-7 bg-amber-500 text-white rounded-lg flex items-center justify-center font-bold shadow-sm active:scale-90 transition-transform shadow-amber-500/30">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div id="floating-cart-bar" x-show="totalItems > 0" x-cloak
         x-transition:enter="transition transform ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         class="fixed bottom-0 left-0 right-0 z-[100] safe-area-bottom pb-8 px-4 bg-gradient-to-t from-slate-50 via-slate-50/80 to-transparent pt-8">
        <div class="max-w-lg mx-auto">
            <a href="{{ route('customer.checkout', ['session' => $sessionToken]) }}" class="block w-full bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-xl transition-all active:scale-[0.98] border border-slate-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-500 text-slate-900 text-[10px] font-black px-2 py-0.5 rounded-lg" x-text="totalItems"></span>
                        <span class="text-xs font-bold uppercase tracking-wide">Item Terpilih</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-amber-400" x-text="formatRp(totalPrice)"></span>
                        <span class="text-xs font-bold uppercase tracking-tight opacity-80">Checkout →</span>
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
        activeCategory: 'all',
        search: '',
        cart: JSON.parse(localStorage.getItem('cart_{{ $sessionToken }}') || '[]'),

        get totalItems() { return this.cart.reduce((s, i) => s + i.qty, 0); },
        get totalPrice() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },

        formatRp(n) { return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },

        getItemQty(id) {
            const item = this.cart.find(i => i.id === id);
            return item ? item.qty : 0;
        },

        addToCart(item) {
            this.cart.push({ ...item, qty: 1, notes: '' });
            this.saveCart();
            showToast(item.name + ' ditambahkan', 'cart');
        },

        updateQty(id, delta) {
            const idx = this.cart.findIndex(i => i.id === id);
            if (idx > -1) {
                this.cart[idx].qty += delta;
                if (this.cart[idx].qty <= 0) this.cart.splice(idx, 1);
                this.saveCart();
            }
        },

        saveCart() { localStorage.setItem('cart_{{ $sessionToken }}', JSON.stringify(this.cart)); },
    };
}
</script>
@endpush
@endsection

